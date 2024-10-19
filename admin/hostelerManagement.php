<?php
$servername = "localhost";
$username = "root";
$password = '';
$dbname = "hhh";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
//$conn = new mysqli( "localhost", "root", "", "hhh");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Insert new room
if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
        echo "Updated successfully";
    } else {
        echo "Error updating record: " . $stmt->error;
    }
    $stmt->close();
    exit();
}



// Delete room
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
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 100%;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        
        /*sidebar css*/
        .sidebar {
            margin:0px;
            height: 100vh;
            background-color: #343a40;
            padding-top: 10px;
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
        .logout-btn {
            margin-top: 20px;
            background-color: #f8f9fa;
            border: none;
            color: #000;
            padding: 10px;
        }
    </style>
</head>
<body> 
<div class="container-fluid m-0">
  <div class="row">
     <!-- Sidebar -->
     <div class="col-md-2 sidebar">
     <h3 class="text-white text-center">Her Home Hostel</h3>
                <a href="addash.php">Dashboard</a>
                <a href="roomManagement.php">Room Management</a>
                <a href="staffmanagement.php">Staff management</a>
                <a href="hostelerManagement.php">Hosteller</a>
<<<<<<< HEAD
                <a href="usersquery.php">Queries</a>
=======
>>>>>>> 5d4f584d001869c933183a50edd24d3ba2bd99bc
                <a href="setting.php">Settings</a>
                <button class="btn w-100" ><a href="../index.php">LOG OUT</a></button>
            </div>
      <div class="col-md-10">
         <h2 class="mt-4 mb-4">Hosteler Management</h2>
        
         <!-- Form to add new hosteler -->
         <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="mb-4">
             <div class="row g-3">
                 <div class="col-md-3">
                     <input type="text" class="form-control" name="name" placeholder="Name" required>
                 </div>
                 <div class="col-md-3">
                     <input type="tel" class="form-control" name="phone_number" placeholder="Phone Number" required>
                 </div>
                 <div class="col-md-3">
                     <input type="text" class="form-control" name="address" placeholder="Address" required>
                 </div>
                 <div class="col-md-3">
                     <input type="email" class="form-control" name="email" placeholder="Email" required>
                 </div>
             </div>
             <div class="mt-3">
                 <button type="submit" class="btn btn-primary">Add Hosteler</button>
             </div>
         </form>

         <!-- Table to display hostelers -->
         <div class="table-responsive">
             <table class="table table-striped table-hover">
                 <thead class="table-dark">
                     <tr>
                         <th>ID</th>
                         <th>Name</th>
                         <th>Phone Number</th>
                         <th>Address</th>
                         <th>Email</th>
                         <th>Actions</th>
                     </tr>
                 </thead>
                 <tbody>
                     <!-- PHP code to populate table rows goes here -->
                 </tbody>
             </table>
         </div>
     </div>
  </div>
</div>
</body>
</html>

<?php
$conn->close();
?>
