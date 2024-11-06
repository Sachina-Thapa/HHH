    <?php
        require('inc/db.php');

        // Handle form submission to add new staff
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $phoneno = $_POST['phoneno'];
            $username = $_POST['username'];
            $password = $_POST['password'];
        
            // Prepare and bind the statement
            $sql = "INSERT INTO staff_data (name, email, phoneno, username, password) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $name, $email, $phoneno, $username, $password);
        
            if ($stmt->execute()) {
                echo "success"; // Return success message
            } else {
                echo "Error: " . $stmt->error; // Return error message
            }
        
            $stmt->close();
            exit(); // Important to stop further execution
        }
            // Update staff
            if (isset($_GET['update'])) {
                $rid = $_GET['update'];
                $fetch_sql = "SELECT * FROM staff_data WHERE st_id = $st_id";
                $fetch_result = $conn->query($fetch_sql);
                $staff_to_update = $fetch_result->fetch_assoc();
            }

            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
                // $st_id = $_POST['st_id'];
                $staff_name = $_POST['name'];
                $staff_email = $_POST['email'];
                $room_phone = $_POST['phoneno'];
                $staff_uname = $_POST['username'];
                $staff_pass = $_POST['password'];

                $update_sql = "UPDATE staff_data SET name=?, email=?, phoneno=?, username=?, password=? WHERE st_id=?";
                $stmt = $conn->prepare($update_sql);
                $stmt->bind_param("sssss", $staff_name, $staff_email, $staff_phone, $staff_uname, $staff_pass );

                if ($stmt->execute()) {
                    header("Location: ".$_SERVER['PHP_SELF']."?updated=1");
                    exit();
                } else {
                    echo "Error updating record: " . $stmt->error;
                }
                $stmt->close();
            }// Delete staff
            if (isset($_GET['delete'])) {
                $st_id = $_GET['delete'];
                $delete_sql = "DELETE FROM staff_data WHERE st_id = $st_id";
                if ($conn->query($delete_sql) === TRUE) {
                    // Reset auto-increment
                    $reset_sql = "ALTER TABLE staff_data AUTO_INCREMENT = 1";
                    $conn->query($reset_sql);
                    header("Location: ".$_SERVER['PHP_SELF']."?deleted=1");
                    exit();
                } else {
                    $error_message = "Error deleting record: " . $conn->error;
                }
            }

            // Fetch all staffs
            $sql = "SELECT * FROM staff_data";
            $result = $conn->query($sql);
?>
        <!-- // Fetch all staff records from the database
        $sql = "SELECT st_id, name, email, phoneno, username, password FROM staff_data";
        $result = $conn->query($sql);
    ?> -->
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
 
         /* Sidebar CSS */
         .sidebar {
             margin: 0px;
             height: 140vh;
             background-color: #343a40;
             padding-top: 10px;
         }
 
         .sidebar a {
             color: #fff;
             padding: 25px;
             display: block;
             text-decoration: none;
         }
 
         .sidebar a:hover {
             background-color: #495057;
         }
 
         .logout-btn {
             margin-top: 30px;
             background-color: #f8f9fa;
             border: none;
             color: #000;
             padding: 6px;
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
             <!-- Sidebar -->
             <div class="col-md-2 sidebar">
                 <h4 class="text-white text-center">Her Home Hostel</h4>
                 <a href="addash.php">Dashboard</a>
                 <a href="roomManagement.php">Room Management</a>
                 <a href="staffmanagement.php">Staff management</a>
                 <a href="hostelerManagement.php">Hosteller</a>
                 <a href="queries.php">Queries</a>
                 <a href="setting.php">Settings</a>
                 <button class="logout-btn w-100">LOG OUT</button>
             </div>
             <!-- Main Content -->
             <div class="col-md-10 p-4">
                 <!-- Success Notification -->
                 <div id="successAlert" class="alert alert-success d-none">
                      Staff added successfully.
                 </div>
                 <!-- Staff Table -->
                 <div class="Staff">
                     <div class="d-flex justify-content-between align-items-center">
                         <h4>Total Staff</h4>
                         <button class="btn btn-primary" id="addNewHosteller">Add New Staff</button>
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
                                 <th>Email</th>
                                 <th>Name</th>
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
                    echo "<tr>";
                    echo "<td>" . $row['name'] . "</td>";
                    echo "<td>" . $row['email'] . "</td>";
                    echo "<td>" . $row['phoneno'] . "</td>";
                    echo "<td>" . $row['username'] . "</td>";
                    echo "<td>" . $row['password'] . "</td>";
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
                     <div class="form-wrapper d-none" id="stForm">
                         <h4>ADD New Staff</h4>
                         <form id="stForm">
                             <div class="mb-3">
                                 <label for="stName" class="form-label">Name *</label>
                                 <input type="text" class="form-control" id="stName" placeholder="Enter name" required>
                             </div>
                             <div class="mb-3">
                                 <label for="stEmail" class="form-label">Email *</label>
                                 <input type="email" class="form-control" id="stEmail" placeholder="Enter email" required>
                             </div>
                             <div class="mb-3">
                                 <label for="stPhoneNo" class="form-label">Phone no *</label>
                                 <input type="text" class="form-control" id="stPhoneNo" placeholder="Enter Phone no" required>
                             </div>
                             <div class="mb-3">
                                 <label for="stUsername" class="form-label">Username *</label>
                                 <input type="text" class="form-control" id="stUsername" placeholder="Enter Username" required>
                             </div>
                             <div class="mb-3">
                                 <label for="stPassword" class="form-label">Password *</label>
                                 <input type="password" class="form-control" id="stPassword" placeholder="Enter password" required>
                             </div>
                             <button type="button" class="btn btn-primary" id="savestBtn">Save</button>
                             <button type="button" class="btn btn-secondary" id="cancelstBtn">Cancel</button>
                         </form>
                     </div>
                 </div>
             </div>
         </div>
     </div>
     <!-- Bootstrap JS -->
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
     <script>
         const successAlert = document.getElementById('successAlert');
         const addNewHostellerBtn = document.getElementById('addNewHosteller');
         const stForm = document.getElementById('stForm');
         const savestBtn = document.getElementById('savestBtn');
         const cancelstBtn = document.getElementById('cancelstBtn');
         const hostellerTable = document.getElementById('hostellerTable').getElementsByTagName('tbody')[0];
         const searchInput = document.getElementById('searchInput');
         const searchBtn = document.getElementById('searchBtn');
 
         // Show form when clicking 'Add New Hosteller'
         addNewHostellerBtn.addEventListener('click', () => {
             stForm.classList.remove('d-none');
         });
 
         // Hide form when clicking 'Cancel'
         cancelstBtn.addEventListener('click', () => {
             stForm.classList.add('d-none');
         });
 
        //  // Function to dynamically add a new row in the table
        //  function addHostellerToTable(email, name, phoneNo, username, password) {
        //      const newRow = hostellerTable.insertRow(); // Create new row
 
        //      // Insert new cells in the row
        //      const emailCell = newRow.insertCell(0);
        //      const nameCell = newRow.insertCell(1);
        //      const phoneNoCell = newRow.insertCell(2);
        //      const usernameCell = newRow.insertCell(3);
        //      const passwordCell = newRow.insertCell(4);
            
 
        //      // Set cell values
        //      emailCell.innerText = email;
        //      nameCell.innerText = name;
        //      phoneNoCell.innerText = phoneNo;
        //      usernameCell.innerText = username;
        //      passwordCell.innerText = password;
 
        //  }
 
         // Show notification
         function showSuccessNotification() {
             successAlert.classList.remove('d-none');
             setTimeout(() => {
                 successAlert.classList.add('d-none');
             }, 3000); // Hide after 3 seconds
         }
 
         // Save the new staff to the table
        savestBtn.addEventListener('click', () => 
{
            const email = document.getElementById('stEmail').value;
            const name = document.getElementById('stName').value;
            const phoneNo = document.getElementById('stPhoneNo').value;
            const username = document.getElementById('stUsername').value;
            const password = document.getElementById('stPassword').value;

            if (email && name && phoneNo && username && password) {
                // Send data to the server using AJAX
                fetch('staffmanagement.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        'name': name,
                        'email': email,
                        'phoneno': phoneNo,
                        'username': username,
                        'password': password
                    })
                })
                .then(response => response.text())
                .then(data => {
                    if (data.includes("success")) {
                        addstaffToTable(email, name, phoneNo, username, password); // Add to table
                        showSuccessNotification(); // Show success alert
                        stForm.classList.add('d-none'); // Hide form after saving
                    } else {
                        alert('Error saving data: ' + data);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            } else {
                alert('Please fill in all required fields.');
            }
});
 
         // Function to search and filter the table based on user input
         function searchHosteller() {
             const filter = searchInput.value.toLowerCase();
             const rows = hostellerTable.getElementsByTagName('tr');
 
             for (let i = 0; i < rows.length; i++) {
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

     </script>
 </body>
 
 </html>