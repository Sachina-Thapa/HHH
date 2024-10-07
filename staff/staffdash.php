<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Panel - Dashboard </title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .sidebar {
            height: 100vh;
            background-color: #343a40;
            padding-top: 10px;
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
        .logout-btn {
            margin-top: 20px;
            background-color: #f8f9fa;
            border: none;
            color: #000;
            padding: 10px;
        }
        .table thead {
            background-color: #000;
            color: #fff;
        }
        .table th, .table td {
            text-align: center;
        }
    </style>
</head>
    </div>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <h3 class="text-white text-center">Her Home Hostel</h3>
                <a href="#">Dashboard</a>
                <a href="#">Manage Hosteler</a>
                <a href="#">Manage Booking</a>
                <a href="#">View Feedback</a>
                <a href="#">Manage Visitor Form</a>
                <a href="#">Services</a>
                <a href="#">Fee</a>
                <a href="#">Report</a>
                <button class="logout-btn w-100">LOG OUT</button>
            </div>
            <!-- Main content -->
            <div class="col-md-10">
                <h3 class="ab-0 h-font">Staff Pannel</h3>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
