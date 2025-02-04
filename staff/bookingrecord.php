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
    WHERE b.bstatus = 'confirmed' OR b.bstatus = 'canceled';

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
            margin: 0;
        }

        .content {
            margin-left: 200px;
            padding: 20px;
            width: calc(100% - 200px);
        }

        .confirmed {
            background-color: #d4edda;
        }

        .canceled {
            opacity: 0.5;
        }

        .clickable-row {
            cursor: pointer;
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
                        if ($result === false) {
                            echo "Error: " . $conn->error;
                        } else {
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $row_class = '';
                                    $action_message = '';

                                    if ($row['booking_status'] === 'confirmed') {
                                        $row_class = 'confirmed';
                                        $action_message = 'Confirmed';
                                    } elseif ($row['booking_status'] === 'canceled') {
                                        $row_class = 'canceled';
                                        $action_message = 'Canceled';
                                    }

                                    echo "<tr class='clickable-row $row_class' data-id='" . htmlspecialchars($row["hosteler_id"]) . "'>
                                            <td>" . htmlspecialchars($row["bid"]) . "</td>
                                            <td>" . htmlspecialchars($row["hosteler_name"]) . "</td>
                                            <td>" . htmlspecialchars($row["phone_number"]) . "</td>
                                            <td>" . htmlspecialchars($row["email"]) . "</td>
                                            <td>" . htmlspecialchars($row["address"]) . "</td>
                                            <td>" . htmlspecialchars($row["room_type"]) . "</td>
                                            <td>" . htmlspecialchars($row["bookingdate"]) . "</td>
                                            <td>";
                                    if ($action_message) {
                                        echo $action_message;
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
                        $conn->close();
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailsModalLabel">Hosteler Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Dynamic content will be injected here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.clickable-row').forEach(row => {
            row.addEventListener('click', function () {
                const hostelerId = this.getAttribute('data-id');

                fetch(`get_hosteler_details.php?id=${hostelerId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            alert(data.error);
                        } else {
                            displayHostelerDetails(data);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            });
        });
    });

    function displayHostelerDetails(data) {
        let modalBody = `
            <h5>Hosteler Information</h5>
            <p><strong>Name:</strong> ${data[0].hosteler_name}</p>
            <p><strong>Phone:</strong> ${data[0].phone_number}</p>
            <p><strong>Email:</strong> ${data[0].email}</p>
            <p><strong>Address:</strong> ${data[0].address}</p>
            <hr>
            <h5>Booking Details</h5>
            <ul>
        `;

        data.forEach(booking => {
            modalBody += `
                <li>
                    <strong>Booking ID:</strong> ${booking.bid}<br>
                    <strong>Room Type:</strong> ${booking.room_type}<br>
                    <strong>Room Price:</strong> ${booking.room_price}<br>
                    <strong>Booking Date:</strong> ${booking.bookingdate}<br>
                    <strong>Status:</strong> ${booking.bstatus}
                </li>
                <hr>
            `;
        });

        modalBody += `</ul>`;

        const modal = document.getElementById('detailsModal');
        modal.querySelector('.modal-body').innerHTML = modalBody;
        new bootstrap.Modal(modal).show();
    }
    </script>
</body>
</html>
