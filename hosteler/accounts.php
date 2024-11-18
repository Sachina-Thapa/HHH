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

// Function to get user information by username
function getUserInfoByUsername($conn, $username) {
    $sql = "SELECT id, name, email, phone_number, picture_path, address, date_of_birth, username, password FROM hostelers WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    return $stmt->get_result();
}

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
$result = getUserInfoByUsername($conn, $username);

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
        .user-info {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            text-align: center; /* Center text */
        }
        .user-info img {
            border-radius: 50%; /* Make the image circular */
            width: 100px; /* Set a fixed width */
            height: 100px; /* Set a fixed height */
            object-fit: cover; /* Maintain aspect ratio */
            margin-bottom: 20px; /* Space below the image */
        }
        .info-line {
            margin: 15px 0; /* Add vertical spacing */
            padding: 10px 0; /* Add padding */
            border-bottom: 1px solid #ddd; /* Add a bottom border */
            text-align: left; /* Align text to the left */
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

<div class="container-fluid col-md-6 offset-md-3 p-4">
    <h2 class="mt-3 text-center">My Account</h2>

    <div id="message">
        <?php
        if (isset($_GET['updated']) && $_GET['updated'] == 1) {
            echo "<p style='color: green;'>Information updated successfully</p>";
        }
        ?>
    </div>

    <div class="user-info">
        <?php
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc(); // Fetch the single row for the logged-in user
            ?>
            <img src="<?php echo htmlspecialchars($row['picture_path']); ?>" alt="Profile Picture">
            
            <div class="info-line">
                <label>ID:</label> <span><?php echo htmlspecialchars($row['id']); ?></span>
            </div>
            <div class="info-line">
                <label>Name:</label> <span id="name"><?php echo htmlspecialchars($row['name']); ?></span>
            </div>
            <div class="info-line">
                <label>Email:</label> <span id="email"><?php echo htmlspecialchars($row['email']); ?></span>
            </div>
            <div class="info-line">
                <label>Phone Number:</label> <span id="phone_number"><?php echo htmlspecialchars($row['phone_number']); ?></span>
            </div>
            <div class="info-line">
                <label>Address:</label> <span id="address"><?php echo htmlspecialchars($row['address']); ?></span>
            </div>
            <div class="info-line">
                <label>Date of Birth:</label> <span id="dob"><?php echo htmlspecialchars($row['date_of_birth']); ?></span>
            </div>
            <div class="info-line">
                <label>Username:</label> <span id="username"><?php echo htmlspecialchars($row['username']); ?></span>
            </div>
            <div class="info-line">
                <label>Password:</label> <span id="password"><?php echo htmlspecialchars($row['password']); ?></span>
            </div>
            <div class="action-links">
                <a href="#" id="edit" class="update-link">Edit</a>
                <a href="#" id="save" class="update-link" style="display:none;">Save</a>
                <a href="?delete=<?php echo $row['id']; ?>" class="delete-link" onclick="return confirm('Are you sure you want to delete your ID?');">Delete</a>
            </div>
            <?php
        } else {
            echo "<p>No information found.</p>";
        }
        ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('edit').addEventListener('click', function(event) {
    event.preventDefault(); // Prevent the default anchor behavior
    enableEdit();
});

document.getElementById('save').addEventListener('click', function(event) {
    event.preventDefault(); // Prevent the default anchor behavior
    saveEdit();
});

function enableEdit() {
    document.getElementById('name').contentEditable = true;
    document.getElementById('email').contentEditable = true;
    document.getElementById('phone_number').contentEditable = true;
    document.getElementById('address').contentEditable = true;
    document.getElementById('dob').contentEditable = true;
    document.getElementById('username').contentEditable = true;
    document.getElementById('password').contentEditable = true;
    document.getElementById('edit').style.display = 'none';
    document.getElementById('save').style.display = 'inline';
}

function saveEdit() {
    var id = <?php echo json_encode($row['id']); ?>;
    var name = document.getElementById('name').innerText;
    var email = document.getElementById('email').innerText;
 var phone_number = document.getElementById('phone_number').innerText;
    var address = document.getElementById('address').innerText;
    var date_of_birth = document.getElementById('dob').innerText;
    var username = document.getElementById('username').innerText;
    var password = document.getElementById('password').innerText;

    // Send AJAX request to update the user information
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '<?php echo $_SERVER["PHP_SELF"]; ?>', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (this.status == 200) {
            alert("Information updated successfully");
            location.reload(); // Reload the page to see the changes
        }
    };
    xhr.send('update=1&id=' + id + '&name=' + encodeURIComponent(name) + '&email=' + encodeURIComponent(email) + '&phone_number=' + encodeURIComponent(phone_number) + '&address=' + encodeURIComponent(address) + '&date_of_birth=' + encodeURIComponent(date_of_birth) + '&username=' + encodeURIComponent(username) + '&password=' + encodeURIComponent(password));
}
</script>
</body>
</html>
<?php
$conn->close();
?>