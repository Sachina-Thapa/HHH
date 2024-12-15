<?php
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
        $stmt = $conn->prepare("INSERT INTO staff_data (name, email, phoneno, username, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $staff_name, $staff_email, $staff_phoneno, $staff_uname, $staff_pass);

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
 
     <!-- Chart.js -->
     <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
 
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
         }
 
         .form-wrapper {
             background-color: white;
             padding: 1.5rem;
             border-radius: 0.5rem;
             box-shadow: 0 2px 4px rgba(97, 57, 57, 0.1);
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
 
         .alert-success {
             position: fixed;
             top: 10px;
             right: 10px;
             z-index: 1000;
         }
     </style>
 </head>
 
 <body>
     <div class="container-fluid">
         <div class="row">
            <!-- Alert Messages -->
            <?php
            // Check for any alert messages
            if (isset($_SESSION['alert'])) {
                echo $_SESSION['alert'];
                unset($_SESSION['alert']); // Clear alert after displaying
            }
            ?>
             <!-- Sidebar -->
             <?php require('inc/sideMenu.php'); ?>
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
                         <button class="btn btn-outline-primary" id="addNewHosteller">Add New Staff</button>
                     </div>
                     <!-- Search and Filters -->
                     <div class="d-flex justify-content-between align-items-center mt-3">
                         <input type="text" id="searchInput" class="form-control w-25" placeholder="Search Staff (Email, Name, Phone)">
                         <button class="btn btn-outline-primary" id="searchBtn">Search</button>
                     </div>
                     <!-- Table -->
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
            echo "<tr><td colspan='4'>No staff found</td></tr>";
        }
    ?>
                    </tbody>

                     </table>
                     <!-- Add/Edit Staff Form -->
                     <div class="form-wrapper d-none" id="staffFormContent">
                         <h4>ADD New Staff</h4>
        <form id="staffFormContent" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="mb-3">
                    <label for="staff_name" class="form-label">Name *</label>
                    <input type="text" class="form-control" id="staff_name" name="name" placeholder="Enter name" required>
                </div>
                <div class="mb-3">
                    <label for="staff_email" class="form-label">Email *</label>
                    <input type="email" class="form-control" id="staff_email" name="email" placeholder="Enter email" required>
                </div>
                <div class="mb-3">
                    <label for="staff_phoneno" class="form-label">Phone no *</label>
                    <input type="text" class="form-control" id="staff_phoneno" name="phoneno" placeholder="Enter Phone no" required>
                </div>
                <div class="mb-3">
                    <label for="staff_uname" class="form-label">Username *</label>
                    <input type="text" class="form-control" id="staff_uname" name="username" placeholder="Enter Username" required>
                    <span id="usernameError" class="text-danger" style="display: none;"></span> <!-- Error message span -->
                </div>
                <div class="mb-3">
                    <label for="staff_pass" class="form-label">Password *</label>
                    <input type="password" class="form-control" id="staff_pass" name="password" placeholder="Enter password" required>
                </div>
                <button type="submit" class="btn btn-outline-primary" name="add">Save</button>
                <button type="button" class="btn btn-outline-secondary" id="cancelstBtn">Cancel</button>
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
        
        // Select the button and the form content
            const addNewstaffBtn = document.getElementById('addNewHosteller');
            const staffFormContent = document.getElementById('staffFormContent');
            const cancelstBtn = document.getElementById('cancelstBtn');

            // Show form when clicking 'Add New Staff'
            addNewstaffBtn.addEventListener('click', () => {
                staffFormContent.classList.remove('d-none');
            });

            // Hide form when clicking 'Cancel'
            cancelstBtn.addEventListener('click', () => {
                staffFormContent.classList.add('d-none');
            });
            // Show notification
                function showSuccessNotification() {
                    successAlert.classList.remove('d-none');
                    setTimeout(() => {
                        successAlert.classList.add('d-none');
                    }, 3000); // Hide after 3 seconds
                }

                function enableEdit(st_id)                 
{
            document.getElementById('name_' + st_id).contentEditable = true;
            document.getElementById('email_' + st_id).contentEditable = true;
            document.getElementById('phoneno_' + st_id).contentEditable = true;
            document.getElementById('username_' + st_id).contentEditable = true;
            document.getElementById('password_' + st_id).contentEditable = true;
            document.getElementById('edit_' + st_id).style.display = 'none';
            document.getElementById('save_' + st_id).style.display = 'inline';
}

    function saveEdit(st_id) 
    {
    var staff_name = document.getElementById('name_' + st_id).innerText;
    var staff_email = document.getElementById('email_' + st_id).innerText;
    var staff_phoneno = document.getElementById('phoneno_' + st_id).innerText;
    var staff_uname = document.getElementById('username_' + st_id).innerText;
    var staff_pass = document.getElementById('password_' + st_id).innerText;

    // Send AJAX request to update staff
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


            fetch('staffmanagement.php', {
                method: 'POST',
                body: data,
            }).then(response => response.text()).then(data => {
                if (data.includes("success")) {
                    alert("Staff added successfully!");
                    location.reload(); // Reload page after successful staff addition
                } else {
                    alert("Error: " + data); // Handle error if there's an issue
                }
            }).catch(error => {
                console.error('Error:', error);
                alert('Error occurred while adding staff!');
            });
        


         // Function to search and filter the table based on user input
         // Select the search input and button
const searchInput = document.getElementById('searchInput');
const searchBtn = document.getElementById('searchBtn');
const hostellerTable = document.getElementById('hostellerTable');

// Function to search and filter the table based on user input
    function searchHosteller() 
{
    const filter = searchInput.value.toLowerCase();
    const rows = hostellerTable.getElementsByTagName('tr');

    for (let i = 1; i < rows.length; i++) { // Start from 1 to skip the header row
        const emailCell = rows[i].getElementsByTagName('td')[0];
        const nameCell = rows[i].getElementsByTagName('td')[1];
        const phoneCell = rows[i].getElementsByTagName('td')[2];

        if (emailCell || nameCell || phoneCell) {
            const emailText = emailCell.textContent || emailCell.innerText;
            const nameText = nameCell.textContent || nameCell.innerText;
            const phoneText = phoneCell.textContent || phoneCell.innerText;

            if (emailText.toLowerCase().indexOf(filter) > -1 || nameText.toLowerCase().indexOf(filter) > -1 || phoneText.toLowerCase().indexOf(filter) > -1) {
                rows[i].style.display = '';
            } else {
                rows[i].style.display = 'none';
            }
        }
    }
}

            // Attach search function to search button click
            searchBtn.addEventListener('click', searchHosteller);

            // Optionally, you can also trigger the search as the user types:
            searchInput.addEventListener('keyup', searchHosteller);


        const usernameInput = document.getElementById('staff_uname');
        const usernameError = document.getElementById('usernameError');

    usernameInput.addEventListener('input', function() {
    const username = usernameInput.value;

    // Make an AJAX call to check the username
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'check_username.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (this.responseText === 'exists') {
            usernameError.innerText = "This username already exists. Please choose another username.";
            usernameError.style.display = 'block';
        } else {
            usernameError.innerText = ""; // Clear the error message
            usernameError.style.display = 'none';
        }
    };
    xhr.send('username=' + encodeURIComponent(username));
});

     </script>
 </body>
 </html>
