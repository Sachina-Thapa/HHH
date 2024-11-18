<?php
require('inc/sidemenu.php');
require('inc/db.php');

// Query to get the bookings data and join with the hostelers table
$query = "
    SELECT b.bid, b.bookingdate, b.status, b.rid, h.name, h.phone_number, b.hid
    FROM booking b
    JOIN hostelers h ON b.hid = h.id
";

$result = $mysqli->query($query);

if ($result === false) {
    // Handle query error, e.g., log it or display a message
    echo "Error fetching data: " . $mysqli->error;
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
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

<!-- Main Content Area -->
<div class="main-content">
    <div class="container-fluid" id="main-content">
        <div class="row">
            <div class="col-lg-17 ms-auto p-4 overflow-hidden">
                <h3 class="mb-4">New Bookings</h3>
                <div class="card border-6 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="text-end mb-4">
                            <input type="text" oninput="search_user(this.value)" class="form-control shadow-none w-25" placeholder="Search user...">
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover border text-center" style="min-width: 1300px;">
                                <thead>
                                    <tr class="bg-dark text-light">
                                        <th scope="col">S.N.</th>
                                        <th scope="col">Hosteler Details</th>
                                        <th scope="col">Room Details</th>
                                        <th scope="col">Booking Details</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result->num_rows > 0): ?>
                                        <?php while ($row = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= $row['bid'] ?></td>
                                                <td>
                                                    <strong><?= $row['hosteler_name'] ?></strong><br>
                                                    Phone: <?= $row['phone_number'] ?>
                                                </td>
                                                <td>Room ID: <?= $row['rid'] ?></td>
                                                <td><?= $row['booking_date'] ?></td>
                                                <td><?= ucfirst($row['status']) ?></td>
                                                <td>
                                                    <?php if ($row['status'] == 'pending'): ?>
                                                        <button 
                                                            onclick="confirmBooking(<?= $row['bid'] ?>, <?= $row['hid'] ?>)" 
                                                            class="btn btn-success btn-sm">
                                                            Confirm
                                                        </button>
                                                        <button 
                                                            onclick="cancelBooking(<?= $row['bid'] ?>, <?= $row['hid'] ?>)" 
                                                            class="btn btn-danger btn-sm">
                                                            Cancel
                                                        </button>
                                                    <?php else: ?>
                                                        <em><?= ucfirst($row['status']) ?></em>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6">No bookings found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>
