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
</head>
<body>
    <div class="container-fluid col-md-10 p-4">
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
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>