<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Panel Sidebar</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* Sidebar styling */
        .sidebar {
            background-color: #343a40;
            height: 100vh;
            padding-top: 10px;
            position: fixed;
            left: 0;
            top: 0;
            width: 200px;
        }
        .sidebar h4 {
            color: #fff;
            text-align: center;
            margin-bottom: 20px;
        }
        .sidebar a {
            color: #fff;
            padding: 15px;
            display: block;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .sidebar .btn {
            background-color: transparent !important;
            border: none;
            width: 100%;
            padding: 0;
        }
        .sidebar .btn a {
            color: #fff;
            text-decoration: none;
            padding: 15px;
            display: block;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="col-lg-2 sidebar">
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid flex-lg-column align-items-stretch">
            <h4 class="mt-2 text-light">Staff Panel</h4>
            <button class="navbar-toggler shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#staffDropdown" aria-controls="staffDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse flex-column align-items-stretch mt-2" id="staffDropdown">
                <ul class="nav nav-pills flex-column">
                    <li class="nav-item">
                        <a href="staffdash.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <button class="btn txt-white px-3 w-100 shadow-none text-start d-flex align-items-center justify-content-between" type="button" data-bs-toggle="collapse" data-bs-target="#bookinglinks">
                        <span class="text-white">Bookings </span>
                        <span><i class= "bi bi-caret-down-fill"></i></span>
                        </button>
                        <div class="collapse show px-3 small mb-1" id="bookinglinks">
                           <ul class="nav nav-pills flex-column rounded border border-secondary">
                                <li class="nav-item">
                                    <a class="nav-link" href="newbooking.php">New Bookings</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="bookingrecord.php">Booking Record</a> 
                                </li>
                            </ul>
                        </div>   
                    </li>
                    <li class="nav-item">
                    <a href="hosteler.php">Hosteler</a>
                    </li>
                    <li class="nav-item">
                    <a href="services.php">Services</a>
                    </li>
                    <li class="nav-item">
                        <a href="feedback.php">Feedback</a>
                    </li>
                    <li class="nav-item">
                        <a href="fee.php">Fee</a>
                    </li>
                    <li class="nav-item">
                        <a href="reports.php">Reports</a>
                    </li>
                    <li class="nav-item">
                        <a href="visitorform.php">Visitors Record</a>
                     </li>
                    <li class="nav-item">
                        <button class="btn"><a href="../index.php">LOG OUT</a></button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>

