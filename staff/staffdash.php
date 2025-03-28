<?php
// Check if it's an AJAX request
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    require_once('inc/db.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        $id = $_POST['id'] ?? '';
        $seid = $_POST['seid'] ?? '';

        if (empty($id) || empty($action) || empty($seid)) {
            echo "Missing required parameters";
            exit;
        }

        // Prepare the new status based on the action
        $status = ($action === 'confirm') ? 'accepted' : 'cancelled';
        $payment_status = ($action === 'confirm') ? 'confirmed' : 'rejected';
        
        // Update the status in the hservice table including payment_status and payment_date
        $stmt = $mysqli->prepare("UPDATE hservice 
                                SET status = ?, 
                                    payment_status = ?,
                                    payment_date = CASE 
                                        WHEN ? = 'confirmed' THEN CURRENT_TIMESTAMP 
                                        ELSE NULL 
                                    END 
                                WHERE id = ? AND seid = ?");
        
        if (!$stmt) {
            echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
            exit;
        }

        $stmt->bind_param("sssii", $status, $payment_status, $payment_status, $id, $seid);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo "1"; // Success
            } else {
                echo "No rows were updated";
            }
        } else {
            echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        }

        $stmt->close();
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Sidebar styling */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 200px;
            background-color: #343a40;
            padding-top: 20px;
            z-index: 1000;
        }
        
        /* Main content styling */
        .main-content {
            margin-left: 200px; /* Match sidebar width */
            padding: 20px;
            width: calc(100% - 200px);
            min-height: 100vh;
        }

        /* Stat cards styling */
        .stat-card {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .stat-card h2 {
            font-size: 28px;
            margin: 0;
            color: #28a745;
        }

        .stat-card p {
            margin: 5px 0 0;
            color: #6c757d;
        }

        /* Table styling */
        .table-responsive {
            overflow-x: auto;
            margin-top: 1rem;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.9em;
        }

        .status-pending {
            background-color: #ffc107;
            color: #000;
        }

        /* Voucher preview styling */
        .voucher-preview {
            max-width: 100%;
            max-height: 300px;
            object-fit: contain;
        }
        .modal-body .voucher-preview {
            max-height: 70vh;
            width: auto;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <?php require('inc/sidemenu.php'); ?>
    </div>

    <div class="main-content">
        <?php 
        require('inc/db.php');

        // Enable error reporting for debugging
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        // Query for dashboard statistics
        $stats = [
            'rooms' => $mysqli->query("SELECT COUNT(*) FROM room")->fetch_row()[0],
            'bookings' => $mysqli->query("SELECT COUNT(*) FROM booking")->fetch_row()[0],
            'feedback' => $mysqli->query("SELECT COUNT(*) FROM feedback")->fetch_row()[0],
            'hostelers' => $mysqli->query("SELECT COUNT(*) FROM hostelers")->fetch_row()[0],
            'visitors' => $mysqli->query("SELECT COUNT(*) FROM visitorform")->fetch_row()[0]
        ];
        ?>
        
        <!-- Statistics Cards - Remain unchanged -->
        <div class="row">
            <div class="col-md-4">
                <div class="stat-card">
                    <h2><?php echo $stats['rooms']; ?></h2>
                    <p>Total Rooms</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <h2><?php echo $stats['bookings']; ?></h2>
                    <p>New Bookings</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <h2><?php echo $stats['feedback']; ?></h2>
                    <p>Feedback Count</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <h2><?php echo $stats['hostelers']; ?></h2>
                    <p>Total Hostelers</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <h2><?php echo $stats['visitors']; ?></h2>
                    <p>Visitor Forms</p>
                </div>
            </div>
        </div>

        <!-- Service Requests Section -->
        <div class="card mt-4">
            <div class="card-header">
                <h3 class="mb-0">Pending Service Requests</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>S.N.</th>
                                <th>Hosteler Name</th>
                                <th>Service ID</th>
                                <th>Service Name</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Payment Status</th>
                                <th>Payment Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Fetch pending service requests with updated query
                            $stmt = $mysqli->query("SELECT 
                                hservice.id, 
                                hservice.seid, 
                                hservice.name AS service_name, 
                                hservice.price, 
                                hservice.hid,
                                hservice.status,
                                hservice.payment_status,
                                hservice.payment_date,
                                hservice.voucher,
                                hostelers.name AS hosteler_name 
                            FROM hservice 
                            JOIN hostelers ON hservice.hid = hostelers.id 
                            WHERE hservice.status = 'pending'
                            ORDER BY hservice.id DESC");

                            if (!$stmt) {
                                echo "<tr><td colspan='9' class='text-center text-danger'>Error fetching service requests</td></tr>";
                            } else {
                                $count = 1;
                                while ($row = $stmt->fetch_assoc()) {
                                    echo "<tr data-id='{$row['id']}'>
                                            <td>{$count}</td>
                                            <td>{$row['hosteler_name']}</td>
                                            <td>{$row['seid']}</td>
                                            <td>{$row['service_name']}</td>
                                            <td>Rs{$row['price']}</td>
                                            <td>
                                                <span class='status-badge status-pending'>
                                                    {$row['status']}
                                                </span>
                                            </td>
                                            <td>{$row['payment_status']}</td>
                                            <td>" . ($row['payment_date'] ? date('Y-m-d H:i:s', strtotime($row['payment_date'])) : '-') . "</td>
                                            <td>";
                                    
                                    // Add voucher preview button if voucher exists
                                    if (!empty($row['voucher'])) {
                                        echo "<button type='button' class='btn btn-info btn-sm mb-2' 
                                                onclick=\"showVoucherPreview('" . htmlspecialchars($row['voucher']) . "', '" . 
                                                htmlspecialchars($row['service_name']) . "')\">
                                                <i class='bi bi-eye'></i> View Voucher
                                            </button><br>";
                                    }
                                    
                                    echo "<button type='button' class='btn btn-success btn-sm' 
                                            onclick='confirmservice({$row['id']}, {$row['seid']})'>
                                            <i class='bi bi-check-circle'></i> Accept
                                        </button>
                                        <button type='button' class='btn btn-danger btn-sm ms-2' 
                                            onclick='cancelservice({$row['id']}, {$row['seid']})'>
                                            <i class='bi bi-x-circle'></i> Decline
                                        </button>
                                    </td>
                                    </tr>";
                                    $count++;
                                }
                                if ($count === 1) {
                                    echo "<tr><td colspan='9' class='text-center'>No pending service requests</td></tr>";
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Voucher Preview Modal -->
    <div class="modal fade" id="voucherPreviewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Service Voucher Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <div id="voucherPreviewContent"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <script>
    let voucherModal;
    
    document.addEventListener('DOMContentLoaded', function() {
        voucherModal = new bootstrap.Modal(document.getElementById('voucherPreviewModal'));
    });

    function showVoucherPreview(voucherPath, serviceName) {
        const previewContainer = document.getElementById('voucherPreviewContent');
        const fileExtension = voucherPath.split('.').pop().toLowerCase();
        
        previewContainer.innerHTML = '<div class="text-center p-4">Loading voucher...</div>';
        voucherModal.show();

        if (['jpg', 'jpeg', 'png'].includes(fileExtension)) {
            const img = new Image();
            const imagePath = `/HHH/hosteler/uploads/service_vouchers/${voucherPath}`;
            
            img.onload = function() {
                previewContainer.innerHTML = `
                    <img src="${imagePath}" 
                         alt="Payment Voucher for ${serviceName}"
                         class="voucher-preview img-fluid">`;
            };
            
            img.onerror = function(e) {
                previewContainer.innerHTML = `
                    <div class="alert alert-danger">
                        <p>Error loading image.</p>
                        <p>File details:</p>
                        <ul>
                            <li>Attempted path: ${imagePath}</li>
                            <li>Original filename: ${voucherPath}</li>
                        </ul>
                    </div>`;
            };
            
            img.src = imagePath;
        } else if (fileExtension === 'pdf') {
            previewContainer.innerHTML = `
                <div class="alert alert-info">
                    <p>PDF file detected. Please click below to view:</p>
                    <a href="/HHH/hosteler/uploads/service_vouchers/${voucherPath}" 
                       target="_blank" 
                       class="btn btn-primary">
                        View PDF Voucher
                    </a>
                </div>`;
        } else {
            previewContainer.innerHTML = `
                <div class="alert alert-warning">
                    <p>Unsupported file type: ${fileExtension}</p>
                </div>`;
        }
    }

    function showMessage(message, type = 'success') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.role = 'alert';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        document.querySelector('.main-content').insertBefore(alertDiv, document.querySelector('.card'));
        
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }

    function confirmservice(id, seid) {
        if (confirm("Are you sure you want to accept this service request?")) {
            updateService(id, seid, 'confirm');
        }
    }

    function cancelservice(id, seid) {
        if (confirm("Are you sure you want to decline this service request?")) {
            updateService(id, seid, 'cancel');
        }
    }

    function updateService(id, seid, action) {
        const formData = new FormData();
        formData.append('action', action);
        formData.append('id', id);
        formData.append('seid', seid);

        fetch(window.location.href, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(data => {
            if (data === '1') {
                const row = document.querySelector(`tr[data-id='${id}']`);
                if (row) {
                    row.remove();
                }
                showMessage(`Service request ${action === 'confirm' ? 'accepted' : 'declined'} successfully!`);
                
                const tbody = document.querySelector('tbody');
                if (!tbody.querySelector('tr')) {
                    tbody.innerHTML = "<tr><td colspan='9' class='text-center'>No pending service requests</td></tr>";
                }
            } else {
                throw new Error(data);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('Failed to update service request: ' + error.message, 'danger');
        });
    }
    </script>
</body>
</html>