<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require('inc/db.php');
require('inc/essentials.php'); // Include the essentials file

// Handle form submission to add new staff
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $staff_name = $_POST['name'];
    $staff_email = $_POST['email'];
    $staff_phoneno = $_POST['phoneno'];
    $staff_uname = $_POST['username'];
    $staff_pass = $_POST['password'];

    // Check if the username already exists
    $check_sql = "SELECT COUNT(*) as count FROM staff_data WHERE username = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $staff_uname);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    $check_row = $check_result->fetch_assoc();

    if ($check_row['count'] > 0) {
        // Username already exists
        $error_message = "This username already exists. Please choose another username.";
    } else {
        // Proceed to insert the new staff
       // After your initial POST checks, modify the prepare statement:
$stmt = $conn->prepare("INSERT INTO staff_data (name, email, phoneno, username, password, status) VALUES (?, ?, ?, ?, ?, ?)");
$default_status = 1;
// Note: all parameters are now strings ("s") except status which is integer ("i")
$stmt->bind_param("sssssi", $staff_name, $staff_email, $staff_phoneno, $staff_uname, $staff_pass, $default_status);

        if ($stmt->execute()) {
            redirect($_SERVER['PHP_SELF']); // Redirect to the same page
            alert("success", "New staff added successfully."); // Display success alert
            exit();
        }
        $stmt->close();
    }
}

// Update staff
if (isset($_GET['update'])) {
    $st_id = $_GET['update'];
    $fetch_sql = "SELECT * FROM staff_data WHERE st_id = $st_id";
    $fetch_result = $conn->query($fetch_sql);
    $staff_to_update = $fetch_result->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $st_id = $_POST['st_id'];
    $staff_name = $_POST['name'];
    $staff_email = $_POST['email'];
    $staff_phone = $_POST['phoneno'];
    $staff_uname = $_POST['username'];
    $staff_pass = $_POST['password'];

    $update_sql = "UPDATE staff_data SET name=?, email=?, phoneno=?, username=?, password=? WHERE st_id=?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("sssssi", $staff_name, $staff_email, $staff_phone, $staff_uname, $staff_pass, $st_id);

    if ($stmt->execute()) {
        redirect($_SERVER['PHP_SELF']); // Redirect to the same page
        alert("success", "Staff updated successfully."); // Display success alert
        exit();
    }
    $stmt->close();
}

// Delete staff
if (isset($_GET['delete'])) {
    $st_id = $_GET['delete'];
    $delete_sql = "DELETE FROM staff_data WHERE st_id = $st_id";
    if ($conn->query($delete_sql) === TRUE) {
        // Reset auto-increment
        $reset_sql = "ALTER TABLE staff_data AUTO_INCREMENT = 1";
        $conn->query($reset_sql);
        redirect($_SERVER['PHP_SELF']); // Redirect to the same page
        alert("success", "Staff deleted successfully."); // Display success alert
        exit();
    }
}

// Fetch all staffs
$sql = "SELECT * FROM staff_data";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
    body {
        background-color: #f5f5f5;
        font-family: Arial, sans-serif;
    }

    .user-table {
        background-color: white;
        padding: 1rem;
        border-radius: 0.5rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .table-wrapper {
        margin-top: 2rem;
        background-color: white;
        padding: 1rem;
        border-radius: 0.5rem;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .form-wrapper {
        background-color: white;
        padding: 1.5rem;
        border-radius: 0.5rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin-top: 2rem;
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

    .button-link {
        background-color: rgb(63, 59, 59);
        color: #fff;
        border-radius: 3px;
    }

    .update-link {
        background-color: rgb(65, 126, 218);
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

    .btn-black {
        background-color: rgb(1, 15, 7);
        color: #fff;
        text-transform: uppercase;
        padding: 5px 10px;
        border-radius: 3px;
        border: none;
        transition: background-color 0.3s;
    }

    .btn-black:hover {
        background-color: rgb(20, 30, 20);
    }

    #message {
        text-align: center;
        font-size: 1.2em;
    }

    .alert-success {
        position: fixed;
        top: 10px;
        right: 10px;
        z-index: 1000;
    }

    .form-label {
        font-weight: bold;
    }

    .form-control {
        border-radius: 0.25rem;
    }

    .table th,
    .table td {
        vertical-align: middle;
    }

    .table thead th {
        background-color: rgb(63, 59, 59);
        color: #fff;
    }

    .table tbody tr:hover {
        background-color: #f1f1f1;
    }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Alert Messages -->
            <?php
            if (isset($_SESSION['alert'])) {
                echo $_SESSION['alert'];
                unset($_SESSION['alert']);
            }
            ?>
               <!-- Sidebar -->
        <div class="sidebar">
            <?php require('inc/sideMenu.php'); ?>
        </div>
            <!-- Main Content -->
            <div class="col-md-10 content-wrapper py-4 px-4">
                <!-- Success Notification -->
                <div id="successAlert" class="alert alert-success d-none">
                    Staff added successfully.
                </div>
                <!-- Staff Table -->
                <div class="Staff">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4>Total Staff</h4>
                        <button class="button-link" id="addNewHosteller">Add New Staff</button>
                    </div>
                    <!-- Search and Filters -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <input type="text" id="searchInput" class="form-control w-75"
                            placeholder="Search Staff (Email, Name, Phone)">
                        <button class="button-link" id="searchBtn">Search</button>
                    </div>
                    <!-- Table -->
                    <div class="table-wrapper">
                        <table class="table mt-3" id="hostellerTable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone No</th>
                                    <th>Username</th>
                                    <th>Password</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr id='staff_" . $row['st_id'] . "'>";
                                        echo "<td id='name_" . $row['st_id'] . "'>" . $row['name'] . "</td>";
                                        echo "<td id='email_" . $row['st_id'] . "'>" . $row['email'] . "</td>";
                                        echo "<td id='phoneno_" . $row['st_id'] . "'>" . $row['phoneno'] . "</td>";
                                        echo "<td id='username_" . $row['st_id'] . "'>" . $row['username'] . "</td>";
                                        echo "<td id='password_" . $row['st_id'] . "'>" . $row['password'] . "</td>";
                                        echo "<td class='action-links'>
                                            <a href='#' id='edit_" . $row["st_id"] . "' class='update-link' onclick='enableEdit(" . $row["st_id"] . ")'>Edit</a>
                                            <a href='#' id='save_" . $row["st_id"] . "' class='update-link' onclick='saveEdit(" . $row["st_id"] . ")' style='display:none;'>Save</a>
                                            <a href='?delete=" . $row["st_id"] . "' class='delete-link' onclick='return confirm(\"Are you sure you want to delete this staff?\");'>Delete</a>
                                        </td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6'>No staff found</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- Add/Edit Staff Form -->
                    <div class="form-wrapper d-none" id="staffFormContent">
                        <h4>ADD New Staff</h4>
                        <form id="staffFormContent" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                            <div class="mb-3">
                            <label for="staff_name" class="form-label">Name *</label>
                            <input type="text" class="form-control" id="staff_name" name="name" placeholder="Enter name" pattern="^[A-Za-z\s]+$" title="Only alphabets and spaces are allowed" required>
                            </div>
                            <div class="mb-3">
                                <label for="staff_email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="staff_email" name="email"
                                    placeholder="Enter email" required>
                            </div>
                            <div class="mb-3">
                                <label for="staff_phoneno" class="form-label" >Phone no *</label>
                                <input type="tel" class="form-control" pattern="(\+977?)?[9][6-9]\d{8}" maxLength="10" id="staff_phoneno" name="phoneno" placeholder="Enter Phone no" required>
                            </div>
                            <div class="mb-3">
                                <label for="staff_uname" class="form-label">Username *</label>
                                <input type="text" class="form-control" id="staff_uname" name="username" maxlength="20" pattern="^[A-Za-z]+$" title="Only alphabetic characters are allowed" placeholder="Enter Username" required>
                                <span id="usernameError" class="text-danger" style="display: none;"></span>
                            </div>
                            <div class="mb-3">
                                <label for="staff_pass" class="form-label">Password *</label>
                                <input type="password" name="password" class="form-control shadow-none" pattern="^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$"  title="Password must be at least 8 characters long and include letter, number, and special character" required>
                                </div>
                            <button type="submit" class="button-link" name="add">Save</button>
                            <button type="button" class="button-link" id="cancelstBtn">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="message">
        <?php
        if (isset($_GET['success']) && $_GET['success'] == 1) {
            echo "<p style='color: green;'>New staff added successfully</p>";
        }
        if (isset($error_message)) {
            echo "<p style='color: red;'>$error_message</p>";
        }
        if (isset($_GET['deleted']) && $_GET['deleted'] == 1) {
            echo "<p style='color: green;'>Staff deleted successfully and IDs reset</p>";
        }
        if (isset($_GET['updated']) && $_GET['updated'] == 1) {
            echo "<p style='color: green;'>Staff updated successfully</p>";
        }
        ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    const addNewstaffBtn = document.getElementById('addNewHosteller');
    const staffFormContent = document.getElementById('staffFormContent');
    const cancelstBtn = document.getElementById('cancelstBtn');

    addNewstaffBtn.addEventListener('click', () => {
        staffFormContent.classList.remove('d-none');
    });

    cancelstBtn.addEventListener('click', () => {
        staffFormContent.classList.add('d-none');
    });

    function enableEdit(st_id) {
        document.getElementById('name_' + st_id).contentEditable = true;
        document.getElementById('email_' + st_id).contentEditable = true;
        document.getElementById('phoneno_' + st_id).contentEditable = true;
        document.getElementById('username_' + st_id).contentEditable = true;
        document.getElementById('password_' + st_id).contentEditable = true;
        document.getElementById('edit_' + st_id).style.display = 'none';
        document.getElementById('save_' + st_id).style.display = 'inline';
    }

    function saveEdit(st_id) {
        var staff_name = document.getElementById('name_' + st_id).innerText;
        var staff_email = document.getElementById('email_' + st_id).innerText;
        var staff_phoneno = document.getElementById('phoneno_' + st_id).innerText;
        var staff_uname = document.getElementById('username_' + st_id).innerText;
        var staff_pass = document.getElementById('password_' + st_id).innerText;

        var xhr = new XMLHttpRequest();
        xhr.open('POST', '<?php echo $_SERVER["PHP_SELF"]; ?>', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (this.status == 200) {
                alert('Staff updated successfully');
                document.getElementById('edit_' + st_id).style.display = 'inline';
                document.getElementById('save_' + st_id).style.display = 'none';
            } else {
                alert('Error updating staff');
            }
        };
        xhr.send('update=1&st_id=' + st_id + '&name=' + staff_name + '&email=' + staff_email + '&phoneno=' + staff_phoneno + '&username=' + staff_uname + '&password=' + staff_pass);
    }

    const searchInput = document.getElementById('searchInput');
    const searchBtn = document.getElementById('searchBtn');
    const hostellerTable = document.getElementById('hostellerTable');

    function searchHosteller() {
        const filter = searchInput.value.toLowerCase();
        const rows = hostellerTable.getElementsByTagName('tr');

        for (let i = 1; i < rows.length; i++) {
            const nameCell = rows[i].getElementsByTagName('td')[0];
            const emailCell = rows[i].getElementsByTagName('td')[1];
            const phoneCell = rows[i].getElementsByTagName('td')[2];

            if (nameCell || emailCell || phoneCell) {
                const nameText = nameCell.textContent || nameCell.innerText;
                const emailText = emailCell.textContent || emailCell.innerText;
                const phoneText = phoneCell.textContent || phoneCell.innerText;

                if (nameText.toLowerCase().indexOf(filter) > -1 || emailText.toLowerCase().indexOf(filter) > -1 || phoneText.toLowerCase().indexOf(filter) > -1) {
                    rows[i].style.display = '';
                } else {
                    rows[i].style.display = 'none';
                }
            }
        }
    }

    searchBtn.addEventListener('click', searchHosteller);
    searchInput.addEventListener('keyup', searchHosteller);

    const usernameInput = document.getElementById('staff_uname');
    const usernameError = document.getElementById('usernameError');

    usernameInput.addEventListener('input', function() {
        const username = usernameInput.value;

        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'check_username.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (this.responseText === 'exists') {
                usernameError.innerText = "This username already exists. Please choose another username.";
                usernameError.style.display = 'block';
            } else {
                usernameError.innerText = "";
                usernameError.style.display = 'none';
            }
        };
        xhr.send('username=' + encodeURIComponent(username));
    });
    </script>
</body>

</html>