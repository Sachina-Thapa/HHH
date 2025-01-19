<?php 
session_start(); // Start session
require('../admin/inc/db.php');
include('inc/hsidemenu.php'); 

// Check if the session variable 'username' is set
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username']; // Retrieve the username from session
} else {
    $username = "Session not set"; // Default message if session is not set
}
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hosteler Panel - Dashboard</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    .main-content {
            margin-left: 210px; /* Adjust to the sidebar width */
            padding: 20px;
            flex-grow: 1;
            background-color: #f1f1f1;
        }
    .room-card {
        margin-bottom: 20px;
    }
</style>
</head>
<body>
<div class="main-content">
    <div class="row">

        <!-- Main Content Column -->
        <div class="col-md-10 p-4">
            <h3 class="mt-3">Hosteler Panel</h3>

            <?php
            // Fetch and display name if session is active and database connected
            if (isset($_SESSION['username'])) {
                $user = $_SESSION['username'];
                $q = mysqli_query($conn, "SELECT * FROM hostelers WHERE username='$user'");

                if ($q && mysqli_num_rows($q) > 0) {
                    $row = mysqli_fetch_array($q);
                    $name = $row['name']; // Get the name from the database
                } else {
                    $name = "User  not found";
                }
            } else {
                $name = "Session not set";
            }
            ?>

            <!-- Display Welcome Message -->
            <div class="alert alert-info mt-3">
                <?php echo "Welcome! " . htmlspecialchars($name); ?>
            </div>

            <!-- Available Rooms Section -->
            <h4 class="mt-4">Available Rooms</h4>
            <div class="row">
                <?php
                // Query to get available rooms
                $roomQuery = "
                    SELECT r.rno, r.rtype, r.rprice 
                    FROM room r 
                    WHERE r.rno NOT IN (SELECT b.rno FROM booking b)
                ";
                $roomResult = mysqli_query($conn, $roomQuery);

                // Check for query errors
                if (!$roomResult) {
                    echo "Error: " . mysqli_error($conn);
                } else {
                    if (mysqli_num_rows($roomResult) > 0) {
                        while ($room = mysqli_fetch_assoc($roomResult)) {
                            echo '<div class="col-md-4 room-card">';
                            echo '<div class="card">';
                            echo '<div class="card-body">';
                            echo '<h5 class="card-title">Room No: ' . htmlspecialchars($room['rno']) . '</h5>';
                            echo '<p class="card-text">Type: ' . htmlspecialchars($room['rtype']) . '</p>';
                            echo '<p class="card-text">Price: ' . htmlspecialchars($room['rprice']) . '</p>';
                            echo '<a href="#" class="btn btn-primary">Book Now</a>'; // Add booking link or button
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="alert alert-warning">No available rooms at the moment.</div>';
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>