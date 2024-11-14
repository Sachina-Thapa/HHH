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

// Handle the update request
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $address = $_POST['address'];
    $date_of_birth = $_POST['date_of_birth'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Handle file upload
    $target_dir = 'uploads/'; // Specify your uploads folder
    $picture_path = $_FILES['profile_picture']['name'] ? $target_dir . basename($_FILES['profile_picture']['name']) : $_POST['current_picture_path'];

    // If a new file is uploaded, move it to the uploads directory
    if ($_FILES['profile_picture']['name']) {
        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $picture_path)) {
            echo "File uploaded successfully.";
        } else {
            echo "Error uploading file.";
        }
    }

    // Prepare the update SQL statement
    $update_sql = "UPDATE hostelers SET name = ?, email = ?, phone_number = ?, picture_path = ?, address = ?, date_of_birth=?, username=?, password=? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssisssssi", $name, $email, $phone_number, $picture_path, $address, $date_of_birth, $username, $password, $id);
    
    if ($update_stmt->execute()) {
        header("Location: accounts.php?updated=1"); // Redirect after successful update
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

// Fetch the logged-in user's information
$sql = "SELECT id, name, email, phone_number, picture_path, address, date_of_birth, username, password FROM hostelers WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    echo "Query error: " . $conn->error;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
        #message {
            text-align: center;
            font-size: 1.2em;
        }
    </style>
</head>
<body>

<div class="container-fluid col-md-10 p-4">
    <div class="row">
        <div class="col -md-10">
            <h2 class="mt-3">My Account</h2>

            <div id="message">
                <?php
                if (isset($_GET['updated']) && $_GET['updated'] == 1) {
                    echo "<p style='color: green;'>Information updated successfully</p>";
                }
                ?>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Photo</th>
                            <th>Address</th>
                            <th>DOB</th>
                            <th>Username</th>
                            <th>Password</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result && $result->num_rows > 0) {
                            $row = $result->fetch_assoc(); // Fetch the single row for the logged-in user
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                            echo "<td id='name" . $row['id'] . "' contenteditable='false'>" . htmlspecialchars($row['name']) . "</td>";
                            echo "<td id='email" . $row['id'] . "' contenteditable='false'>" . htmlspecialchars($row['email']) . "</td>";
                            echo "<td id='phone_number" . $row['id'] . "' contenteditable='false'>" . htmlspecialchars($row['phone_number']) . "</td>";
                            echo "<td id='picture_path" . $row['id'] . "'><img src='" . htmlspecialchars($row['picture_path']) . "' alt='Profile Picture' width='50'></td>";
                            echo "<td id='address" . $row['id'] . "' contenteditable='false'>" . htmlspecialchars($row['address']) . "</td>";
                            echo "<td id='dob" . $row['id'] . "' contenteditable='false'>" . htmlspecialchars($row['date_of_birth']) . "</td>";
                            echo "<td id='username" . $row['id'] . "' contenteditable='false'>" . htmlspecialchars($row['username']) . "</td>";
                            echo "<td id='password" . $row['id'] . "' contenteditable='false'>" . htmlspecialchars($row['password']) . "</td>";
                            echo "<td class='action-links'>
                                <a href='#' id='edit_" . $row["id"] . "' class='update-link'>Edit</a>
                                <a href='#' id='save_" . $row["id"] . "' class='update-link' style='display:none;'>Save</a>
                                <a href='?delete=" . $row["id"] . "' class='delete-link' onclick='return confirm(\"Are you sure you want to delete your ID?\");'>Delete</a>
                            </td>";
                            echo "</tr>";
                        } else {
                            echo "<tr><td colspan='10' class='text-center'>No information found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('.update-link').forEach(link => {
    link.addEventListener('click', function(event) {
        event.preventDefault(); // Prevent the default anchor behavior
        const id = this.id.split('_')[1]; // Extract the ID from the link's ID
        if (this.textContent === 'Edit') {
            enableEdit(id);
        } else {
            saveEdit(id);
        }
    });
});

function enableEdit(id) {
    document.getElementById('name' + id).contentEditable = true;
    document.getElementById('email' + id).contentEditable = true;
    document.getElementById('phone_number' + id).contentEditable = true;
    document.getElementById('address' + id).contentEditable = true;
    document.getElementById('dob' + id).contentEditable = true;
    document.getElementById('username' + id).contentEditable = true;
    document.getElementById('password' + id).contentEditable = true;
    document.getElementById('edit_' + id).style.display = 'none';
    document.getElementById('save_' + id).style.display = 'inline';
}

function saveEdit(id) {
    var name = document.getElementById('name' + id). innerText;
    var email = document.getElementById('email' + id).innerText;
    var phone_number = document.getElementById('phone_number' + id).innerText;
    var picture_path = document.getElementById('picture_path' + id).innerText; // Assuming picture_path is editable
    var address = document.getElementById('address' + id).innerText;
    var date_of_birth = document.getElementById('dob' + id).innerText;
    var username = document.getElementById('username' + id).innerText;
    var password = document.getElementById('password' + id).innerText;

    // Send AJAX request to update the user information
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '<?php echo $_SERVER["PHP_SELF"]; ?>', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (this.status == 200) {
            alert("Information updated successfully");
            // Optionally refresh the page or update the DOM to reflect the changes
            location.reload(); // Reload the page to see the changes
        }
    };
    xhr.send('update=1&id=' + id + '&name=' + encodeURIComponent(name) + '&email=' + encodeURIComponent(email) + '&phone_number=' + encodeURIComponent(phone_number) + '&picture_path=' + encodeURIComponent(picture_path) + '&address=' + encodeURIComponent(address) + '&date_of_birth=' + encodeURIComponent(date_of_birth) + '&username=' + encodeURIComponent(username) + '&password=' + encodeURIComponent(password));
}
</script>
</body>
</html>
<?php
$conn->close();
?>