<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
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
            margin-left: 220px; /* Adjust to be a bit more than sidebar width */
            padding: 20px;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<?php require('inc/sidemenu.php'); ?>

<!-- Main Content Area -->
<div class="main-content">
    <h1>Booking Page</h1>
    <p>Hello, this is the booking page content.</p>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>
