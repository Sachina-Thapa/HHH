<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Sidebar Styles */
        .sidebar {
            height: 100vh;
            background-color: hsl(210, 10.30%, 22.70%);
            padding-top: 10px;
            position: fixed;
            width: 250px;
            left: 0;
            top: 0;
            overflow-y: auto;
            z-index: 1000;
        }

        .sidebar a {
            color: #fff;
            padding: 15px;
            display: block;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .sidebar a:hover {
            background-color: #495057;
        }

        .sidebar h3 {
            padding: 15px;
            margin-bottom: 20px;
            color: #fff;
        }

        /* Main Content Styles */
        .main-content {
            margin-left: 250px; /* Same as sidebar width */
            padding: 20px;
            min-height: 100vh;
            background-color: #f8fafc;
            transition: margin-left 0.3s ease;
        }

        .content-wrapper {
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                width: 100%;
            }
            
            .content-wrapper {
                padding: 1rem;
            }
        }

        /* Add margin to the logout button */
        .sidebar .btn {
            margin: 15px;
            width: calc(100% - 30px);
        }

        .sidebar .btn a {
            color: #000;
            padding: 0;
        }

        .sidebar .btn a:hover {
            background: none;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h3>Her Home Hostel</h3>
        <a href="hostelerdash.php">Dashboard</a>
        <a href="booking.php">Manage Booking</a>
        <a href="feedback.php">Feedback</a>
        <a href="visitor.php">Visitor</a>
        <a href="services.php">Services</a>
        <a href="accounts.php">My Account</a>
        <a href="fee.php">Fee</a>
        <button class="btn btn-light"><a href="../index.php">LOG OUT</a></button>
    </div>
</body>
</html>