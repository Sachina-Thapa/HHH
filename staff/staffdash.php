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
    </style>
</head>
<body>
<?php require('inc/sidemenu.php'); ?>

<div class="main-content">
    <?php require('inc/db.php');
        
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
        
        // Query for Check-In
        // $stmt = $mysqli->query("SELECT COUNT(*) FROM visitorform");
        // $checkin = $stmt->fetch_row()[0];
        
        // // Query for Check-Out
        // $stmt = $mysqli->query("SELECT COUNT(*) FROM visitorform ");
        // $checkout = $stmt->fetch_row()[0];
        
        // // Query for Fee Collected
        // $stmt = $mysqli->query("SELECT COUNT(*) FROM visitorform");
        // $feecollected = $stmt->fetch_row()[0];
        // ?>
        
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
                <!-- <i class="bi bi-person-plus"></i> -->
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <h2><?php echo $feedback; ?></h2>
                <p>Feedback</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <h2><?php echo $totalhosteler; ?></h2>
                <p>Total Hosteler</p>
                <!-- <i class="bi bi-person-plus"></i> -->
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <h2><?php echo $visitorform; ?></h2>
                <p>Visitor Form</p>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.js"></script>
</body>
</html>

