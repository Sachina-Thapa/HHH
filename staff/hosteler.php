<?php
require('inc/db.php');

// Fetch hostelers data
$stmt = $mysqli->prepare("SELECT * FROM hostelers");
$stmt->execute();
$result = $stmt->get_result();
?>

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
                    <input type="text" class="form-control" id="search-user" placeholder="Search user..." oninput="if(this.value === '') get_hosteler(); else search_hosteler(this.value)" 
                    aria-label="Search hosteler by name">
                </div>

                <!-- Hosteler Table -->
                <div class="card border shadow-sm mb-4">
                    <div class="card-body">
                    <div style="overflow-x: auto; white-space: nowrap;">
                        <table class="table table-hover border text-center">
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
                                <?php
                                $i = 1;
                                while ($row = $result->fetch_assoc()) {
                                    echo "
                                    <tr>
                                        <td>$i</td>
                                        <td>{$row['name']}</td>
                                        <td>{$row['email']}</td>
                                        <td>{$row['phone_number']}</td>
                                        <td>{$row['address']}</td>
                                        <td>{$row['date_of_birth']}</td>
                                        <td>{$row['status']}</td>
                                        <td>{$row['created_at']}</td>
                                        <td>
                                            <button onclick='vhosteler({$row['id']})' class='btn btn-info btn-sm'>View</button>
                                        </td>
                                    </tr>
                                    ";
                                    $i++;
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    </div>
                </div>

                <!-- Hosteler Details Modal -->
                <div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="detailsModalLabel">Hosteler Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p><strong>ID:</strong> <span id="hosteler-id"></span></p>
                                <p><strong>Name:</strong> <span id="hosteler-name"></span></p>
                                <p><strong>Email:</strong> <span id="hosteler-email"></span></p>
                                <p><strong>Phone Number:</strong> <span id="hosteler-phone_number"></span></p>
                                <p><strong>Address:</strong> <span id="hosteler-address"></span></p>
                                <p><strong>Status:</strong> <span id="hosteler-status"></span></p>
                                <p><strong>Date of Birth:</strong> <span id="hosteler-date_of_birth"></span></p>
                                <p><strong>Created At:</strong> <span id="hosteler-created_at"></span></p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div> 
        </div>
    </div> 

    <!-- Include necessary scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="scripts/hosteler.js"></script> <!-- Ensure this path is correct -->
</body>
</html>