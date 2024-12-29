<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hosteler Management</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .sidebar {
            min-height: 100vh;
            padding: 0;
            margin: 0;
            background-color: #343a40;
            color: white;
        }
        .main-content {
            margin-left: 220px;
            padding: 20px;
        }
        #hosteler-details {
            margin-top: 20px; /* Space between first and second tables */
        }
        #hosteler-details table th {
            width: 30%;
            text-align: left;
            background-color: #f8f9fa;
        }
        #hosteler-details table td {
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row no-gutters">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <?php require('inc/sidemenu.php'); ?>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content p-4" id="main-content">
                <h3 class="mb-4">HOSTELERS</h3>
                
                <!-- Search Bar -->
                <div class="text-end mb-4">
                    <input type="text" class="form-control" id="search-user" placeholder="Search user..." oninput="search_hosteler(this.value)">
                </div>

                <!-- Hosteler Table -->
                <div class="card border shadow-sm mb-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover border text-center" style="min-width: 1300px;">
                                <thead>
                                    <tr class="bg-dark text-light">
                                        <th scope="col">S.N.</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Email</th>
                                        <th scope="col">Phone No.</th>
                                        <th scope="col">Location</th>
                                        <th scope="col">DOB</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="hosteler-data">
                                    <!-- Dynamic data will be populated here by the JavaScript functions -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Hosteler Details Section -->
                <div id="hosteler-details" class="card border shadow-sm mt-4">
                    <div class="card-body">
                        <h5 class="mb-3">Hosteler Details</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th scope="row">ID</th>
                                        <td id="hosteler-id"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Name</th>
                                        <td id="hosteler-name"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Email</th>
                                        <td id="hosteler-email"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Phone</th>
                                        <td id="hosteler-phone"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Location</th>
                                        <td id="hosteler-location"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Date of Birth</th>
                                        <td id="hosteler-dob"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Status</th>
                                        <td id="hosteler-status"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Created At</th>
                                        <td id="hosteler-created-at"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include necessary scripts -->
    <?php @include('inc/scripts.php'); ?>
    <script src="scripts/hosteler.js"></script>
</body>
</html>
