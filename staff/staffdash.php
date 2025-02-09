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

        // Update the status in the hservice table
        $stmt = $mysqli->prepare("UPDATE hservice SET status = ? WHERE id = ? AND seid = ?");
        
        if (!$stmt) {
            echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
            exit;
        }

        $stmt->bind_param("sii", $status, $id, $seid);
        
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
        }
        
        .sidebar a {
            color: #fff;
            padding: 15px;
            display: block;
            text-decoration: none;
        }

        .sidebar a:hover {
            background-color: #495057;
        }

        /* Main content styling */
        .main-content {
            margin-left: 210px;
            padding: 20px;
        }

        /* Stat card styling */
        .stat-card {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            color: #28a745;
            margin-bottom: 20px;
        }
        .stat-card h2 {
            font-size: 36px;
            margin: 0;
        }
        .stat-card p {
            margin: 5px 0 0;
            color: #6c757d;
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
    </style>
</head>
<body>
    <?php require('inc/sidemenu.php'); ?>

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
        
        <!-- Statistics Cards -->
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
                                <th>#</th>
                                <th>Hosteler Name</th>
                                <th>Service ID</th>
                                <th>Service Name</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Fetch pending service requests
                            $stmt = $mysqli->query("SELECT 
                                hservice.id, 
                                hservice.seid, 
                                hservice.name AS service_name, 
                                hservice.price, 
                                hservice.hid, 
                                hservice.status, 
                                hostelers.name AS hosteler_name 
                            FROM hservice 
                            JOIN hostelers ON hservice.hid = hostelers.id 
                            WHERE hservice.status = 'pending'
                            ORDER BY hservice.id DESC");

                            if (!$stmt) {
                                echo "<tr><td colspan='7' class='text-center text-danger'>Error fetching service requests</td></tr>";
                            } else {
                                $count = 1;
                                while ($row = $stmt->fetch_assoc()) {
                                    echo "<tr data-id='{$row['id']}'>
                                            <td>{$count}</td>
                                            <td>{$row['hosteler_name']}</td>
                                            <td>{$row['seid']}</td>
                                            <td>{$row['service_name']}</td>
                                            <td>\${$row['price']}</td>
                                            <td>
                                                <span class='status-badge status-pending'>
                                                    {$row['status']}
                                                </span>
                                            </td>
                                            <td>
                                                <button type='button' class='btn btn-success btn-sm' 
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
                                    echo "<tr><td colspan='7' class='text-center'>No pending service requests</td></tr>";
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <script>
    function showMessage(message, type = 'success') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.role = 'alert';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        document.querySelector('.main-content').insertBefore(alertDiv, document.querySelector('.card'));
        
        // Auto-dismiss after 5 seconds
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
                // Remove the row from the table
                const row = document.querySelector(`tr[data-id='${id}']`);
                if (row) {
                    row.remove();
                }
                
                // Show success message
                showMessage(`Service request ${action === 'confirm' ? 'accepted' : 'declined'} successfully!`);
                
                // Check if table is empty after removal
                const tbody = document.querySelector('tbody');
                if (!tbody.querySelector('tr')) {
                    tbody.innerHTML = "<tr><td colspan='7' class='text-center'>No pending service requests</td></tr>";
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