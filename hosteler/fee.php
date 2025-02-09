<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Include database connection
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

// Get booking and room details
$booking = null;
$room_fee = 0;
$days = 0;
$room_id = null;

try {
    $query = "SELECT b.*, r.rprice, r.rtype, r.rid 
              FROM booking b 
              JOIN room r ON b.rno = r.rno 
              WHERE b.id = ? AND b.bstatus = 'confirmed'
              ORDER BY b.bookingdate DESC LIMIT 1";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("i", $hosteler_id);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $booking = $result->fetch_assoc();
    
    if ($booking) {
        $room_id = $booking['rid'];
        $days = !empty($booking['number_of_days']) ? 
                $booking['number_of_days'] : 
                (strtotime($booking['check_out']) - strtotime($booking['check_in'])) / (60 * 60 * 24) + 1;
        $room_fee = $booking['rprice'] * $days;
    }
} catch (Exception $e) {
    error_log("Error fetching booking: " . $e->getMessage());
    die("Error fetching booking: " . $e->getMessage());
}

// Get visitor fees and ID
$visitor_fees = 0;
$visitor_id = null;
try {
    $stmt = $conn->prepare("SELECT SUM(fee) as total_fee, MAX(vid) as latest_vid 
                           FROM visitorform WHERE hid = ?");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("i", $hosteler_id);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $visitor_fees = $row['total_fee'] ?? 0;
    $visitor_id = $row['latest_vid'];
} catch (Exception $e) {
    error_log("Error fetching visitor fees: " . $e->getMessage());
    die("Error fetching visitor fees: " . $e->getMessage());
}

// Get services and service ID
$services = [];
$service_total = 0;
$service_id = null;
try {
    $stmt = $conn->prepare("SELECT h.name, h.price, h.seid 
                           FROM hservice h 
                           WHERE h.hid = ? AND h.status = 'accepted'
                           ORDER BY h.id DESC");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("i", $hosteler_id);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $services = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $service_total = array_sum(array_column($services, 'price'));
    
    if (!empty($services)) {
        $service_id = $services[0]['seid'];
    }
} catch (Exception $e) {
    error_log("Error fetching services: " . $e->getMessage());
    die("Error fetching services: " . $e->getMessage());
}

// Calculate grand total
$grand_total = $room_fee + $visitor_fees + $service_total;

// Get existing fee record and voucher details
$existing_fee = null;
try {
    $stmt = $conn->prepare("SELECT feeid, voucher, status FROM fee WHERE hid = ? ORDER BY feeid DESC LIMIT 1");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("i", $hosteler_id);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $existing_fee = $result->fetch_assoc();
} catch (Exception $e) {
    error_log("Error checking fee existence: " . $e->getMessage());
    die("Error checking fee existence: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['voucher']) && isset($_POST['upload_voucher'])) {
    try {
        // Validate hosteler_id before proceeding
        $stmt = $conn->prepare("SELECT id FROM hostelers WHERE id = ?");
        $stmt->bind_param("i", $hosteler_id);
        if (!$stmt->execute()) {
            throw new Exception("Failed to validate hosteler ID");
        }
        
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            throw new Exception("Hosteler ID not found");
        }

        // Define upload directory
        $upload_dir = 'uploads/vouchers/';
        if (!is_dir($upload_dir)) {
            if (!@mkdir($upload_dir, 0777, true)) {
                throw new Exception('Failed to create vouchers directory');
            }
            @chmod($upload_dir, 0777);
        }

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

        // Generate unique filename
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
                                      SET rid = ?, 
                                          seid = ?, 
                                          vid = ?, 
                                          total = ?, 
                                          voucher = ?,
                                          status = 'pending'
                                      WHERE hid = ? AND feeid = ?");
                $stmt->bind_param("iiidssi", $room_id, $service_id, $visitor_id, $grand_total, 
                                $new_filename, $hosteler_id, $existing_fee['feeid']);
            } else {
                // Insert new fee record
                $stmt = $conn->prepare("INSERT INTO fee 
                                      (hid, rid, seid, vid, total, voucher, status) 
                                      VALUES (?, ?, ?, ?, ?, ?, 'pending')");
                $stmt->bind_param("iiidss", $hosteler_id, $room_id, $service_id, $visitor_id, 
                                $grand_total, $new_filename);
            }

            if (!$stmt->execute()) {
                throw new Exception('Database error: ' . $stmt->error);
            }

            $conn->commit();
            $success_message = "Voucher uploaded and fee record updated successfully.";
            
            // Refresh the fee record after successful upload
            $stmt = $conn->prepare("SELECT feeid, voucher, status FROM fee WHERE hid = ? ORDER BY feeid DESC LIMIT 1");
            $stmt->bind_param("i", $hosteler_id);
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

// Return JSON response for AJAX requests
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => empty($error_message),
        'error' => $error_message,
        'success_message' => $success_message,
        'fee_data' => [
            'room_fee' => $room_fee,
            'visitor_fees' => $visitor_fees,
            'service_total' => $service_total,
            'grand_total' => $grand_total,
            'days' => $days
        ]
    ]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hostel Fee Management</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
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
        
        .status-approved {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* Voucher specific styles */
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
            max-height: 400px;
            width: auto;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            object-fit: contain;
            padding: 10px;
            background: white;
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

.status-approved, .status-confirmed {
    background-color: #28a745;
    color: #ffffff;
}

.status-rejected, .status-cancel {
    background-color: #dc3545;
    color: #ffffff;
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

        <!-- Fee Summary Card -->
        <div class="fee-card">
            <h3 class="mb-4">Fee Summary</h3>
            <div class="row">
                <div class="col-md-3">
                    <div class="fee-details">
                        <div class="fee-label">Room Fee</div>
                        <div class="amount">Rs <?php echo number_format($room_fee, 2); ?></div>
                        <small class="text-muted">Stay Duration: <?php echo $days; ?> days</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="fee-details">
                        <div class="fee-label">Visitor Fees</div>
                        <div class="amount">Rs <?php echo number_format($visitor_fees, 2); ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="fee-details">
                        <div class="fee-label">Service Charges</div>
                        <div class="amount">Rs <?php echo number_format($service_total, 2); ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="fee-details">
                        <div class="fee-label">Total Amount</div>
                        <div class="total-amount">Rs <?php echo number_format($grand_total, 2); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Details Card -->
        <?php if ($booking): ?>
        <div class="fee-card">
            <h3 class="mb-4">Booking Details</h3>
            <div class="table-responsive">
                <table class="table table-hover">
                    <tr>
                        <th style="width: 200px;">Check-in Date:</th>
                        <td><?php echo date('d M Y', strtotime($booking['check_in'])); ?></td>
                    </tr>
                    <tr>
                        <th>Check-out Date:</th>
                        <td><?php echo date('d M Y', strtotime($booking['check_out'])); ?></td>
                    </tr>
                    <tr>
                        <th>Room Number:</th>
                        <td><?php echo $booking['rno']; ?></td>
                    </tr>
                    <tr>
                        <th>Room Type:</th>
                        <td><?php echo $booking['rtype']; ?></td>
                    </tr>
                    <tr>
                        <th>Daily Rate:</th>
                        <td>Rs <?php echo number_format($booking['rprice'], 2); ?></td>
                    </tr>
                    <tr>
                        <th>Number of Days:</th>
                        <td><?php echo $days; ?></td>
                    </tr>
                </table>
            </div>
        </div>
        <?php endif; ?>

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

            <?php if ($existing_fee && $existing_fee['voucher']): ?>
                <?php
                $voucher_path = 'uploads/vouchers/' . $existing_fee['voucher'];
                $file_extension = strtolower(pathinfo($existing_fee['voucher'], PATHINFO_EXTENSION));
                ?>
                
                <?php if (in_array($file_extension, ['jpg', 'jpeg', 'png'])): ?>
                    <div class="voucher-preview-container">
                        <img src="<?php echo $voucher_path; ?>" alt="Payment Voucher" class="voucher-preview">
                    </div>
                <?php elseif ($file_extension === 'pdf'): ?>
                    <div class="pdf-preview">
                        <a href="<?php echo $voucher_path; ?>" target="_blank" class="btn btn-primary">
                            View PDF Voucher
                        </a>
                    </div>
                <?php endif; ?>
            <?php else: ?>
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
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    // Simple file validation
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
    </script>
</body>
</html>