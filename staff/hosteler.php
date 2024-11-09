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
            </div>
        </div>
    </div>

    <!-- Include necessary scripts -->
    <?php @include('inc/scripts.php'); ?>
    <script src="scripts/hosteler.js"></script>
</body>
</html>
