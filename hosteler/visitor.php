<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require('inc/db.php');

// Check login
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];
$error_message = '';
$success_message = '';

// Get hosteler ID from hostelers table
$hosteler_id = null;
try {
    $stmt = $conn->prepare("SELECT id FROM hostelers WHERE username = ?");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("s", $username);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hosteler_id = $row['id'];
    } else {
        throw new Exception("User not found in hostelers table");
    }
} catch (Exception $e) {
    error_log("Error fetching user: " . $e->getMessage());
    die("Error fetching user: " . $e->getMessage());
}

// Check if fee is confirmed
$fee_confirmed = false;
try {
    $stmt = $conn->prepare("SELECT status, confirmed_date FROM fee 
                           WHERE hid = ? AND status = 'confirmed' 
                           ORDER BY feeid DESC LIMIT 1");
    $stmt->bind_param("i", $hosteler_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $fee_confirmed = ($row['status'] === 'confirmed' && !empty($row['confirmed_date']));
    }
} catch (Exception $e) {
    error_log("Error checking fee status: " . $e->getMessage());
}

// Get current active booking
$active_booking = null;
try {
    $query = "SELECT b.*, r.rid, r.rprice 
              FROM booking b 
              JOIN room r ON b.rno = r.rno 
              WHERE b.id = ? AND b.bstatus = 'confirmed' 
              AND b.check_out >= CURRENT_DATE()
              ORDER BY b.bookingdate DESC LIMIT 1";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $hosteler_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $active_booking = $result->fetch_assoc();
} catch (Exception $e) {
    error_log("Error fetching active booking: " . $e->getMessage());
    die("Error fetching active booking: " . $e->getMessage());
}

// Handle visitor form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    if (!$active_booking) {
        $error_message = "You need an active booking to request visitors.";
    } elseif (!$fee_confirmed) {
        $error_message = "Your hostel fee must be confirmed before requesting visitors.";
    } else {
        try {
            $conn->begin_transaction();

            $vname = $_POST['vname'];
            $relation = $_POST['relation'];
            $reason = $_POST['reason'];
            $days = ($reason === 'stay') ? (int)$_POST['days'] : 1;
            $requires_voucher = ($reason === 'stay') ? 1 : 0;
            
            // Calculate fee for stay requests
            $fee = ($reason === 'stay') ? ($active_booking['rprice'] / 2) * $days : 0;

            // Handle voucher upload for stay requests
            $voucher_filename = null;
            if ($requires_voucher && isset($_FILES['voucher'])) {
                $file = $_FILES['voucher'];
                $allowed_types = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
                $max_size = 5 * 1024 * 1024; // 5MB

                if ($file['error'] !== UPLOAD_ERR_OK) {
                    throw new Exception('Upload failed with error code: ' . $file['error']);
                }

                if ($file['size'] > $max_size) {
                    throw new Exception('File size exceeds limit of 5MB');
                }

                $mime_type = mime_content_type($file['tmp_name']);
                if (!in_array($mime_type, $allowed_types)) {
                    throw new Exception('Invalid file type. Only PDF, JPG, and PNG files are allowed');
                }

                // Setup upload directory
                $upload_dir = 'uploads/visitorVouchers/';
                if (!is_dir($upload_dir)) {
                    if (!@mkdir($upload_dir, 0777, true)) {
                        throw new Exception('Failed to create vouchers directory');
                    }
                }

                // Generate filename
                $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $voucher_filename = uniqid('visitor_voucher_') . '_' . date('Ymd_His') . '.' . $file_extension;
                $destination = $upload_dir . $voucher_filename;

                if (!@move_uploaded_file($file['tmp_name'], $destination)) {
                    throw new Exception('Failed to move uploaded file');
                }
            }

            // Insert visitor request
            $stmt = $conn->prepare("INSERT INTO visitorform (vname, relation, reason, days, fee, 
                                  hid, rid, bid, seid, sid, status, requires_voucher, voucher, 
                                  voucher_status, after_fee_confirmed) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, 0, ?, ?, ?, ?, ?)");
            
            $status = 'pending';
            $voucher_status = $requires_voucher ? 'pending' : null;
            $after_fee_confirmed = 1;
            
            $stmt->bind_param("sssiiiiisssis", $vname, $relation, $reason, $days, $fee, 
                            $hosteler_id, $active_booking['rid'], $active_booking['bid'],
                            $status, $requires_voucher, $voucher_filename, $voucher_status,
                            $after_fee_confirmed);
            
            if ($stmt->execute()) {
                $conn->commit();
                $success_message = "Visitor request submitted successfully.";
            } else {
                throw new Exception("Error submitting visitor request");
            }
        } catch (Exception $e) {
            $conn->rollback();
            $error_message = "Error: " . $e->getMessage();
            error_log("Error in visitor request: " . $e->getMessage());
        }
    }
}


// Get active and pending visitor requests with voucher information
$active_visitors = [];
$pending_visitors = [];
try {
    if ($active_booking) {
        $stmt = $conn->prepare("SELECT v.*, 
                               CASE 
                                   WHEN v.reason = 'stay' THEN 'View Voucher'
                                   ELSE ''
                               END as view_voucher_text,
                               v.confirm_date
                               FROM visitorform v
                               WHERE v.hid = ? AND v.bid = ? AND v.status IN ('accepted', 'pending')
                               ORDER BY v.vid DESC");
        $stmt->bind_param("ii", $hosteler_id, $active_booking['bid']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($visitor = $result->fetch_assoc()) {
            if ($visitor['status'] === 'accepted') {
                $active_visitors[] = $visitor;
            } else {
                $pending_visitors[] = $visitor;
            }
        }
    }
} catch (Exception $e) {
    error_log("Error fetching visitor requests: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .sidebar {
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            background-color: #f8f9fa;
            padding: 20px;
            border-right: 1px solid #dee2e6;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        
        .visitor-card {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .visitor-form {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 4px;
            font-weight: 500;
            font-size: 0.9rem;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-accepted {
            background-color: #d4edda;
            color: #155724;
        }
        
        .table th {
            background-color: #f8f9fa;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }

        .voucher-preview {
            max-width: 100%;
            max-height: 300px;
            object-fit: contain;
            margin: 10px 0;
        }

        .pdf-preview {
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            text-align: center;
        }
        
        .total-price-card {
            background: #f8f9fa;
            border-left: 4px solid #0d6efd;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
        }
        
        .modal-body .voucher-preview {
            max-height: 70vh;
            width: auto;
            margin: 0 auto;
        }
        
        .modal-body .pdf-preview {
            padding: 40px;
            margin: 20px;
            background: #f8f9fa;
            border-radius: 12px;
        }

        .fee-estimate {
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
            border-left: 4px solid #0d6efd;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <?php include('inc/hsidemenu.php'); ?>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <?php if ($error_message): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?php echo $error_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php echo $success_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (!$active_booking): ?>
            <div class="alert alert-info">
                <h4 class="alert-heading">No Active Booking</h4>
                <p>You need an active room booking to request visitors.</p>
                <hr>
                <p class="mb-0">Visit the <a href="hostelerdash.php">dashboard</a> to book a room.</p>
            </div>
        <?php elseif (!$fee_confirmed): ?>
            <div class="alert alert-warning">
                <h4 class="alert-heading">Fee Confirmation Required</h4>
                <p>Your hostel fee must be confirmed before you can request visitors.</p>
                <hr>
                <p class="mb-0">Please check your <a href="fee.php">fee status</a>.</p>
            </div>
        <?php else: ?>
            <!-- Visitor Request Form -->
            <div class="visitor-card">
                <h3 class="mb-4">New Visitor Request</h3>
                <form method="POST" action="" class="visitor-form" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Visitor's Name</label>
                            <input type="text" class="form-control" name="vname" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Relationship</label>
                            <input type="text" class="form-control" name="relation" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Purpose of Visit</label>
                            <select class="form-select" name="reason" required onchange="toggleAdditionalFields(this.value)">
                                <option value="">Select Purpose</option>
                                <option value="visit">Visit</option>
                                <option value="stay">Stay</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3" id="daysSection" style="display: none;">
                            <label class="form-label">Number of Days</label>
                            <input type="number" class="form-control" name="days" min="1" value="1" 
                                   onchange="calculateFee()" onkeyup="calculateFee()">
                        </div>
                    </div>

                    <!-- Fee Display Section -->
                    <div id="feeDisplaySection" class="fee-estimate" style="display: none;"></div>

                    <div id="voucherSection" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label">Upload Payment Voucher</label>
                            <input type="file" class="form-control" name="voucher" id="voucher" 
                                   accept=".pdf,.jpg,.jpeg,.png">
                            <small class="text-muted">Allowed formats: PDF, JPG, JPEG, PNG (Max size: 5MB)</small>
                        </div>
                        <div id="voucherPreview" class="mt-3"></div>
                    </div>

                    <button type="submit" name="submit" class="btn btn-primary mt-3">Submit Request</button>
                </form>
            </div>

            <!-- Active Visitors -->
            <?php if (!empty($active_visitors)): ?>
                <div class="visitor-card">
                    <h3 class="mb-4">Active Visitors</h3>
                    
                    <!-- Total Price Summary -->
                    <div class="total-price-card">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="mb-0">Total Visitor Fees</h5>
                            </div>
                            <div class="col-auto">
                                <h4 class="text-primary mb-0">
                                    Rs <?php echo number_format(array_sum(array_column($active_visitors, 'fee')), 2); ?>
                                </h4>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Visitor Name</th>
                                    <th>Relationship</th>
                                    <th>Purpose</th>
                                    <th>Days</th>
                                    <th>Fee</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                    <th> Confirm Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($active_visitors as $visitor): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($visitor['vname']); ?></td>
                                        <td><?php echo htmlspecialchars($visitor['relation']); ?></td>
                                        <td><?php echo htmlspecialchars($visitor['reason']); ?></td>
                                        <td><?php echo $visitor['days']; ?></td>
                                        <td>Rs <?php echo number_format($visitor['fee'], 2); ?></td>
                                        <td>
                                            <span class="status-badge status-accepted">Active</span>
                                        </td>
                                        <td>
                                            <?php if ($visitor['reason'] === 'stay' && !empty($visitor['voucher'])): ?>
                                                <button type="button" 
                                                        class="btn btn-sm btn-primary"
                                                        onclick="showVoucherPreview('<?php echo htmlspecialchars($visitor['voucher']); ?>', 
                                                                                   '<?php echo htmlspecialchars($visitor['vname']); ?>')">
                                                    View Voucher
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                         <td><?php echo $visitor['confirm_date'] ? date('Y-m-d H:i:s', strtotime($visitor['confirm_date'])) : '-'; ?></td>

                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Voucher Preview Modal -->
                <div class="modal fade" id="voucherPreviewModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Visitor Voucher</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body text-center">
                                <div id="modalVoucherPreview"></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Pending Visitors -->
            <?php if (!empty($pending_visitors)): ?>
                <div class="visitor-card">
                    <h3 class="mb-4">Pending Requests</h3>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Visitor Name</th>
                                    <th>Relationship</th>
                                    <th>Purpose</th>
                                    <th>Days</th>
                                    <th>Fee</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pending_visitors as $visitor): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($visitor['vname']); ?></td>
                                        <td><?php echo htmlspecialchars($visitor['relation']); ?></td>
                                        <td><?php echo htmlspecialchars($visitor['reason']); ?></td>
                                        <td><?php echo $visitor['days']; ?></td>
                                        <td>Rs <?php echo number_format($visitor['fee'], 2); ?></td>
                                        <td>
                                            <span class="status-badge status-pending">Pending</span>
                                        </td>
                                        <td>
                                            <?php if ($visitor['reason'] === 'stay' && !empty($visitor['voucher'])): ?>
                                                <button type="button" 
                                                        class="btn btn-sm btn-secondary"
                                                        onclick="showVoucherPreview('<?php echo htmlspecialchars($visitor['voucher']); ?>', 
                                                                                   '<?php echo htmlspecialchars($visitor['vname']); ?>')">
                                                    View Voucher
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
        const roomRate = <?php echo isset($active_booking['rprice']) ? $active_booking['rprice'] : 0; ?>;
        let voucherModal;

        // Initialize Bootstrap modal on page load
        document.addEventListener('DOMContentLoaded', function() {
            voucherModal = new bootstrap.Modal(document.getElementById('voucherPreviewModal'));
            
            // Add click handlers to all voucher preview buttons
            document.querySelectorAll('.voucher-preview-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const voucherPath = this.getAttribute('data-voucher');
                    const visitorName = this.getAttribute('data-visitor');
                    showVoucherPreview(voucherPath, visitorName);
                });
            });
        });

        function showVoucherPreview(voucherPath, visitorName) {
            const previewContainer = document.getElementById('modalVoucherPreview');
            const fileExtension = voucherPath.split('.').pop().toLowerCase();
            
            // Clear previous content
            previewContainer.innerHTML = '';
            
            // Update modal title first
            const modalTitle = document.querySelector('#voucherPreviewModal .modal-title');
            modalTitle.textContent = 'Payment Voucher - ' + visitorName;
            
            // Show loading indicator
            previewContainer.innerHTML = '<div class="text-center p-4">Loading voucher...</div>';
            
            // Show modal before loading content
            voucherModal.show();
            
            if (['jpg', 'jpeg', 'png'].includes(fileExtension)) {
                // For images, preload before showing
                const img = new Image();
                img.onload = function() {
                    previewContainer.innerHTML = `
                        <img src="uploads/visitorVouchers/${voucherPath}" 
                             alt="Payment Voucher for ${visitorName}"
                             class="voucher-preview img-fluid">`;
                };
                img.onerror = function() {
                    previewContainer.innerHTML = '<div class="alert alert-danger">Error loading image. Please try again.</div>';
                };
                img.src = `uploads/visitorVouchers/${voucherPath}`;
            } else if (fileExtension === 'pdf') {
                // For PDFs, use iframe with fallback
                previewContainer.innerHTML = `
                    <div class="ratio ratio-16x9" style="height: 70vh;">
                        <iframe src="uploads/visitorVouchers/${voucherPath}" 
                                type="application/pdf" 
                                width="100%" 
                                height="100%"
                                class="rounded"
                                onload="this.style.display='block'"
                                onerror="this.parentElement.innerHTML='<div class=\'alert alert-danger\'>Error loading PDF. Please try again.</div>'">
                        </iframe>
                    </div>`;
            }
        }

        
        function calculateFee() {
            const reason = document.querySelector('select[name="reason"]').value;
            const days = document.querySelector('input[name="days"]').value;
            const feeDisplaySection = document.getElementById('feeDisplaySection');
            
            if (reason === 'stay' && days > 0) {
                const fee = (roomRate / 2) * parseInt(days);
                feeDisplaySection.style.display = 'block';
                feeDisplaySection.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">Estimated Fee</h5>
                            <small class="text-muted">Based on ${days} day(s) stay at Rs ${(roomRate/2).toFixed(2)} per day</small>
                        </div>
                        <div>
                            <h4 class="text-primary mb-0">Rs ${fee.toFixed(2)}</h4>
                        </div>
                    </div>`;
            } else {
                feeDisplaySection.style.display = 'none';
                feeDisplaySection.innerHTML = '';
            }
        }

        function toggleAdditionalFields(reason) {
            const daysSection = document.getElementById('daysSection');
            const voucherSection = document.getElementById('voucherSection');
            const feeDisplaySection = document.getElementById('feeDisplaySection');
            
            if (reason === 'stay') {
                daysSection.style.display = 'block';
                voucherSection.style.display = 'block';
                document.getElementById('voucher').required = true;
                calculateFee();
            } else {
                daysSection.style.display = 'none';
                voucherSection.style.display = 'none';
                document.getElementById('voucher').required = false;
                feeDisplaySection.style.display = 'none';
                feeDisplaySection.innerHTML = '';
            }
        }



 </script>
</body>
</html>