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
            margin: 0;
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
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-top: 2rem;
        }

        .alert-success {
            position: fixed;
            top: 10px;
            right: 10px;
            z-index: 1000;
        }

        /* Modal styling */
        .modal-content {
            border-radius: 0.5rem;
        }

        .modal-header {
            background-color: #007bff;
            color: white;
            border-top-left-radius: 0.5rem;
            border-top-right-radius: 0.5rem;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .table th {
            background-color: #007bff;
            color: white;
        }

        .table tbody tr:hover {
            background-color: #f1f1f1;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
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
                <a href="admanagement.php">Room Management</a>
                <a href="adstaff.php">Staff Management</a>
                <a href="adhosteller.php">Hosteller</a>
                <a href="setting.php">Settings</a>
                <button class="logout-btn w-100">LOG OUT</button>
            </div>
            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <!-- Success Notification -->
                <div id="successAlert" class="alert alert-success d-none">
                    Well done! Staff added successfully.
                </div>

                <!-- Staff Table -->
                <div class="table-wrapper">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4>Total Staff</h4>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStaffModal">Add New Staff</button>
                    </div>
                    <!-- Search and Filters -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <input type="text" id="searchInput" class="form-control w-25" placeholder="Search Staff (Email, Name, Phone)">
                        <button class="btn btn-outline-primary" id="searchBtn">Search</button>
                    </div>
                    <!-- Table -->
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Email</th>
                                <th>Name</th>
                                <th>Phone No</th>
                                <th>Username</th>
                                <th>Password</th>
                                <th>Edit</th>
                                <th>Update</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody id="hostellerTable">
                            <tr>
                                <td>aakritipandit11@gmail.com</td>
                                <td>Aakriti Pandit</td>
                                <td>7777777</td>
                                <td>Ak.riti537</td>
                                <td>vok layo</td>
                                <td><button class="btn btn-outline-secondary btn-sm">Edit</button></td>
                                <td><button class="btn btn-outline-secondary btn-sm">Update</button></td>
                                <td><button class="btn btn-outline-danger btn-sm">Delete</button></td>
                            </tr>
                            <!-- Add more rows as needed -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Adding New Staff -->
    <div class="modal fade" id="addStaffModal" tabindex="-1" aria-labelledby="addStaffModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addStaffModalLabel">Add New Staff</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="hostellerForm">
                        <div class="mb-3">
                            <label for="HostellerName" class="form-label">Name *</label>
                            <input type="text" class="form-control" id="HostellerName" placeholder="Enter name" required>
                        </div>
                        <div class="mb-3">
                            <label for="HostellerEmail" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="HostellerEmail" placeholder="Enter email" required>
                        </div>
                        <div class="mb-3">
                            <label for="HostellerPhoneNo" class="form-label">Phone no *</label>
                            <input type="text" class="form-control" id="HostellerPhoneNo" placeholder="Enter phone number" required>
                        </div>
                        <div class="mb-3">
                            <label for="HostellerUsername" class="form-label">Username *</label>
                            <input type="text" class="form-control" id="HostellerUsername" placeholder="Enter username" required>
                        </div>
                        <div class="mb-3">
                            <label for="HostellerPassword" class="form-label">Password *</label>
                            <input type="password" class="form-control" id="HostellerPassword" placeholder="Enter password" required>
                        </div>
                        <button type="button" class="btn btn-primary" id="saveHostellerBtn">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const successAlert = document.getElementById('successAlert');
        const saveHostellerBtn = document.getElementById('saveHostellerBtn');
        const hostellerTable = document.getElementById('hostellerTable');

        // Function to dynamically add a new row in the table
        function addHostellerToTable(email, name, phoneNo, username, password) {
            const newRow = document.createElement('tr');

            newRow.innerHTML = `
                <td>${email}</td>
                <td>${name}</td>
                <td>${phoneNo}</td>
                <td>${username}</td>
                <td>${password}</td>
                <td><button class="btn btn-outline-secondary btn-sm">Edit</button></td>
                <td><button class="btn btn-outline-secondary btn-sm">Update</button></td>
                <td><button class="btn btn-outline-danger btn-sm">Delete</button></td>
            `;

            hostellerTable.appendChild(newRow);
        }

        // Save the new hosteller to the table
        saveHostellerBtn.addEventListener('click', () => {
            const email = document.getElementById('HostellerEmail').value;
            const name = document.getElementById('HostellerName').value;
            const phoneNo = document.getElementById('HostellerPhoneNo').value;
            const username = document.getElementById('HostellerUsername').value;
            const password = document.getElementById('HostellerPassword').value;

            if (email && name && phoneNo && username && password) {
                addHostellerToTable(email, name, phoneNo, username, password); // Add to table
                successAlert.classList.remove('d-none'); // Show success alert
                setTimeout(() => successAlert.classList.add('d-none'), 3000); // Hide after 3 seconds
            } else {
                alert('Please fill in all required fields.');
            }
        });
    </script>
</body>
</html>
