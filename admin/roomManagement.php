<?php
session_start(); // Start the session
require('inc/db.php');

// Handle deletion of a room
if (isset($_GET['delete'])) {
    $room_id = $_GET['delete'];
    $delete_sql = "DELETE FROM room WHERE rid = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    
    if ($delete_stmt === false) {
        die("Error preparing delete statement: " . $conn->error);
    }

    $delete_stmt->bind_param("i", $room_id);
    if ($delete_stmt->execute()) {
        $_SESSION['success_message'] = "Room deleted successfully.";
    } else {
        $_SESSION['error_message'] = "Error deleting room: " . $delete_stmt->error;
    }
    $delete_stmt->close();

    // Redirect to the same page
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Insert new room
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add'])) {
    // Check if the room number already exists
    $room_number = $_POST['room_number'];
    $check_sql = "SELECT * FROM room WHERE rno = ?";
    $check_stmt = $conn->prepare($check_sql);

    if ($check_stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    $check_stmt->bind_param("s", $room_number);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $_SESSION['error_message'] = "The room already exists.";
    } else {
        // Insert new room without features
        $stmt = $conn->prepare("INSERT INTO room (rno, rtype, rprice) VALUES (?, ?, ?)");
        
        if ($stmt === false) {
            die("Error preparing insert statement: " . $conn->error);
        }

        $room_type = $_POST['room_type'];
        $room_price = $_POST['room_price'];

        $stmt->bind_param("ssd", $room_number, $room_type, $room_price);

        if ($stmt->execute()) {
            // Store features in session
            if (isset($_POST['features'])) {
                $_SESSION['room_features'][$room_number] = $_POST['features'];
            }
            $_SESSION['success_message'] = "New room added successfully.";
        } else {
            $_SESSION['error_message'] = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
    $check_stmt->close();

    // Redirect to the same page
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch all rooms in ascending order
$sql = "SELECT * FROM room ORDER BY rno ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Management</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/roommanagement.css"> <!-- Link to external CSS -->

    <script src="js/roommanagement.js" defer></script> <!-- Link to external JavaScript -->
    <script>
        function enableEdit(rid) {
            // Enable editing for the selected room
            document.getElementById('rno_' + rid).contentEditable = true;
            document.getElementById('rtype_' + rid).contentEditable = true;
            document.getElementById('rprice_' + rid).contentEditable = true;

            // Change the edit link to save link
            document.getElementById('edit_' + rid).style.display = 'none';
            document.getElementById('save_' + rid).style.display = 'inline';
        }

        function saveEdit(rid) {
            // Get the updated values
            var rno = document.getElementById('rno_' + rid).innerText;
            var rtype = document.getElementById('rtype_' + rid).innerText;
            var rprice = document.getElementById('rprice_' + rid).innerText.replace('Rs  ', '');

            // Send the updated values to the server using AJAX
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "update_room.php", true); // Create a new PHP file for updating
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // Handle the response if needed
                    location.reload(); // Reload the page to see the changes
                }
            };
            xhr.send("rid=" + rid + "&rno=" + encodeURIComponent(rno) + "&rtype=" + encodeURIComponent(rtype) + "&rprice=" + encodeURIComponent(rprice));
        }
    </script>
</head>
<body>
<div class="container-fluid">
    <div class="row">
         <!-- Sidebar -->
         <div class="sidebar">
            <?php require('inc/sideMenu.php'); ?>
        </div>

        <div class="col-md-10 content-wrapper">
            <h4>Add New Room</h4>
            <?php
            // Display session messages
            if (isset($_SESSION['success_message'])) {
                echo "<div class='alert alert-success'>" . $_SESSION['success_message'] . "</div>";
                unset($_SESSION['success_message']); // Clear the message after displaying
            }

            if (isset($_SESSION['error_message'])) {
                echo "<div class='alert alert-danger'>" . $_SESSION['error_message'] . "</div>";
                unset($_SESSION['error_message']); // Clear the message after displaying
            }
            ?>
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <label for="room_number">Room Number</label>
                <input type="text" id="room_number" name="room_number" placeholder="Room Number" required value="<?php echo isset($room_to_update) ? $room_to_update['rno'] : ''; ?>" onkeyup="checkRoomNumber()">
                <div id="room_check_message" style="color: red; font-size: small;"></div>

                <!-- Dropdown for Room Type -->
                <select name="room_type" required>
                    <option value="" disabled selected>Select Room Type</option>
                    <option value="Single">Single</option>
                    <option value="Double">Double</option>
                    <option value="Triple">Triple</option>
                </select>

                <input type="number" name="room_price" step="0.01" placeholder="Room Price" required value="<?php echo isset($room_to_update) ? $room_to_update['rprice'] : ''; ?>">

                <input type="submit" name="add" value="Add Room">
            </form>

            <h4 class="mt-5">Room Details</h4>
            <div style="max-height: 300px; overflow-y: auto;"> <!-- Add scrollable area -->
                <table class="table">
                    <thead>
                        <tr>
                            <th>Room Number</th>
                            <th>Room Type</th>
                            <th>Room Price</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td id='rno_" . $row["rid"] . "'>" . $row["rno"] . "</td>";
                                echo "<td id='rtype_" . $row["rid"] . "'>" . $row["rtype"] . "</td>";
                                echo "<td id='rprice_" . $row["rid"] . "'>Rs  " . number_format($row["rprice"], 2) . "</td>";
                                echo "<td class='action-links'>
                                    <a href='#' id='edit_" . $row["rid"] . "' class='update-link' onclick='enableEdit(" . $row["rid"] . "); return false;'>Edit</a>
                                    <a href='#' id='save_" . $row["rid"] . "' class='update-link btn-save' onclick='saveEdit(" . $row["rid"] . "); return false;' style='display:none;'>Save</a>
                                    <a href='?delete=" . $row["rid"] . "' class='delete-link' onclick='return confirm(\"Are you sure you want to delete this room?\");'>Delete</a>
                                </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>No rooms found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    // Auto-dismiss messages after 3 seconds
    setTimeout(function() {
        var alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            alert.style.display = 'none';
        });
    }, 3000);
</script>

</body>
</html>