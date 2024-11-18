<?php
require('../admin/inc/db.php');

// Check if a form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $vid = $_POST['vid'];
    $action = $_POST['action'];

    // Determine the new status based on the action
    $new_status = ($action === 'accept') ? 'accepted' : 'declined';

    // Prepare the SQL statement to update the status
    $sql = "UPDATE visitorform SET status = ? WHERE vid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $new_status, $vid);

    // Execute the statement
    if ($stmt->execute()) {
        // Status updated successfully
    } else {
        echo "Error updating status: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}

// Fetch visitor data
$sql = "
    SELECT v.hid, h.name AS hosteler_name, v.vid, v.vname, v.relation, v.reason, v.days, v.status 
    FROM visitorform v 
    JOIN hostelers h ON v.hid = h.id"; // Adjusted the column name
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Form</title>
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

        .accepted {
            background-color: #d4edda; /* Light green background for accepted */
        }

        .declined {
            opacity: 0.5; /* Less transparent for declined */
        }
    </style>
</head>
<body>
    <?php require('inc/sidemenu.php'); ?>

    <div class="content">
        <div class="container mt-4">
            <h3>Visitor Information</h3>
            <div class="table-responsive">
                <table class="table table-hover text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>Hosteler Id</th>
                            <th>Hosteler Name</th>
                            <th>Visitor ID</th>
                            <th>Name</th>
                            <th>Relation</th>
                            <th>Reason</th>
                            <th>Day</th>
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

                                    if ($row['status'] === 'accepted') {
                                        $row_class = 'accepted';
                                        $action_message = 'Accepted';
                                        } elseif ($row['status'] === 'declined') {
                                        $row_class = 'declined';
                                        $action_message = 'Declined';
                                    }

                                    echo "<tr class='$row_class'>
                                            <td>" . htmlspecialchars($row["hid"]) . "</td>
                                            <td>" . htmlspecialchars($row["hosteler_name"]) . "</td>
                                            <td>" . htmlspecialchars($row["vid"]) . "</td>
                                            <td>" . htmlspecialchars($row["vname"]) . "</td>
                                            <td>" . htmlspecialchars($row["relation"]) . "</td>
                                            <td>" . htmlspecialchars($row["reason"]) . "</td>
                                            <td>" . htmlspecialchars($row["days"]) . "</td>
                                            <td>";
                                    if ($action_message) {
                                        echo $action_message; // Show the action message
                                    } else {
                                        echo "<form method='post' action=''>
                                                <input type='hidden' name='vid' value='" . htmlspecialchars($row['vid']) . "'>
                                                <button type='submit' name='action' value='accept' class='btn btn-success'>Accept</button>
                                                <button type='submit' name='action' value='decline' class='btn btn-danger'>Decline</button>
                                            </form>";
                                    }
                                    echo "</td>
                                        </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='8'>No visitors found.</td></tr>";
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