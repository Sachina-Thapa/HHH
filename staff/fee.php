<?php
require('../admin/inc/db.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle fee status updates
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['feeid']) && isset($_POST['hid']) && isset($_POST['action'])) {
        $feeid = intval($_POST['feeid']);
        $hid = intval($_POST['hid']);
        $action = $_POST['action'];
        
        if (in_array($action, ['confirm', 'cancel'])) {
            $new_status = ($action === 'confirm') ? 'confirmed' : 'canceled';
            
            try {
                $conn->begin_transaction();
                
                // Modified SQL to include confirmed_date when confirming
                if ($action === 'confirm') {
                    $sql = "UPDATE fee SET status = ?, confirmed_date = NOW() WHERE feeid = ? AND hid = ?";
                } else {
                    $sql = "UPDATE fee SET status = ? WHERE feeid = ? AND hid = ?";
                }
                
                $stmt = $conn->prepare($sql);
                
                if (!$stmt) {
                    throw new Exception("Prepare failed: " . $conn->error);
                }
                
                $stmt->bind_param("sii", $new_status, $feeid, $hid);
                
                if (!$stmt->execute()) {
                    throw new Exception("Execute failed: " . $stmt->error);
                }
                
                if ($stmt->affected_rows > 0) {
                    $conn->commit();
                    $message = "Fee status updated successfully to " . $new_status;
                    $status = "success";
                } else {
                    throw new Exception('No fee record found with the provided ID');
                }
                
                $stmt->close();
            } catch (Exception $e) {
                $conn->rollback();
                $message = "Error: " . $e->getMessage();
                $status = "error";
            }
        }
    }
}

// Fetch pending fee data
$pending_query = "
    SELECT 
        f.feeid,
        f.hid,
        f.rid,
        f.seid,
        f.vid,
        f.sid,
        f.total,
        f.voucher,
        f.status,
        f.confirmed_date,
        h.name AS hosteler_name,
        h.phone_number,
        h.email,
        r.rtype AS room_type,
        r.rno AS room_no
    FROM fee f
    JOIN hostelers h ON f.hid = h.id
    LEFT JOIN room r ON f.rid = r.rno
    WHERE f.status = 'pending'
    ORDER BY f.feeid DESC
";

$pending_result = $conn->query($pending_query);

// Fetch all fee data
$all_fees_query = "
    SELECT 
        f.feeid,
        f.hid,
        f.rid,
        f.seid,
        f.vid,
        f.sid,
        f.total,
        f.voucher,
        f.status,
        f.confirmed_date,
        h.name AS hosteler_name,
        h.phone_number,
        h.email,
        r.rtype AS room_type,
        r.rno AS room_no
    FROM fee f
    JOIN hostelers h ON f.hid = h.id
    LEFT JOIN room r ON f.rid = r.rno
    ORDER BY f.feeid DESC
";

$all_fees_result = $conn->query($all_fees_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .content {
            margin-left: 200px;
            padding: 20px;
        }
        .page-title {
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #dee2e6;
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.875rem;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }
        .status-confirmed {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .status-canceled {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .table {
            background-color: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .table th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            color: #495057;
            font-weight: 600;
        }
        .table td {
            vertical-align: middle;
        }
        .action-btn {
            margin: 0 5px;
        }
        .table tbody tr:hover {
            background-color: transparent !important;
        }
        .alert {
            margin-bottom: 20px;
        }
        .detail-text {
            margin-bottom: 0;
            font-size: 0.9rem;
            color: #666;
        }
        .detail-text strong {
            color: #333;
        }
        .section-divider {
            margin: 40px 0 20px;
            border-top: 2px solid #dee2e6;
        }
        .voucher-preview {
            max-width: 100%;
            max-height: 70vh;
            object-fit: contain;
        }
        .modal-body {
            text-align: center;
            background-color: #f8f9fa;
            padding: 20px;
        }
        .card {
            margin-bottom: 20px;
        }
    </style>
</head>
<body class="bg-light">
    <?php require('inc/sidemenu.php'); ?>

    <div class="content">
        <?php if (isset($message)): ?>
            <div class="alert alert-<?php echo $status === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="container-fluid">
            <h2 class="page-title">Fee Management</h2>
            
            <!-- Pending Fees Section -->
            <div class="card">
                <div class="card-header">
                    <h3 class="h5 mb-0">Pending Fee Requests</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Fee ID</th>
                                    <th>Hosteler Details</th>
                                    <th>Room Info</th>
                                    <th>Amount</th>
                                    <th>Voucher</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($pending_result && $pending_result->num_rows > 0) {
                                    while ($row = $pending_result->fetch_assoc()) {
                                        ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['feeid']); ?></td>
                                            <td>
                                                <p class="mb-1"><strong><?php echo htmlspecialchars($row['hosteler_name']); ?></strong></p>
                                                <p class="detail-text"><?php echo htmlspecialchars($row['phone_number']); ?></p>
                                                <p class="detail-text"><?php echo htmlspecialchars($row['email']); ?></p>
                                            </td>
                                            <td>
                                                <p class="detail-text">Type: <?php echo htmlspecialchars($row['room_type'] ?? 'N/A'); ?></p>
                                                <p class="detail-text">No: <?php echo htmlspecialchars($row['room_no'] ?? 'N/A'); ?></p>
                                            </td>
                                            <td>Rs <?php echo htmlspecialchars($row['total']); ?></td>
                                            <td>
                                                <?php if (!empty($row['voucher'])): ?>
                                                    <button type="button" class="btn btn-info btn-sm" 
                                                            onclick="showVoucherPreview('<?php echo htmlspecialchars($row['voucher']); ?>', '<?php echo htmlspecialchars($row['hosteler_name']); ?>')">
                                                        <i class="bi bi-eye"></i> View Voucher
                                                    </button>
                                                <?php else: ?>
                                                    N/A
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="status-badge status-<?php echo strtolower($row['status']); ?>">
                                                    <?php echo ucfirst(htmlspecialchars($row['status'])); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div>
                                                    <form method="POST" class="d-inline">
                                                        <input type="hidden" name="feeid" value="<?php echo $row['feeid']; ?>">
                                                        <input type="hidden" name="hid" value="<?php echo $row['hid']; ?>">
                                                        <input type="hidden" name="action" value="confirm">
                                                        <button type="submit" class="btn btn-outline-success action-btn" 
                                                                onclick="return confirm('Are you sure you want to confirm this fee payment?')">
                                                            <i class="bi bi-check-circle"></i> Confirm
                                                        </button>
                                                    </form>
                                                    <form method="POST" class="d-inline">
                                                        <input type="hidden" name="feeid" value="<?php echo $row['feeid']; ?>">
                                                        <input type="hidden" name="hid" value="<?php echo $row['hid']; ?>">
                                                        <input type="hidden" name="action" value="cancel">
                                                        <button type="submit" class="btn btn-outline-danger action-btn" 
                                                                onclick="return confirm('Are you sure you want to cancel this fee payment?')">
                                                            <i class="bi bi-x-circle"></i> Cancel
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    echo "<tr><td colspan='7' class='text-center py-3'>No pending fees found.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- All Fees Section -->
            <div class="card">
                <div class="card-header">
                    <h3 class="h5 mb-0">All Fee Records</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Fee ID</th>
                                    <th>Hosteler Details</th>
                                    <th>Room Info</th>
                                    <th>Amount</th>
                                    <th>Voucher</th>
                                    <th>Status</th>
                                    <th>Confirmed Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($all_fees_result && $all_fees_result->num_rows > 0) {
                                    while ($row = $all_fees_result->fetch_assoc()) {
                                        ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['feeid']); ?></td>
                                            <td>
                                                <p class="mb-1"><strong><?php echo htmlspecialchars($row['hosteler_name']); ?></strong></p>
                                                <p class="detail-text"><?php echo htmlspecialchars($row['phone_number']); ?></p>
                                                <p class="detail-text"><?php echo htmlspecialchars($row['email']); ?></p>
                                            </td>
                                            <td>
                                                <p class="detail-text">Type: <?php echo htmlspecialchars($row['room_type'] ?? 'N/A'); ?></p>
                                                <p class="detail-text">No: <?php echo htmlspecialchars($row['room_no'] ?? 'N/A'); ?></p>
                                            </td>
                                            <td>Rs <?php echo htmlspecialchars($row['total']); ?></td>
                                            <td>
                                                <?php if (!empty($row['voucher'])): ?>
                                                    <button type="button" class="btn btn-info btn-sm" 
                                                            onclick="showVoucherPreview('<?php echo htmlspecialchars($row['voucher']); ?>', '<?php echo htmlspecialchars($row['hosteler_name']); ?>')">
                                                        <i class="bi bi-eye"></i> View Voucher
                                                    </button>
                                                <?php else: ?>
                                                    N/A
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="status-badge status-<?php echo strtolower($row['status']); ?>">
                                                    <?php echo ucfirst(htmlspecialchars($row['status'])); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php 
                                                if ($row['confirmed_date']) {
                                                    echo date('Y-m-d H:i:s', strtotime($row['confirmed_date']));
                                                } else {
                                                    echo 'N/A';
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    echo "<tr><td colspan='7' class='text-center py-3'>No fee records found.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Voucher Preview Modal -->
    <div class="modal fade" id="voucherPreviewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Voucher Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="voucherPreviewContent"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let voucherModal;
        
        document.addEventListener('DOMContentLoaded', function() {
            voucherModal = new bootstrap.Modal(document.getElementById('voucherPreviewModal'));
            
            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                var alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    var bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });

        function showVoucherPreview(voucherPath, hostelerName) {
            const previewContainer = document.getElementById('voucherPreviewContent');
            const fileExtension = voucherPath.split('.').pop().toLowerCase();
            
            previewContainer.innerHTML = '<div class="text-center p-4">Loading voucher...</div>';
            voucherModal.show();

            if (['jpg', 'jpeg', 'png'].includes(fileExtension)) {
                const img = new Image();
                const imagePath = `/HHH/hosteler/uploads/vouchers/${voucherPath}`;
                
                img.onload = function() {
                    previewContainer.innerHTML = `
                        <img src="${imagePath}" 
                             alt="Payment Voucher for ${hostelerName}"
                             class="voucher-preview img-fluid">`;
                };
                
                img.onerror = function() {
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
                        <a href="/HHH/hosteler/uploads/vouchers/${voucherPath}" 
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
    </script>
</body>
</html>