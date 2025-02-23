<?php
require('../admin/inc/db.php');

// Check if a form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $vid = $_POST['vid'];
    $action = $_POST['action'];

    // Determine the new status based on the action
    $new_status = ($action === 'accept') ? 'accepted' : 'declined';
    $voucher_status = ($action === 'accept') ? 1 : 0;
    
    // Add confirm_date when accepting
    if ($action === 'accept') {
        $sql = "UPDATE visitorform SET status = ?, voucher_status = ?, confirm_date = CURRENT_TIMESTAMP WHERE vid = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sii", $new_status, $voucher_status, $vid);
    } else {
        $sql = "UPDATE visitorform SET status = ?, voucher_status = ? WHERE vid = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sii", $new_status, $voucher_status, $vid);
    }

    if (!$stmt->execute()) {
        echo "Error updating status: " . $stmt->error;
    }

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            if ($row['reason'] === 'stay' && !empty($row['voucher'])) {
                $voucherPath = "uploads/visitorVouchers/" . $row['voucher'];
                echo "<!-- Debug: Full path: " . realpath($voucherPath) . " -->";
                echo "<!-- Debug: File exists: " . (file_exists($voucherPath) ? 'Yes' : 'No') . " -->";
                
                echo "<button type='button'...";
            }
        }
    }
    $stmt->close();
}

// Fetch visitor data with confirm_date
$sql = "
    SELECT v.hid, h.name AS hosteler_name, v.vid, v.vname, v.relation, v.reason, v.days, 
           v.status, v.voucher, v.confirm_date 
    FROM visitorform v 
    JOIN hostelers h ON v.hid = h.id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            margin: 0;
        }
        
        .content {
            margin-left: 200px;
            padding: 20px;
            width: calc(100% - 200px);
        }

        .accepted {
            background-color: #d4edda;
        }

        .declined {
            opacity: 0.5;
        }

        .voucher-preview {
            max-width: 100%;
            max-height: 300px;
            object-fit: contain;
            margin: 10px 0;
        }

        .modal-body .voucher-preview {
            max-height: 70vh;
            width: auto;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <?php require('inc/sidemenu.php'); ?>

    <div class="content">
        <div class="container mt-4">
            <h3>Visitor Information</h3>
            <div class="table-responsive">
                <table class="table table-hover text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>Hosteler Id</th>
                            <th>Hosteler Name</th>
                            <th>Visitor ID</th>
                            <th>Name</th>
                            <th>Relation</th>
                            <th>Reason</th>
                            <th>Day</th>
                            <th>Action</th>
                            <th>Confirmation Date</th>
                            <th>Voucher</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    if ($result === false) {
                        echo "Error: " . $conn->error;
                    } else {
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                $row_class = '';
                                $action_message = '';

                                if ($row['status'] === 'accepted') {
                                    $row_class = 'accepted';
                                    $action_message = 'Accepted';
                                } elseif ($row['status'] === 'declined') {
                                    $row_class = 'declined';
                                    $action_message = 'Declined';
                                }

                                echo "<tr class='$row_class'>
                                        <td>" . htmlspecialchars($row["hid"]) . "</td>
                                        <td>" . htmlspecialchars($row["hosteler_name"]) . "</td>
                                        <td>" . htmlspecialchars($row["vid"]) . "</td>
                                        <td>" . htmlspecialchars($row["vname"]) . "</td>
                                        <td>" . htmlspecialchars($row["relation"]) . "</td>
                                        <td>" . htmlspecialchars($row["reason"]) . "</td>
                                        <td>" . htmlspecialchars($row["days"]) . "</td>
                                        <td>";
                                if ($action_message) {
                                    echo $action_message;
                                } else {
                                    echo "<form method='post' action=''>
                                            <input type='hidden' name='vid' value='" . htmlspecialchars($row['vid']) . "'>
                                            <button type='submit' name='action' value='accept' class='btn btn-success'>Accept</button>
                                            <button type='submit' name='action' value='decline' class='btn btn-danger'>Decline</button>
                                        </form>";
                                }
                                echo "</td>";
                                // Add confirmation date column
                                echo "<td>" . ($row['confirm_date'] ? htmlspecialchars($row['confirm_date']) : '-') . "</td>";
                                echo "<td>";
                                if ($row['reason'] === 'stay' && !empty($row['voucher'])) {
                                    echo "<!-- Debug: Voucher filename in DB: " . htmlspecialchars($row['voucher']) . " -->";
                                    
                                    echo "<button type='button' 
                                            class='btn btn-primary btn-sm'
                                            onclick=\"showVoucherPreview('" . htmlspecialchars($row['voucher']) . "', '" . 
                                            htmlspecialchars($row['vname']) . "')\">
                                            View Voucher
                                        </button>";
                                }
                                echo "</td></tr>";
                            }
                        } else {
                            echo "<tr><td colspan='10'>No visitors found.</td></tr>";
                        }
                    }
                    $conn->close();
                    ?>
                    </tbody>
                </table>
            </div>
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

     <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <script>
        let voucherModal;

        document.addEventListener('DOMContentLoaded', function() {
            voucherModal = new bootstrap.Modal(document.getElementById('voucherPreviewModal'));
        });

       function showVoucherPreview(voucherPath, visitorName) {
    const previewContainer = document.getElementById('modalVoucherPreview');
    const fileExtension = voucherPath.split('.').pop().toLowerCase();
    
    // Log for debugging
    console.log('Voucher path from database:', voucherPath);
    
    // Get the current page path
    const currentPath = window.location.pathname;
    console.log('Current page path:', currentPath);
    
    previewContainer.innerHTML = '<div class="text-center p-4">Loading voucher...</div>';
    voucherModal.show();

    if (['jpg', 'jpeg', 'png'].includes(fileExtension)) {
        const img = new Image();
        const imagePath = `/HHH/hosteler/uploads/visitorVouchers/${voucherPath}`;
        
        img.onload = function() {
            previewContainer.innerHTML = `
                <img src="${imagePath}" 
                     alt="Payment Voucher for ${visitorName}"
                     class="voucher-preview img-fluid">`;
            console.log('Image loaded successfully');
        };
        
        img.onerror = function(e) {
            console.error('Image load error:', e);
            
            // Show detailed error information
            previewContainer.innerHTML = `
                <div class="alert alert-danger">
                    <p>Error loading image.</p>
                    <p>File details:</p>
                    <ul>
                        <li>Attempted path: ${imagePath}</li>
                        <li>Original filename: ${voucherPath}</li>
                        <li>Current page: ${window.location.pathname}</li>
                    </ul>
                </div>`;
        };
        
        img.src = imagePath;
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
</body>
</html>
