<?php
session_start();
require('../admin/inc/db.php'); // Ensure you have the correct database connection
require('inc/hsidemenu.php'); // Include the sidebar if needed

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to login page or show an error
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username']; // Get the logged-in username
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Custom CSS -->
    <style>
         body {
             background-color: #f5f5f5;
             font-family: Arial, sans-serif;
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

<div class="container-fluid col-md-10 p-4">
    <div class="row">
        <div class="col-md-10">
            <h2 class="mt-3">My Account</h2>

            <!-- Table Query Section -->
            <?php
            // Fetch the logged-in user's information
            $sql = "SELECT id, name, phone_number, address, email, status 
                    FROM hostelers 
                    WHERE username = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if (!$result) {
                echo "Query error: " . $conn->error;
            }
            ?>
    <div id="message">
        <?php
        if (isset($error_message)) {
            echo "<p style='color: red;'>$error_message</p>";
        }
        if (isset($_GET['deleted']) && $_GET['deleted'] == 1) {
            echo "<p style='color: green;'>Your ID is deleted successfully</p>";
        }
        if (isset($_GET['updated']) && $_GET['updated'] == 1) {
            echo "<p style='color: green;'>Information updated successfully</p>";
        }
        ?>
     </div>
            <!-- Table Display Section -->
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Phone Number</th>
                            <th>Address</th>
                            <th>Email</th>
                            <th>Action</th>
                        </tr>
                        
                    </thead>
                    <tbody>
                    <?php
                    if ($result && $result->num_rows > 0) {
                        $row = $result->fetch_assoc(); // Fetch the single row for the logged-in user
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['phone_number']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['address']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                        echo "<td class='action-links'>
                    <a href='#' id='edit_" . $row["id"] . "' class='update-link' onclick='enableEdit(" . $row["id"] . ")'>Edit</a>
                    <a href='#' id='save_" . $row["id"] . "' class='update-link' onclick='saveEdit(" . $row["id"] . ")' style='display:none;'>Save</a>
                    <a href='?delete=" . $row["id"] . "' class='delete-link' onclick='return confirm(\"Are you sure you want your id?\");'>Delete</a>
                </td>";
                        
                        echo "</tr>";
                    } else {
                        echo "<tr><td colspan='5' class='text-center'>No information found</td></tr>";
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<script>
function enableEdit(id) {
    document.getElementById('name' + id).contentEditable = true;
    document.getElementById('phone_number' + id).contentEditable = true;
    document.getElementById('address' + id).contentEditable = true;
    document.getElementById('email' + id).contentEditable = true;
    document.getElementById('edit_' + id).style.display = 'none';
    document.getElementById('save_' + id).style.display = 'inline';
}
function saveEdit(id) {
    var name= document.getElementById('name' + id).innerText;
    var phone_number= document.getElementById('phone_number' + id).innerText;
    var address= document.getElementById('address' + id).innerText;
    var email= document.getElementById('email' + id).innerText;

    // Send AJAX request to update the room
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '<?php echo $_SERVER["PHP_SELF"]; ?>', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (this.status == 200) {
            document.getElementById('name' + id).contentEditable = true;
    document.getElementById('phone_number' + id).contentEditable = true;
    document.getElementById('address' + id).contentEditable = true;
    document.getElementById('email' + id).contentEditable = true;
    document.getElementById('edit_' + id).style.display = 'none';
    document.getElementById('save_' + id).style.display = 'inline';
}
    };
    xhr.send('update=1&id=' + id + '&name=' + encodeURIComponent(name) + '&phone_number=' + encodeURIComponent(phone_number) + '&address=' + encodeURIComponent(address) +'&email=' +encodeURIComponent(email));
}
<?php
$conn->close();
?>