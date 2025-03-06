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
    $sql = "SELECT id, name, email, phone_number, picture_path, address, date_of_birth, username FROM hostelers WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    return $stmt->get_result();
}

// Handle the update request
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $address = $_POST['address'];

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

    // Prepare the update SQL statement - only updating name and address
    $update_sql = "UPDATE hostelers SET name = ?, address = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssi", $name, $address, $id);
    
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
    <meta name="viewport" content="width=1024">
    <title>My Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container-wrapper {
            width: 800px;
            margin: 40px auto;
            position: absolute;
            left: calc(50% + 135px); 
            transform: translateX(-50%);
        }
        .user-info {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }
        .profile-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 30px;
            position: relative;
        }
        .profile-header img {
            border-radius: 50%;
            width: 130px;
            height: 130px;
            object-fit: cover;
            border: 5px solid #f8f9fa;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .profile-name {
            font-size: 24px;
            font-weight: 600;
            margin-top: 15px;
            color: #333;
        }
        .info-line {
            display: flex;
            margin: 20px 0;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }
        .info-line label {
            width: 140px;
            font-weight: 600;
            color: #555;
        }
        .info-line span {
            flex: 1;
            color: #333;
        }
        .editable {
            padding: 5px 10px;
            border-radius: 4px;
            transition: all 0.3s;
        }
        .editable[contenteditable="true"] {
            background-color: #f0f7ff;
            border: 1px solid #cce5ff;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
        }
        .action-button {
            display: inline-block;
            padding: 10px 20px;
            margin: 10px 5px;
            text-decoration: none;
            color: #fff;
            border-radius: 5px;
            font-weight: 500;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }
        .update-button {
            background-color: #4CAF50;
        }
        .update-button:hover {
            background-color: #3d8b40;
        }
        .save-button {
            background-color: #2196F3;
        }
        .save-button:hover {
            background-color: #0b7dda;
        }
        .message {
            text-align: center;
            font-size: 1.1em;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .editable-icon {
            margin-left: 10px;
            color: #777;
            visibility: hidden;
        }
        .info-line:hover .editable-icon {
            visibility: visible;
        }
    </style>
</head>
<body>

<div class="container-wrapper">
    <h2 class="text-center mb-4">My Account Profile</h2>

    <?php if (isset($_GET['updated']) && $_GET['updated'] == 1): ?>
    <div class="message success-message">
        <i class="fas fa-check-circle"></i> Your information has been updated successfully!
    </div>
    <?php endif; ?>

    <div class="user-info">
        <?php
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc(); // Fetch the single row for the logged-in user
        ?>
        <div class="profile-header">
            <img src="<?php echo htmlspecialchars($row['picture_path']); ?>" alt="Profile Picture">
            <div class="profile-name"><?php echo htmlspecialchars($row['name']); ?></div>
        </div>
        
        <div class="info-line">
            <label>Name:</label> 
            <span id="name" class="editable"><?php echo htmlspecialchars($row['name']); ?></span>
            <i class="fas fa-pencil-alt editable-icon"></i>
        </div>
        <div class="info-line">
            <label>Email:</label> 
            <span><?php echo htmlspecialchars($row['email']); ?></span>
        </div>
        <div class="info-line">
            <label>Phone Number:</label> 
            <span><?php echo htmlspecialchars($row['phone_number']); ?></span>
        </div>
        <div class="info-line">
            <label>Address:</label> 
            <span id="address" class="editable"><?php echo htmlspecialchars($row['address']); ?></span>
            <i class="fas fa-pencil-alt editable-icon"></i>
        </div>
        <div class="info-line">
            <label>Date of Birth:</label> 
            <span><?php echo htmlspecialchars($row['date_of_birth']); ?></span>
        </div>
        <div class="info-line">
            <label>Username:</label> 
            <span><?php echo htmlspecialchars($row['username']); ?></span>
        </div>

        <div class="text-center mt-4">
            <button id="edit" class="action-button update-button">
                <i class="fas fa-edit"></i> Edit Profile
            </button>
            <button id="save" class="action-button save-button" style="display:none;">
                <i class="fas fa-save"></i> Save Changes
            </button>
        </div>
        <?php
        } else {
            echo "<p class='text-center'>No information found.</p>";
        }
        ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('edit').addEventListener('click', function(event) {
    event.preventDefault();
    enableEdit();
});

document.getElementById('save').addEventListener('click', function(event) {
    event.preventDefault();
    saveEdit();
});

function enableEdit() {
    document.getElementById('name').contentEditable = true;
    document.getElementById('address').contentEditable = true;
    
    document.getElementById('name').focus();
    document.getElementById('name').classList.add('editable-active');
    document.getElementById('address').classList.add('editable-active');
    
    document.getElementById('edit').style.display = 'none';
    document.getElementById('save').style.display = 'inline-block';
}

function saveEdit() {
    var id = <?php echo json_encode($row['id']); ?>;
    var name = document.getElementById('name').innerText;
    var address = document.getElementById('address').innerText;

    // Send AJAX request to update the user information
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '<?php echo $_SERVER["PHP_SELF"]; ?>', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (this.status == 200) {
            window.location.href = 'accounts.php?updated=1';
        }
    };
    xhr.send('update=1&id=' + id + '&name=' + encodeURIComponent(name) + '&address=' + encodeURIComponent(address));
}
</script>
</body>
</html>
<?php
$conn->close();
?>