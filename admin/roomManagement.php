<?php
    require('inc/db.php');

// Insert new room
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add'])) {
    $stmt = $conn->prepare("INSERT INTO room (rno, rtype, rprice) VALUES (?, ?, ?)");
    $stmt->bind_param("ssd", $room_number, $room_type, $room_price);
    
    $room_number = $_POST['room_number'];
    $room_type = $_POST['room_type'];
    $room_price = $_POST['room_price'];

    if ($stmt->execute()) {
        header("Location: ".$_SERVER['PHP_SELF']."?success=1");
        exit();
    } else {
        $error_message = "Error: " . $stmt->error;
    }
    $stmt->close();
}


// Update room
if (isset($_GET['update'])) {
    $rid = $_GET['update'];
    $fetch_sql = "SELECT * FROM room WHERE rid = $rid";
    $fetch_result = $conn->query($fetch_sql);
    $room_to_update = $fetch_result->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $rid = $_POST['rid'];
    $room_number = $_POST['rno'];
    $room_type = $_POST['rtype'];
    $room_price = $_POST['rprice'];

    $update_sql = "UPDATE room SET rno=?, rtype=?, rprice=? WHERE rid=?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ssdi", $room_number, $room_type, $room_price, $rid);

    if ($stmt->execute()) {
        header("Location: ".$_SERVER['PHP_SELF']."?updated=1");
        exit();
    } else {
        echo "Error updating record: " . $stmt->error;
    }
    $stmt->close();
}// Delete room
if (isset($_GET['delete'])) {
    $rid = $_GET['delete'];
    $delete_sql = "DELETE FROM room WHERE rid = $rid";
    if ($conn->query($delete_sql) === TRUE) {
        // Reset auto-increment
        $reset_sql = "ALTER TABLE room AUTO_INCREMENT = 1";
        $conn->query($reset_sql);
        header("Location: ".$_SERVER['PHP_SELF']."?deleted=1");
        exit();
    } else {
        $error_message = "Error deleting record: " . $conn->error;
    }
}

// Fetch all rooms
$sql = "SELECT * FROM room";
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

    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Roboto', sans-serif;
        }

        h3, h4 {
            color: #343a40;
        }

        .content-wrapper {
            background: #ffffff;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            padding: 20px;
        }

        form {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        input[type="text"], input[type="number"], select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-size: 16px;
        }

        input[type="submit"] {
            background-color: #0d6efd;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #0a58ca;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }

        th {
            background-color: #343a40;
            color: #ffffff;
        }

        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .action-links a {
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 5px;
            margin-right: 5px;
            color: #ffffff;
            font-size: 14px;
            transition: background-color 0.3s;
        }

        .update-link {
            background-color: #198754;
        }

        .update-link:hover {
            background-color: #157347;
        }

        .delete-link {
            background-color: #dc3545;
        }

        .delete-link:hover {
            background-color: #bb2d3b;
        }

        #message {
            text-align: center;
            font-size: 1.2em;
            margin-bottom: 20px;
        }

        .container-fluid {
            padding: 30px;
        }

        .btn-save {
            display: none;
            background-color: #0dcaf0;
        }

        .btn-save:hover {
            background-color: #0b97a9;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
         <!-- Sidebar -->
         <div class="sidebar">
            <?php require('inc/sideMenu.php'); ?>
        </div>

        <div class="col-md-10 content-wrapper">
            <!-- <h3 class="mb-4">Room Management</h3> -->

            <h4>Add New Room</h4>
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <?php if (isset($room_to_update)) echo "<input type='hidden' name='rid' value='" . $room_to_update['rid'] . "'>"; ?>
                <input type="text" name="room_number" placeholder="Room Number" required value="<?php echo isset($room_to_update) ? $room_to_update['rno'] : ''; ?>">

                <!-- Dropdown for Room Type -->
                <select name="room_type" required>
                    <option value="" disabled selected>Select Room Type</option>
                    <option value="Single" <?php if (isset($room_to_update) && $room_to_update['rtype'] == 'Single') echo 'selected'; ?>>Single</option>
                    <option value="Double" <?php if (isset($room_to_update) && $room_to_update['rtype'] == 'Double') echo 'selected'; ?>>Double</option>
                    <option value="Triple" <?php if (isset($room_to_update) && $room_to_update['rtype'] == 'Triple') echo 'selected'; ?>>Triple</option>
                </select>

                <input type="number" name="room_price" step="0.01" placeholder="Room Price" required value="<?php echo isset($room_to_update) ? $room_to_update['rprice'] : ''; ?>">
                <input type="submit" name="add" value="Add Room">
            </form>

            <div id="message">
                <?php
                if (isset($_GET['success']) && $_GET['success'] == 1) {
                    echo "<p style='color: green;'>New room added successfully</p>";
                }
                if (isset($error_message)) {
                    echo "<p style='color: red;'>$error_message</p>";
                }
                if (isset($_GET['deleted']) && $_GET['deleted'] == 1) {
                    echo "<p style='color: green;'>Room deleted successfully and IDs reset</p>";
                }
                if (isset($_GET['updated']) && $_GET['updated'] == 1) {
                    echo "<p style='color: green;'>Room updated successfully</p>";
                }
                ?>
            </div>

            <h4 class="mt-5">Room Details</h4>
            <table>
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
                            echo "<td id='rprice_" . $row["rid"] . "'>₹ " . number_format($row["rprice"], 2) . "</td>";
                            echo "<td class='action-links'>
                                <a href='#' id='edit_" . $row["rid"] . "' class='update-link' onclick='enableEdit(" . $row["rid"] . ")'>Edit</a>
                                <a href='#' id='save_" . $row["rid"] . "' class='update-link btn-save' onclick='saveEdit(" . $row["rid"] . ")'>Save</a>
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

<script>
function enableEdit(rid) {
    document.getElementById('rno_' + rid).contentEditable = true;
    document.getElementById('rtype_' + rid).contentEditable = true;
    document.getElementById('rprice_' + rid).contentEditable = true;
    document.getElementById('edit_' + rid).style.display = 'none';
    document.getElementById('save_' + rid).style.display = 'inline';
}

function saveEdit(rid) {
    var rno = document.getElementById('rno_' + rid).innerText;
    var rtype = document.getElementById('rtype_' + rid).innerText;
    var rprice = document.getElementById('rprice_' + rid).innerText.replace('₹ ', '').replace(',', '');

    var xhr = new XMLHttpRequest();
    xhr.open('POST', '<?php echo $_SERVER["PHP_SELF"]; ?>', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (this.status == 200) {
            document.getElementById('rno_' + rid).contentEditable = false;
            document.getElementById('rtype_' + rid).contentEditable = false;
            document.getElementById('rprice_' + rid).contentEditable = false;
            document.getElementById('edit_' + rid).style.display = 'inline';
            document.getElementById('save_' + rid).style.display = 'none';
        }
    };
    xhr.send('update=1&rid=' + rid + '&rno=' + encodeURIComponent(rno) + '&rtype=' + encodeURIComponent(rtype) + '&rprice=' + encodeURIComponent(rprice));
}
</script>

</body>
</html>
