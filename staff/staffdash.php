<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
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
            margin-left: 210px; /* Adjust to be a bit more than sidebar width */
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

        .message {
            display: none;
            margin-top: 20px;
            font-size: 16px;
            color: #28a745;
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

    // Query for Total Bookings
    $stmt = $mysqli->query("SELECT COUNT(*) FROM booking");
    $totalbooking = $stmt->fetch_row()[0];
    
    // Query for Available Rooms
    $stmt = $mysqli->query("SELECT COUNT(*) FROM room");
    $totalroom = $stmt->fetch_row()[0];
    
    // Query for Enquiries
    $stmt = $mysqli->query("SELECT COUNT(*) FROM feedback");
    $feedback = $stmt->fetch_row()[0];
    
    // Query for Hostelers
    $stmt = $mysqli->query("SELECT COUNT(*) FROM hostelers");
    $totalhosteler = $stmt->fetch_row()[0];
    
    // Query for Visitor Forms
    $stmt = $mysqli->query("SELECT COUNT(*) FROM visitorform");
    $visitorform = $stmt->fetch_row()[0];

    // Check for messages
    $message = '';
    if (isset($_GET['action']) && isset($_GET['status'])) {
        $action = $_GET['action'];
        $message = "You have $action the service request.";
    }
    ?>
        
    <!-- Display the stats in a row -->
    <div class="row">
        <div class="col-md-4">
            <div class="stat-card">
                <h2><?php echo $totalroom; ?></h2>
                <p>Total Room</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <h2><?php echo $totalbooking; ?></h2>
                <p>New Booking</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <h2><?php echo $feedback; ?></h2>
                <p>Feedback</p>
            </div>
        </div>
        <div class ="col-md-4">
            <div class="stat-card">
                <h2><?php echo $totalhosteler; ?></h2>
                <p>Total Hosteler</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <h2><?php echo $visitorform; ?></h2>
                <p>Visitor Form</p>
            </div>
        </div>
    </div>

    <!-- Display message if exists -->
    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>

    <!-- Hosteler Service Requests Table -->
    <h3 class="mt-4">Hosteler Service Requests</h3>
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
            // Query to fetch only pending services
            $stmt = $mysqli->query("SELECT hservice.id, hservice.seid, hservice.name, hservice.price, hservice.hid, hservice.status, hostelers.name AS hosteler_name 
                                     FROM hservice 
                                     JOIN hostelers ON hservice.hid = hostelers.id
                                     WHERE hservice.status = 'Pending'");

            if (!$stmt) {
                die("Query failed: " . $mysqli->error);
            }

            $count = 1;
            while ($row = $stmt->fetch_assoc()) {
                echo "<tr id='row-{$row['id']}'>
                        <td>{$count}</td>
                        <td>{$row['hosteler_name']}</td>
                        <td>{$row['seid']}</td>
                        <td>{$row['name']}</td>
                        <td>{$row['price']}</td>
                        <td>{$row['status']}</td>
                        <td>
                            <form method='post' action='ajax_handler.php' class='action-form'>
                                <input type='hidden' name='id' value='{$row['id']}'>
                                <button type='button' name='action' value='accept' class='btn btn-success btn-sm accept-btn'>Accept</button>
                                <button type='button' name='action' value='decline' class='btn btn-danger btn-sm decline-btn'>Decline</button>
                            </form>
                            <div class='message' id='message-{$row['id']}'></div>
                        </td>
                      </tr>";
                $count++;
            }
            ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
<script>
    document.querySelectorAll('.action-form').forEach(form => {
        // Extract the buttons
        const acceptButton = form.querySelector('.accept-btn');
        const declineButton = form.querySelector('.decline-btn');
        const messageDiv = form.querySelector('.message');

        acceptButton.addEventListener('click', function() {
            handleFormSubmit(form, 'accept');
        });

        declineButton.addEventListener('click', function() {
            handleFormSubmit(form, 'decline');
        });
    });

    function handleFormSubmit(form, action) {
        const formData = new FormData(form);
        formData.append('action', action); // Append action manually
        const id = formData.get('id');
        const row = document.getElementById(`row-${id}`);
        const messageDiv = form.querySelector('.message');

        // Disable both buttons after one is clicked
        form.querySelectorAll('button').forEach(button => {
            button.disabled = true;
        });

        // Send the form data using fetch
        fetch('staffdash.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Show the message based on the action
                if (data.action === 'accept') {
                    messageDiv.textContent = `You have accepted the service: ${row.cells[3].textContent} for ${row.cells[1].textContent}`;
                } else if (data.action === 'decline') {
                    messageDiv.textContent = `You have declined the service: ${row.cells[3].textContent} for ${row.cells[1].textContent}`;
                }
                messageDiv.style.display = 'block';

                // Remove the row immediately
                row.remove();

                // Set a timeout to remove the message after 3 seconds
                setTimeout(() => {
                    messageDiv.style.display = 'none'; // Hide the message after 3 seconds
                }, 3000);
            } else {
                console.error(data.message); // Log the error message if any
            }
        })
        .catch(error => console.error('Error:', error));
    }
</script>
</body>
</html>