<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Management</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
<<<<<<< HEAD
       body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 100%;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
=======
        body {
            background-color: #f8f9fa;
>>>>>>> 5d4f584d001869c933183a50edd24d3ba2bd99bc
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


        <!-- Main Content -->
        <div class="col-md-10 p-4">

            <!-- Success Notification -->
            <div id="successAlert" class="alert alert-success d-none">
                Well done! User added successfully.
            </div>

            <!-- Staff Table -->
            <div class="Staff">
                <div class="d-flex justify-content-between align-items-center">
                    <h4>Total</h4>
                    <button class="btn btn-primary" id="addNewUser">+ Add New User</button>
                </div>

                <!-- Search and Filters -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <input type="text" class="form-control w-25" placeholder="Search user name, email...">
                    <select class="form-select w-25">
                        <option selected>Staff Permissions</option>
                        <option value="1">Operational</option>
                      
                        <option value="3">View Only</option>
                    </select>
                    <button class="btn btn-outline-primary">Search</button>
                </div>
            
                    <!-- Table -->
                <table class="table mt-3">
                    <thead>
                    <tr>
                        <th>Email</th>
                        <th>Name</th>
                        <th>Ph.no</th>
                        <th>Staff Permissions</th>
                        <th>Username</th>
                        <th>password</th>
                        <th>Edit</th>
                        <th>Update</th>
                        <th>Delete</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>aakritipandit11111@gmail.com</td>
                            <td>Aakriti pandit</td>
                            <td>9999999999</td>
                            <td>operational</td>
                            <td>Ak.riti537</td>
                            <td>junkiri</td>
                            <td><button class="btn btn-outline-secondary btn-sm">Edit</button></td>
                            <td><button class="btn btn-outline-secondary btn-sm">update</button></td>
                            <td><button class="btn btn-outline-secondary btn-sm">Delete</button></td>

                        </tr>
                        <tr>
                            <td>smritipandit222222@gmail.com</td>
                            <td>Smriti pandit</td>
                            <td>1111111111</td>
                            <td>view only</td>
                            <td>ismu537</td>
                            <td>missusissy</td>
                            <td><button class="btn btn-outline-secondary btn-sm">Edit</button></td>
                            <td><button class="btn btn-outline-secondary btn-sm">update</button></td>
                            <td><button class="btn btn-outline-secondary btn-sm">Delete</button></td>

                        </tr>
                    <!-- Add more rows as needed -->
                    </tbody>
                </table>

               
            <!-- Add/Edit Staff Form -->
            <div class="form-wrapper d-none" id="userForm">
                <h4>ADD NEW STAFF</h4>
                <form>
                    <div class="mb-3">
                        <label for="userName" class="form-label">Name *</label>
                        <input type="text" class="form-control" id="userName" placeholder="Enter name">
                    
                    <div class="mb-3">
                        <label for="StaffEmail" class="form-label">Email *</label>
                        <input type="emmail" class="form-control" id="StaffEmail" placeholder="Enter email">
                    
                </div>
                <div class="mb-3">
                    <label for="Staffphone no*" class="form-label">Phone no*</label>
                    <input type="phone no*" class="form-control" id="Staffphone no" placeholder="Enter Phone no">
            
            </div>
            <div class="mb-3">
                <label for="Username*" class="form-label">Username*</label>
                <input type="Username*" class="form-control" id="Staff Username" placeholder="Enter Username">
        
        </div>
        <div class="mb-3">
            <label for="Staffpassword*" class="form-label">password*</label>
            <input type="password" class="form-control" id="Staffpassword" placeholder="Enter password">
        </div>
                    <div class="mb-3">
                        <label for="Staff Permissions" class="form-label">Staff Permissions *</label>
                        <select id="Staff Permissions" class="form-select">
                            <option value="Operational">Operational</option>
                            <option value="View Only">View Only</option>
                        </select>
                    </div>        
                    <button type="button" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-primary">Cancel</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Show notification
    const successAlert = document.getElementById('successAlert');
    const addNewUserBtn = document.getElementById('addNewUser');
    const userForm = document.getElementById('userForm');

    // Show form when clicking 'Add New User'
    addNewUserBtn.addEventListener('click', () => {
        userForm.classList.remove('d-none');
    });

    // Simulate showing the success notification
    function showSuccessNotification() {
        successAlert.classList.remove('d-none');
        setTimeout(() => {
            successAlert.classList.add('d-none');
        }, 3000); // Hide after 3 seconds
    }
</script>

</body>
</html>
