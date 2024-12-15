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
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
        <!-- Custom CSS -->
        <style>
         body {
             background-color: #f5f5f5;
             font-family: Arial, sans-serif;
         }
 
        
 
        h1, h2{
            color: #2c3e50;
        }
        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        input[type="text"], input[type="number"], select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            /* Make the text style consistent */
            font-size: 16px;
            color: #333;
            box-sizing: border-box;
        }
        select {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-color: #fff;
        }
        input[type="submit"] {
            background-color: #3498db;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        input[type="submit"]:hover {
            background-color: #2980b9;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #3498db;
            color: #fff;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .action-links a {
            display: inline-block;
            padding: 5px 10px;
            margin: 2px;
            text-decoration: none;
            color: #fff;
            border-radius: 3px;
            transition: background-color 0.3s;
        }
        .update-link {
            background-color: #2ecc71;
        }
        .update-link:hover {
            background-color: #27ae60;
        }
        .delete-link {
            background-color: #e74c3c;
        }
        .delete-link:hover {
            background-color: #c0392b;
        }
        #message{
           text-align: center;
           font-size: 1.2em;
        }
        

         
    </style>
</head>
<body> 
<div class="container-fluid m-0 ">
  <div class="row">
  <!-- Sidebar -->
  <?php require('inc/sideMenu.php'); ?>  
  <div class="col-md-10 content-wrapper py-4 px-4">
     <h3>Add New Room</h3>
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

     <h4>Room Details</h4>
     <table>
        <tr>
            <th>Room Number</th>
            <th>Room Type</th>
            <th>Room Price</th>
            <th>Action</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td id='rno_" . $row["rid"] . "'>" . $row["rno"] . "</td>";
                echo "<td id='rtype_" . $row["rid"] . "'>" . $row["rtype"] . "</td>";
                echo "<td id='rprice_" . $row["rid"] . "'> रु " . number_format($row["rprice"], 2) . "</td>";
                echo "<td class='action-links'>
                    <a href='#' id='edit_" . $row["rid"] . "' class='update-link' onclick='enableEdit(" . $row["rid"] . ")'>Edit</a>
                    <a href='#' id='save_" . $row["rid"] . "' class='update-link' onclick='saveEdit(" . $row["rid"] . ")' style='display:none;'>Save</a>
                    <a href='?delete=" . $row["rid"] . "' class='delete-link' onclick='return confirm(\"Are you sure you want to delete this room?\");'>Delete</a>
                </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No rooms found</td></tr>";
        }
        ?>
    </table>
    </div>
 </div>
</div>
</body>
</html>

<?php
$conn->close();
?>

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
    var rprice = document.getElementById('rprice_' + rid).innerText.replace('रु ', '').replace(',', '');

    // Send AJAX request to update the room
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