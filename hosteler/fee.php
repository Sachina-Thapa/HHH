<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require('inc/db.php');

// Initialize variables with default values
$room_fee = 0;
$service_total = 0;
$grand_total = 0;
$days = 0;
$services = [];
$booking = null;
$existing_fee = null;
$error_message = '';
$success_message = '';

// Check login
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];

// Get hosteler ID
try {
    $stmt = $conn->prepare("SELECT id FROM hostelers WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hosteler_id = $row['id'];
    } else {
        throw new Exception("User not found in hostelers table");
    }
} catch (Exception $e) {
    die("Error fetching user: " . $e->getMessage());
}

// Get latest booking status (both active and completed)
$latest_booking_status = null;
try {
    $query = "SELECT b.*, r.rprice, r.rtype, r.rid, 
              b.bstatus, b.check_out >= CURRENT_DATE() as is_active
              FROM booking b 
              JOIN room r ON b.rno = r.rno 
              WHERE b.id = ? AND b.bstatus = 'confirmed'
              ORDER BY b.bookingdate DESC LIMIT 1";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $hosteler_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $booking = $result->fetch_assoc();
    
    if ($booking) {
        $latest_booking_status = $booking['bstatus'];
        $is_active_booking = $booking['is_active'];
        
        if ($is_active_booking) {
            $room_id = $booking['rid'];
            $booking_id = $booking['bid'];
            
            // Calculate days properly
            if (!empty($booking['number_of_days'])) {
                $days = $booking['number_of_days'];
            } else if (!empty($booking['check_in']) && !empty($booking['check_out'])) {
                $days = (strtotime($booking['check_out']) - strtotime($booking['check_in'])) / (60 * 60 * 24) + 1;
            } else {
                $days = 0;
            }
            
            // Calculate room fee
            $room_fee = floatval($booking['rprice']) * $days;
        }
    }
} catch (Exception $e) {
    error_log("Error fetching booking: " . $e->getMessage());
    die("Error fetching booking: " . $e->getMessage());
}

// Show different messages based on booking status
if (!$booking || $latest_booking_status === 'completed') {
    $message_type = "info";
    $message_title = "No Active Booking";
    $message_content = "You have checked out from your previous booking. Please make a new booking to continue.";
    $message_action = "hostelerdash.php";
    $message_button = "Book a Room";
    
    // Reset all fees and totals
    $room_fee = 0;
    $service_total = 0;
    $grand_total = 0;
    $days = 0;
} elseif (!$is_active_booking) {
    $message_type = "warning";
    $message_title = "Booking Expired";
    $message_content = "Your booking period has ended. Please make a new booking.";
    $message_action = "hostelerdash.php";
    $message_button = "Book a Room";
} else {
    // Get active services for current booking
    $service_id = null;
    try {
        $stmt = $conn->prepare("SELECT h.name, h.price, h.seid 
                               FROM hservice h 
                               WHERE h.hid = ? AND h.bid = ? AND h.status IN ('accepted', 'pending')
                               ORDER BY h.id DESC");
        $stmt->bind_param("ii", $hosteler_id, $booking_id);
        $stmt->execute();
        $services = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $service_total = array_sum(array_column($services, 'price'));
        
        if (!empty($services)) {
            $service_id = $services[0]['seid'];
        }
    } catch (Exception $e) {
        error_log("Error fetching services: " . $e->getMessage());
        $service_total = 0;
    }

    // Calculate grand total
    $grand_total = $room_fee + $service_total;

    // Get existing fee record for current booking
    try {
        $stmt = $conn->prepare("
            SELECT f.feeid, f.voucher, f.status 
            FROM fee f
            WHERE f.hid = ? AND f.rid = ? AND f.bid = ? AND f.status != 'completed'
            ORDER BY f.feeid DESC LIMIT 1");
        $stmt->bind_param("iii", $hosteler_id, $room_id, $booking_id);
        $stmt->execute();
        $existing_fee = $stmt->get_result()->fetch_assoc();
    } catch (Exception $e) {
        error_log("Error checking fee existence: " . $e->getMessage());
    }
}
// Handle voucher upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['voucher']) && isset($_POST['upload_voucher'])) {
    try {
        // Validate file
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
        $upload_dir = 'uploads/vouchers/';
        if (!is_dir($upload_dir)) {
            if (!@mkdir($upload_dir, 0777, true)) {
                throw new Exception('Failed to create vouchers directory');
            }
            @chmod($upload_dir, 0777);
        }

        // Generate filename
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $new_filename = uniqid('voucher_') . '_' . date('Ymd_His') . '.' . $file_extension;
        $destination = $upload_dir . $new_filename;

        // Start transaction
        $conn->begin_transaction();

        try {
            if (!@move_uploaded_file($file['tmp_name'], $destination)) {
                throw new Exception('Failed to move uploaded file');
            }

            if ($existing_fee) {
                // Update existing fee record
                $stmt = $conn->prepare("UPDATE fee 
                                      SET total = ?, 
                                          voucher = ?,
                                          status = 'pending'
                                      WHERE feeid = ?");
                $stmt->bind_param("dsi", $grand_total, $new_filename, $existing_fee['feeid']);
            } else {
                // Insert new fee record with booking ID
                $stmt = $conn->prepare("INSERT INTO fee 
                                      (hid, rid, seid, bid, total, voucher, status) 
                                      VALUES (?, ?, ?, ?, ?, ?, 'pending')");
                $stmt->bind_param("iiidss", $hosteler_id, $room_id, $service_id, 
                                $booking_id, $grand_total, $new_filename);
            }

            if (!$stmt->execute()) {
                throw new Exception('Database error: ' . $stmt->error);
            }

            $conn->commit();
            $success_message = "Voucher uploaded successfully. Awaiting confirmation.";
            
            // Refresh fee record
            $stmt = $conn->prepare("SELECT feeid, voucher, status FROM fee 
                                  WHERE hid = ? AND rid = ? AND bid = ? 
                                  ORDER BY feeid DESC LIMIT 1");
            $stmt->bind_param("iii", $hosteler_id, $room_id, $booking_id);
            $stmt->execute();
            $existing_fee = $stmt->get_result()->fetch_assoc();

        } catch (Exception $e) {
            $conn->rollback();
            if (file_exists($destination)) {
                @unlink($destination);
            }
            throw $e;
        }

    } catch (Exception $e) {
        error_log("Error processing voucher upload: " . $e->getMessage());
        $error_message = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hostel Fee Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
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
        
        .fee-card {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 4px;
            font-weight: 500;
            font-size: 0.9rem;
            display: inline-block;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-confirmed {
            background-color: #28a745;
            color: #ffffff;
        }
        
        .status-rejected {
            background-color: #dc3545;
            color: #ffffff;
        }

        .status-canceled {
            background-color: #dc3545;
            color: #ffffff;
        }

        .voucher-preview-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            min-height: 200px;
        }

        .voucher-preview {
            max-width: 100%;
            max-height: 70vh;
            width: auto;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            object-fit: contain;
            padding: 10px;
            background: white;
        }
        
        .modal-body {
            padding: 1.5rem;
            background-color: #f8f9fa;
        }
        
        .modal-header {
            border-bottom: 1px solid #dee2e6;
            background-color: #fff;
        }
        
        .modal-content {
            border: none;
            border-radius: 12px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .upload-section {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            background-color: #f8f9fa;
        }

        .pdf-preview {
            padding: 20px;
            text-align: center;
            background-color: #f8f9fa;
            border-radius: 8px;
        }

        .alert {
            margin-bottom: 20px;
            border-radius: 8px;
        }

        .alert-info {
            background-color: #cce5ff;
            border-color: #b8daff;
        }

        .alert-heading {
            color: #004085;
            margin-bottom: 10px;
        }

        .alert-link {
            font-weight: 600;
            text-decoration: none;
        }

        .alert-link:hover {
            text-decoration: underline;
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
        <!-- Alert Messages -->
        <?php if ($error_message): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $success_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (!$booking): ?>
            <!-- No Active Booking Message -->
            <div class="alert alert-info">
                <h4 class="alert-heading">No Active Booking</h4>
                <p>You don't have any active room bookings at the moment.</p>
                <hr>
                <p class="mb-0">Visit the <a href="hostelerdash.php" class="alert-link">dashboard</a> to book a room.</p>
            </div>
        <?php else: ?>
            <!-- Fee Summary Card -->
            <div class="fee-card">
                <h3 class="mb-4">Fee Summary</h3>
                <div class="row">
                    <div class="col-md-4">
                        <div class="fee-details">
                            <div class="fee-label">Room Fee</div>
                            <div class="amount">Rs <?php echo number_format($room_fee, 2); ?></div>
                            <small class="text-muted">Stay Duration: <?php echo $days; ?> days</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="fee-details">
                            <div class="fee-label">Service Charges</div>
                            <div class="amount">Rs <?php echo number_format($service_total, 2); ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="fee-details">
                            <div class="fee-label">Total Amount</div>
                            <div class="total-amount">Rs <?php echo number_format($grand_total, 2); ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Booking Details Card -->
            <div class="fee-card">
                <h3 class="mb-4">Booking Details</h3>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <tr>
                            <th style="width: 200px;">Check-in Date:</th>
                             <td><?php 
        if (!empty($booking['check_in'])) {
            echo date('d M Y', strtotime($booking['check_in']));
        } else {
            echo "Not set";
        }
    ?></td>
                        </tr>
                        <tr>
                            <th>Check-out Date:</th>
                            <td><?php 
        if (!empty($booking['check_out'])) {
            echo date('d M Y', strtotime($booking['check_out']));
        } else {
            echo "Not set";
        }
    ?></td>
                        </tr>
                        <tr>
                            <th>Room Number:</th>
                            <td><?php echo !empty($booking['rno']) ? $booking['rno'] : "Not assigned"; ?></td>
                        </tr>
                        <tr>
                            <th>Room Type:</th>
                           <td><?php echo !empty($booking['rtype']) ? $booking['rtype'] : "Not assigned"; ?></td>
                        </tr>
                        <tr>
                            <th>Daily Rate:</th>
                             <td>Rs <?php echo !empty($booking['rprice']) ? number_format($booking['rprice'], 2) : "0.00"; ?></td>
                        </tr>
                        <tr>
                            <th>Number of Days:</th>
                            <td><?php echo !empty($days) ? $days : "0"; ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Additional Services Card -->
            <?php if ($services): ?>
            <div class="fee-card">
                <h3 class="mb-4">Additional Services</h3>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Service</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($services as $service): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($service['name']); ?></td>
                                <td>Rs <?php echo number_format($service['price'], 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            <!-- Payment Voucher Card -->
            <div class="fee-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="mb-0">Payment Voucher</h3>
                    <?php if ($existing_fee && $existing_fee['voucher']): ?>
                        <span class="status-badge status-<?php echo strtolower($existing_fee['status']); ?>">
                            <?php echo ucfirst($existing_fee['status']); ?>
                        </span>
                    <?php endif; ?>
                </div>

                <?php if ($existing_fee && $existing_fee['voucher'] && $existing_fee['status'] != 'canceled'): ?>
                    <?php
                    $voucher_path = 'uploads/vouchers/' . $existing_fee['voucher'];
                    $file_extension = strtolower(pathinfo($existing_fee['voucher'], PATHINFO_EXTENSION));
                    ?>
                    
                    <div class="text-center p-4 bg-light rounded">
                        <div class="d-flex justify-content-center align-items-center">
                            <div class="me-3">
                                <i class="bi bi-file-earmark-check fs-1 text-success"></i>
                            </div>
                            <div class="text-start">
                                <h5 class="mb-1">Voucher Uploaded Successfully</h5>
                                <p class="text-muted mb-2">File: <?php echo $existing_fee['voucher']; ?></p>
                            </div>
                        </div>
                        <?php if (in_array($file_extension, ['jpg', 'jpeg', 'png'])): ?>
                            <button type="button" class="btn btn-primary mt-3" 
                                    onclick="showVoucherPreview('<?php echo $existing_fee['voucher']; ?>')">
                                <i class="bi bi-eye me-2"></i>View Voucher
                            </button>
                        <?php elseif ($file_extension === 'pdf'): ?>
                            <a href="<?php echo $voucher_path; ?>" target="_blank" class="btn btn-primary mt-3">
                                <i class="bi bi-file-pdf me-2"></i>View PDF Voucher
                            </a>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <?php if ($existing_fee && $existing_fee['status'] == 'canceled'): ?>
                        <div class="alert alert-danger mb-4">
                            <h5 class="alert-heading"><i class="bi bi-exclamation-triangle me-2"></i>Your payment was canceled</h5>
                            <p>Please upload a new payment voucher using the form below.</p>
                        </div>
                    <?php endif; ?>
                    <div class="upload-section">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="voucher" class="form-label">Select Voucher File</label>
                                <input type="file" class="form-control" name="voucher" id="voucher" 
                                       accept=".pdf,.jpg,.jpeg,.png" required>
                                <small class="text-muted d-block mt-2">Allowed formats: PDF, JPG, JPEG, PNG (Max size: 5MB)</small>
                            </div>
                            <button type="submit" name="upload_voucher" class="btn btn-primary">Upload Voucher</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Voucher Preview Modal -->
    <div class="modal fade" id="voucherPreviewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Payment Voucher</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div id="modalVoucherPreview"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
    let voucherModal;
    
    // Initialize modal on page load
    document.addEventListener('DOMContentLoaded', function() {
        voucherModal = new bootstrap.Modal(document.getElementById('voucherPreviewModal'));
    });

    function showVoucherPreview(voucherPath) {
        const previewContainer = document.getElementById('modalVoucherPreview');
        const fileExtension = voucherPath.split('.').pop().toLowerCase();
        
        // Clear previous content
        previewContainer.innerHTML = '';
        
        // Show loading indicator
        previewContainer.innerHTML = '<div class="text-center p-4">Loading voucher...</div>';
        
        // Show modal
        voucherModal.show();
        
        if (['jpg', 'jpeg', 'png'].includes(fileExtension)) {
            const img = new Image();
            img.onload = function() {
                previewContainer.innerHTML = `
                    <img src="uploads/vouchers/${voucherPath}" 
                         alt="Payment Voucher"
                         class="voucher-preview img-fluid">`;
            };
            img.onerror = function() {
                previewContainer.innerHTML = '<div class="alert alert-danger">Error loading image. Please try again.</div>';
            };
            img.src = `uploads/vouchers/${voucherPath}`;
        } else if (fileExtension === 'pdf') {
            previewContainer.innerHTML = `
                <div class="pdf-preview">
                    <a href="uploads/vouchers/${voucherPath}" target="_blank" class="btn btn-primary">
                        View PDF Voucher
                    </a>
                </div>`;
        }
    }

    // File validation
    document.getElementById('voucher')?.addEventListener('change', function() {
        const file = this.files[0];
        const maxSize = 5 * 1024 * 1024; // 5MB
        
        if (file.size > maxSize) {
            alert('File is too large. Maximum size allowed is 5MB');
            this.value = '';
            return;
        }
        
        const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
        const fileType = file.type.toLowerCase();
        
        if (!allowedTypes.includes(fileType)) {
            alert('Invalid file type. Please upload PDF, JPG, or PNG files only');
            this.value = '';
            return;
        }
    });

    // Auto-hide alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach(function(alert) {
                const closeButton = alert.querySelector('.btn-close');
                if (closeButton) {
                    closeButton.click();
                }
            });
        }, 5000);
    });
    </script>
</body>
</html> 