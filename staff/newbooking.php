<?php
require('../admin/inc/db.php');

// Check if a form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bid = $_POST['bid'];
    $action = $_POST['action'];

    // Determine the new status based on the action
    $new_status = ($action === 'confirm') ? 'confirmed' : 'canceled';

    // Prepare the SQL statement to update the status
    $sql = "UPDATE booking SET bstatus = ? WHERE bid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $new_status, $bid);

    // Execute the statement
    if ($stmt->execute()) {
        // Redirect to the same page to avoid form resubmission
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        // Handle error if needed
        error_log("Error updating status: " . $stmt->error);
    }

    // Close the statement
    $stmt->close();
}

// Fetch booking data
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
        h.id AS hosteler_id
    FROM booking b
    JOIN hostelers h ON b.id = h.id  -- Ensure this is the correct foreign key
    JOIN room r ON b.rid = r.rid
";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            margin: 0; /* Remove default margin */
        }
        
        .content {
            margin-left: 200px; /* Same as sidebar width */
            padding: 20px; /* Add padding to the content area */
            width: calc(100% - 200px); /* Adjust width to fill the remaining space */
        }

        .confirmed {
            background-color: #d4edda; /* Light green background for confirmed */
        }

        .canceled {
            opacity: 0.5; /* Less transparent for canceled */
        }
    </style>
</head>
<body>
    <?php require('inc/sidemenu.php'); ?>

    <div class="content">
        <div class="container mt-4">
            <h3>New Bookings</h3>
            <div class="table-responsive">
                <table class="table table-hover text-center">
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
                        // Check if the query was successful
                        if ($result === false) {
                            // Output the error message
                            echo "Error: " . $conn->error;
                        } else {
                            // Check if there are results
                            if ($result->num_rows > 0) {
                                // Output data of each row
                                while($row = $result->fetch_assoc()) {
                                    // Determine the class for the row based on the status
                                    $row_class = '';
                                    $action_message = '';

                                    if ($row['booking_status'] === 'confirmed') {
                                        $row_class = 'confirmed';
                                        $action_message = 'Confirmed';
                                    } elseif ($row['booking_status'] === 'canceled') {
                                        $row_class = 'canceled';
                                        $action_message = 'Canceled';
                                    }

                                    echo "<tr class='$row_class'>
                                            <td>" . htmlspecialchars($row["bid"]) . "</td>
                                            <td>" . htmlspecialchars($row["hosteler_name"]) . "</td>
                                            <td >" . htmlspecialchars($row["phone_number"]) . "</td>
                                            <td>" . htmlspecialchars($row["email"]) . "</td>
                                            <td>" . htmlspecialchars($row["address"]) . "</td>
                                            <td>" . htmlspecialchars($row["room_type"]) . "</td>
                                            <td>" . htmlspecialchars($row["bookingdate"]) . "</td>
                                            <td>";
                                    if ($action_message) {
                                        echo $action_message; // Show the action message
                                    } else {
                                        echo "<form method='post' action=''>
                                                <input type='hidden' name='bid' value='" . htmlspecialchars($row['bid']) . "'>
                                                <button type='submit' name='action' value='confirm' class='btn btn-success'>Confirm</button>
                                                <button type='submit' name='action' value='cancel' class='btn btn-danger'>Cancel</button>
                                            </form>";
                                    }
                                    echo "</td>
                                        </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='8'>No bookings found.</td></tr>";
                            }
                        }

                        // Close the connection
                        $conn->close();
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>