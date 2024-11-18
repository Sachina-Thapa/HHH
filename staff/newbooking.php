<?php
require('inc/sidemenu.php');
require('inc/db.php');

// Query to get the bookings data and join with the hostelers and room tables
$query = "
    SELECT 
        b.bid, 
        b.bookingdate, 
        b.bstatus AS booking_status, 
        b.rid, 
        h.name AS hosteler_name, 
        h.phone_number, 
        h.email, 
        h.address,
        r.rtype AS room_type, 
        r.rprice AS room_price,
        b.id AS hosteler_id
    FROM booking b
    JOIN hostelers h ON b.id = h.id
    JOIN room r ON b.rid = r.rid
";

$result = $mysqli->query($query);

if ($result === false) {
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

        .main-content {
            margin-left: 220px;
            padding: 20px;
        }
    </style>
</head>
<body>

<div class="main-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 ms-auto p-4 overflow-hidden">
                <h3 class="mb-4">New Bookings</h3>
                <div class="card border-6 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover border text-center" style="min-width: 1300px;">
                                <thead>
                                    <tr class="bg-dark text-light">
                                        <th scope="col">S.N.</th>
                                        <th scope="col">Hosteler Details</th>
                                        <th scope="col">Room Details</th>
                                        <th scope="col">Booking Details</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result->num_rows > 0): ?>
                                        <?php $serial_number = 1; ?>
                                        <?php while ($row = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= $serial_number++; ?></td>
                                                <td>
                                                    <strong><?= htmlspecialchars($row['hosteler_name']) ?></strong><br>
                                                    Email: <?= htmlspecialchars($row['email']) ?><br>
                                                    Phone: <?= htmlspecialchars($row['phone_number']) ?><br>
                                                    Address: <?= htmlspecialchars($row['address']) ?>
                                                </td>
                                                <td>
                                                    Type: <?= htmlspecialchars($row['room_type']) ?><br>
                                                    Price: $<?= htmlspecialchars($row['room_price']) ?>
                                                </td>
                                                <td><?= htmlspecialchars($row['bookingdate']) ?></td>
                                                <td><?= ucfirst(htmlspecialchars($row['booking_status'])) ?></td>
                                                <td>
                                                    <?php if ($row['booking_status'] == 'pending'): ?>
                                                        <button 
                                                            onclick="confirmBooking(<?= $row['bid'] ?>, <?= $row['hosteler_id'] ?>)" 
                                                            class="btn btn-success btn-sm">
                                                            Confirm
                                                        </button>
                                                        <button 
                                                            onclick="cancelBooking(<?= $row['bid'] ?>, <?= $row['hosteler_id'] ?>)" 
                                                            class="btn btn-danger btn-sm">
                                                            Cancel
                                                        </button>
                                                    <?php else: ?>
                                                        <em><?= ucfirst(htmlspecialchars($row['booking_status'])) ?></em>
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
<script src="scripts/newbooking.js"></script>
</body>
</html>
