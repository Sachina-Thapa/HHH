<?php
require('../admin/inc/db.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);
// Fetch only pending booking data
$query = "
    SELECT 
        b.bid, 
        b.bookingdate AS gdate, 
        b.bstatus AS booking_status, 
        b.rno, 
        h.name AS hosteler_name, 
        h.phone_number, 
        h.email, 
        h.address,
        r.rtype AS room_type, 
        r.rprice AS room_price,
        h.id AS hosteler_id 
    FROM booking b 
    JOIN hostelers h ON b.id = h.id 
    JOIN room r ON b.rno = r.rno 
    WHERE b.bstatus = 'pending';  -- Match the exact case
";

$result = $conn->query($query);
if ($result === false) {
    echo "Error: " . $conn->error;  
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Bookings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="inc/newbooking.css">
</head>
<body>
    <?php require('inc/sidemenu.php'); ?>

    <div class="content">
        <div class="container mt-4">
            <h3>New Bookings</h3>
            <div class="table-responsive">
                <table class="table table-hover text-center" id="bookings-table">
                    <thead class="table-dark">
                        <tr>
                            <th>Booking ID</th>
                            <th>Hosteler Name</th>
                            <th>Hosteler Phone</th>
                            <th>Hosteler Email</th>
                            <th>Hosteler Address</th>
                            <th>Room Type</th>
                            <th>Booking Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                            <tbody>
           <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    ?>
                    <tr class='clickable-row' data-id='<?php echo htmlspecialchars($row['hosteler_id']); ?>'>
                        <td><?php echo htmlspecialchars($row['bid']); ?></td>
                        <td><?php echo htmlspecialchars($row['hosteler_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['address']); ?></td>
                        <td><?php echo htmlspecialchars($row['room_type']); ?></td>
                        <td><?php echo htmlspecialchars($row['gdate']); ?></td>
                        <td>
                            <button type='button' onclick='confirmBooking(<?php echo $row['bid']; ?>, <?php echo $row['hosteler_id']; ?>)' class='btn btn-success'>Confirm</button>
                            <button type='button' onclick='cancelBooking(<?php echo $row['bid']; ?>, <?php echo $row['hosteler_id']; ?>)' class='btn btn-danger'>Cancel</button>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                echo "<tr><td colspan='8'>No pending bookings found.</td></tr>";
            }
            ?>
        </tbody>

                </table>
            </div>
        </div>
    </div>

    <script src="scripts/newbooking.js"></script>
</body>
</html>
