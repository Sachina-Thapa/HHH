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
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hosteler_id = $row['id'];
    }
} catch (Exception $e) {
    die("Error fetching user: " . $e->getMessage());
}

// Check if fee is confirmed
$fee_confirmed = false;
$fee_confirmed_date = null;
$fee_status = null;
try {
    $stmt = $conn->prepare("SELECT status, confirmed_date FROM fee 
                           WHERE hid = ? 
                           ORDER BY feeid DESC LIMIT 1");
    $stmt->bind_param("i", $hosteler_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $fee_status = $row['status'];
        // Only set fee_confirmed if status is 'confirmed' and has a confirmed date
        $fee_confirmed = ($row['status'] === 'confirmed' && !empty($row['confirmed_date']));
        $fee_confirmed_date = $row['confirmed_date'];
    }
} catch (Exception $e) {
    error_log("Error checking fee status: " . $e->getMessage());
}

// Get current active booking
$active_booking = null;
try {
    $query = "SELECT b.* FROM booking b 
              WHERE b.id = ? AND b.bstatus = 'confirmed' 
              AND b.check_out >= CURRENT_DATE()
              ORDER BY b.bookingdate DESC LIMIT 1";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $hosteler_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $active_booking = $result->fetch_assoc();
} catch (Exception $e) {
    die("Error fetching active booking: " . $e->getMessage());
}

// Handle service requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['submit_services'])) {
        if (!$active_booking) {
            $error_message = "You need an active booking to request services.";
        } else {
            $services = $_POST['services'] ?? [];
            
            if (!empty($services)) {
                $conn->begin_transaction();
                
                try {
                    // If fee is confirmed, validate voucher upload
                    $voucher_filename = null;
                    if ($fee_confirmed && !empty($fee_confirmed_date)) {
                        if (!isset($_FILES['service_voucher']) || $_FILES['service_voucher']['error'] !== UPLOAD_ERR_OK) {
                            throw new Exception("Payment voucher is required for services after fee confirmation");
                        }

                        $file = $_FILES['service_voucher'];
                        
                        // Validate file
                        $allowed_types = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
                        $max_size = 5 * 1024 * 1024; // 5MB

                        if ($file['size'] > $max_size) {
                            throw new Exception('File size exceeds limit of 5MB');
                        }

                        $mime_type = mime_content_type($file['tmp_name']);
                        if (!in_array($mime_type, $allowed_types)) {
                            throw new Exception('Invalid file type. Only PDF, JPG, and PNG files are allowed');
                        }

                        // Setup upload directory
                        $upload_dir = 'uploads/service_vouchers/';
                        if (!is_dir($upload_dir)) {
                            if (!@mkdir($upload_dir, 0777, true)) {
                                throw new Exception('Failed to create vouchers directory');
                            }
                        }

                        // Generate unique filename
                        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                        $voucher_filename = uniqid('service_voucher_') . '_' . date('Ymd_His') . '.' . $file_extension;
                        $destination = $upload_dir . $voucher_filename;

                        if (!move_uploaded_file($file['tmp_name'], $destination)) {
                            throw new Exception('Failed to upload voucher');
                        }
                    }

                    // Process each selected service
                    foreach ($services as $service_id) {
                        // Get service details
                        $stmt = $conn->prepare("SELECT name, price FROM services WHERE seid = ?");
                        $stmt->bind_param("i", $service_id);
                        $stmt->execute();
                        $service = $stmt->get_result()->fetch_assoc();

                        if (!$service) {
                            throw new Exception("Invalid service selected");
                        }

                        // Determine if this is a post-fee service
                        $is_post_fee = ($fee_confirmed && !empty($fee_confirmed_date)) ? 1 : 0;

                        if ($is_post_fee) {
                            // Insert service with voucher and payment status
                            $stmt = $conn->prepare("INSERT INTO hservice 
                                                  (seid, name, price, hid, bid, status, payment_status,
                                                   post_fee_confirmation, voucher, total) 
                                                  VALUES (?, ?, ?, ?, ?, 'pending', 'pending', 1, ?, ?)");
                            $stmt->bind_param("isdiisi", 
                                $service_id,
                                $service['name'],
                                $service['price'],
                                $hosteler_id,
                                $active_booking['bid'],
                                $voucher_filename,
                                $service['price']
                            );
                        } else {
                            // Insert regular service
                            $stmt = $conn->prepare("INSERT INTO hservice 
                                                  (seid, name, price, hid, bid, status, 
                                                   post_fee_confirmation, total) 
                                                  VALUES (?, ?, ?, ?, ?, 'pending', 0, ?)");
                            $stmt->bind_param("isdiid", 
                                $service_id,
                                $service['name'],
                                $service['price'],
                                $hosteler_id,
                                $active_booking['bid'],
                                $service['price']
                            );
                        }
                        
                        if (!$stmt->execute()) {
                            throw new Exception("Failed to insert service request");
                        }
                    }
                    
                    $conn->commit();
                    $success_message = "Service requests submitted successfully.";
                } catch (Exception $e) {
                    $conn->rollback();
                    $error_message = $e->getMessage();
                    
                    // Clean up uploaded file if it exists
                    if ($voucher_filename && file_exists($upload_dir . $voucher_filename)) {
                        @unlink($upload_dir . $voucher_filename);
                    }
                }
            } else {
                $error_message = "Please select at least one service.";
            }
        }
    }
}

// Get available services
$available_services = [];
try {
    if ($active_booking) {
        $query = "SELECT s.* FROM services s 
                  WHERE s.seid NOT IN (
                      SELECT h.seid FROM hservice h 
                      WHERE h.hid = ? AND h.bid = ? AND h.status IN ('pending', 'accepted')
                  )";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $hosteler_id, $active_booking['bid']);
        $stmt->execute();
        $available_services = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
} catch (Exception $e) {
    error_log("Error fetching available services: " . $e->getMessage());
}

// Get active and pending services
$active_services = [];
$pending_services = [];
try {
    if ($active_booking) {
        $stmt = $conn->prepare("SELECT h.*, 
                               (SELECT services.price FROM services WHERE services.seid = h.seid) as service_price 
                               FROM hservice h 
                               WHERE h.hid = ? AND h.bid = ? AND h.status IN ('accepted', 'pending')
                               ORDER BY h.id DESC");
        $stmt->bind_param("ii", $hosteler_id, $active_booking['bid']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($service = $result->fetch_assoc()) {
            if ($service['status'] === 'accepted') {
                $active_services[] = $service;
            } else {
                $pending_services[] = $service;
            }
        }
    }
} catch (Exception $e) {
    error_log("Error fetching service status: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Management</title>
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
        
        .service-card {
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
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-accepted {
            background-color: #d4edda;
            color: #155724;
        }

        .voucher-preview {
            max-width: 100%;
            max-height: 300px;
            object-fit: contain;
        }

        .service-item {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }

        .service-summary {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
            display: none;
        }

        .voucher-section {
            background-color: #fff;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
            display: none;
        }

        .service-list {
            list-style: none;
            padding: 0;
        }

        .service-list li {
            padding: 8px 0;
            border-bottom: 1px solid #dee2e6;
        }

        .service-list li:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <?php include('inc/hsidemenu.php'); ?>
    </div>

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
                <p>You need an active room booking to request services.</p>
                <hr>
                <p class="mb-0">Visit the <a href="hostelerdash.php">dashboard</a> to book a room.</p>
            </div>
        <?php else: ?>
            <!-- Available Services -->
            <?php if (!empty($available_services)): ?>
                <div class="service-card">
                    <h3 class="mb-4">Request Services</h3>
                    <form method="POST" action="" enctype="multipart/form-data" id="serviceForm">
                        <div class="row">
                            <?php foreach ($available_services as $service): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="service-item">
                                        <div class="form-check">
                                            <input class="form-check-input service-checkbox" 
                                                   type="checkbox" 
                                                   name="services[]" 
                                                   value="<?php echo $service['seid']; ?>"
                                                   id="service_<?php echo $service['seid']; ?>"
                                                   data-name="<?php echo htmlspecialchars($service['name']); ?>"
                                                   data-price="<?php echo $service['price']; ?>"
                                                   onchange="updateServiceSummary()">
                                            <label class="form-check-label" for="service_<?php echo $service['seid']; ?>">
                                                <?php echo htmlspecialchars($service['name']); ?> 
                                                (Rs <?php echo number_format($service['price'], 2); ?>)
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Service Summary Section -->
                        <div id="serviceSummary" class="service-summary">
                            <h4>Selected Services</h4>
                            <ul id="selectedServicesList" class="service-list mb-3"></ul>
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Total Amount:</h5>
                                <h5 class="mb-0">Rs <span id="totalAmount">0.00</span></h5>
                            </div>
                        </div>

                        <!-- Voucher Upload Section (shows only after fee confirmation) -->
                        <?php if ($fee_confirmed && !empty($fee_confirmed_date)): ?>
                            <div id="voucherSection" class="voucher-section">
                                <h4>Payment Voucher</h4>
                                <p class="text-muted mb-3">Upload payment proof for all selected services</p>
                                <div class="mb-3">
                                    <input type="file" 
                                           class="form-control" 
                                           name="service_voucher" 
                                           id="serviceVoucher"
                                           accept=".pdf,.jpg,.jpeg,.png">
                                    <div class="form-text">
                                        Accepted formats: PDF, JPG, PNG (Max size: 5MB)
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <button type="submit" 
                                name="submit_services" 
                                class="btn btn-primary mt-3"
                                id="submitButton" 
                                disabled>
                            Request Services
                        </button>
                    </form>
                </div>
            <?php endif; ?>

            <!-- Active Services -->
            <?php if (!empty($active_services)): ?>
                <div class="service-card">
                    <h3 class="mb-4">Active Services</h3>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Service Name</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <?php if ($fee_confirmed && !empty($fee_confirmed_date)): ?>
                                        <th>Payment Status</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($active_services as $service): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($service['name']); ?></td>
                                        <td>Rs <?php echo number_format($service['price'], 2); ?></td>
                                        <td>
                                            <span class="status-badge status-accepted">Active</span>
                                        </td>
                                        <?php if ($fee_confirmed && !empty($fee_confirmed_date)): ?>
                                            <td>
                                                <?php if ($service['post_fee_confirmation']): ?>
                                                    <?php if ($service['payment_status'] === 'pending'): ?>
                                                        <span class="badge bg-warning">Pending Verification</span>
                                                    <?php elseif ($service['payment_status'] === 'confirmed'): ?>
                                                        <span class="badge bg-success">Confirmed</span>
                                                    <?php endif; ?>
                                                    
                                                    <?php if (!empty($service['voucher'])): ?>
                                                        <button type="button" 
                                                                class="btn btn-sm btn-info ms-2"
                                                                onclick="previewVoucher('<?php echo $service['voucher']; ?>')">
                                                            View Voucher
                                                        </button>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="badge bg-info">Included in Main Fee</span>
                                                <?php endif; ?>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Pending Services -->
            <?php if (!empty($pending_services)): ?>
                <div class="service-card">
                    <h3 class="mb-4">Pending Services</h3>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Service Name</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <?php if ($fee_confirmed && !empty($fee_confirmed_date)): ?>
                                        <th>Payment Status</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pending_services as $service): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($service['name']); ?></td>
                                        <td>Rs <?php echo number_format($service['price'], 2); ?></td>
                                        <td>
                                            <span class="status-badge status-pending">Pending</span>
                                        </td>
                                        <?php if ($fee_confirmed && !empty($fee_confirmed_date)): ?>
                                            <td>
                                                <?php if ($service['post_fee_confirmation']): ?>
                                                    <?php if ($service['payment_status'] === 'pending'): ?>
                                                        <span class="badge bg-warning">Pending Verification</span>
                                                    <?php endif; ?>
                                                    
                                                    <?php if (!empty($service['voucher'])): ?>
                                                        <button type="button" 
                                                                class="btn btn-sm btn-info ms-2"
                                                                onclick="previewVoucher('<?php echo $service['voucher']; ?>')">
                                                            View Voucher
                                                        </button>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="badge bg-info">Included in Main Fee</span>
                                                <?php endif; ?>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Voucher Preview Modal -->
            <div class="modal fade" id="voucherPreviewModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Payment Voucher Preview</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body text-center">
                            <div id="voucherPreviewContent"></div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let voucherPreviewModal;
        
        document.addEventListener('DOMContentLoaded', function() {
            voucherPreviewModal = new bootstrap.Modal(document.getElementById('voucherPreviewModal'));
            
            // Form validation
            const serviceForm = document.getElementById('serviceForm');
            if (serviceForm) {
                serviceForm.addEventListener('submit', validateForm);
            }

            // File validation
            const voucherInput = document.getElementById('serviceVoucher');
            if (voucherInput) {
                voucherInput.addEventListener('change', validateFile);
            }
        });

        function updateServiceSummary() {
            const selectedServices = document.querySelectorAll('.service-checkbox:checked');
            const summarySection = document.getElementById('serviceSummary');
            const voucherSection = document.getElementById('voucherSection');
            const submitButton = document.getElementById('submitButton');
            const servicesList = document.getElementById('selectedServicesList');
            const totalAmount = document.getElementById('totalAmount');
            
            if (selectedServices.length > 0) {
                let total = 0;
                let listHtml = '';
                
                selectedServices.forEach(checkbox => {
                    const name = checkbox.dataset.name;
                    const price = parseFloat(checkbox.dataset.price);
                    total += price;
                    
                    listHtml += `<li class="d-flex justify-content-between">
                                    <span>${name}</span>
                                    <span>Rs ${price.toFixed(2)}</span>
                                </li>`;
                });
                
                servicesList.innerHTML = listHtml;
                totalAmount.textContent = total.toFixed(2);
                summarySection.style.display = 'block';
                if (voucherSection) voucherSection.style.display = 'block';
                submitButton.disabled = false;
            } else {
                summarySection.style.display = 'none';
                if (voucherSection) voucherSection.style.display = 'none';
                submitButton.disabled = true;
            }
        }

        function validateFile(event) {
            const file = event.target.files[0];
            if (!file) return;

            const maxSize = 5 * 1024 * 1024; // 5MB
            const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
            
            if (file.size > maxSize) {
                alert('File is too large. Maximum size allowed is 5MB');
                event.target.value = '';
                return false;
            }
            
            const fileType = file.type.toLowerCase();
            
            if (!allowedTypes.includes(fileType)) {
                alert('Invalid file type. Please upload PDF, JPG, or PNG files only');
                event.target.value = '';
                return false;
            }
            
            return true;
        }

        function validateForm(event) {
            const selectedServices = document.querySelectorAll('.service-checkbox:checked');
            
            if (selectedServices.length === 0) {
                alert('Please select at least one service');
                event.preventDefault();
                return false;
            }

            const voucherInput = document.getElementById('serviceVoucher');
            if (voucherInput && voucherInput.style.display !== 'none' && selectedServices.length > 0) {
                if (!voucherInput.files || voucherInput.files.length === 0) {
                    alert('Please upload a payment voucher for the selected services');
                    event.preventDefault();
                    return false;
                }
                
                if (!validateFile({ target: voucherInput })) {
                    event.preventDefault();
                    return false;
                }
            }
            
            return true;
        }

        function previewVoucher(voucherPath) {
            const previewContainer = document.getElementById('voucherPreviewContent');
            const fileExtension = voucherPath.split('.').pop().toLowerCase();
            
            if (['jpg', 'jpeg', 'png'].includes(fileExtension)) {
                previewContainer.innerHTML = `
                    <img src="uploads/service_vouchers/${voucherPath}" 
                         alt="Payment Voucher" 
                         class="voucher-preview">`;
            } else if (fileExtension === 'pdf') {
                previewContainer.innerHTML = `
                    <div class="text-center">
                        <a href="uploads/service_vouchers/${voucherPath}" 
                           target="_blank" 
                           class="btn btn-primary">
                            View PDF Voucher
                        </a>
                    </div>`;
            }
            
            voucherPreviewModal.show();
        }

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            document.querySelectorAll('.alert').forEach(function(alert) {
                const closeBtn = alert.querySelector('.btn-close');
                if (closeBtn) closeBtn.click();
            });
        }, 5000);
    </script>
</body>
</html>