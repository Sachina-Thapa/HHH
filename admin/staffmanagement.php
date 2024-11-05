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
                     Well done! Staff added successfully.
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
                                 <th>Edit</th>
                                 <th>Update</th>
                                 <th>Delete</th>
                             </tr>
                         </thead>
                         <tbody>
                         <tr>
                                 <td>aakritipandit11@gmail.com</td>
                                 <td>Aakriti pandit</td>
                                 <td>7777777</td>
                                 <td>Ak.riti537</td>
                                 <td>vok layo</td>
                                 <td><button class="btn btn-outline-secondary btn-sm">Edit</button></td>
                                 <td><button class="btn btn-outline-secondary btn-sm">Update</button></td>
                                 <td><button class="btn btn-outline-secondary btn-sm">Delete</button></td>
                             </tr>
                             <tr>
                                 <td>anitalamichanel1@gmail.com</td>
                                 <td>Anita Lamichane</td>
                                 <td>254684635</td>
                                 <td>lamichaneanita537</td>
                                 <td>anitaanita</td>
                                 <td><button class="btn btn-outline-secondary btn-sm">Edit</button></td>
                                 <td><button class="btn btn-outline-secondary btn-sm">Update</button></td>
                                 <td><button class="btn btn-outline-secondary btn-sm">Delete</button></td>
                             </tr>
                             <tr>
                                 <td>ritapoudel11@gmail.com</td>
                                 <td>Rita Poudel</td>
                                 <td>254646</td>
                                 <td>rita.poudel537</td>
                                 <td>ritarita</td>
                                 <td><button class="btn btn-outline-secondary btn-sm">Edit</button></td>
                                 <td><button class="btn btn-outline-secondary btn-sm">Update</button></td>
                                 <td><button class="btn btn-outline-secondary btn-sm">Delete</button></td>
                             </tr>
                             <tr>
                                 <td>gitakafle00@gmail.com</td>
                                 <td>Gita Kafle</td>
                                 <td>7777777</td>
                                 <td>Gita.kfle321</td>
                                 <td>gitagita</td>
                                 <td><button class="btn btn-outline-secondary btn-sm">Edit</button></td>
                                 <td><button class="btn btn-outline-secondary btn-sm">Update</button></td>
                                 <td><button class="btn btn-outline-secondary btn-sm">Delete</button></td>
                             </tr>
                             <tr>
                                 <td>smupandit21@gmail.com</td>
                                 <td>Smriti pandit</td>
                                 <td>7777777</td>
                                 <td>smriti537</td>
                                 <td>hellosmu</td>
                                 <td><button class="btn btn-outline-secondary btn-sm">Edit</button></td>
                                 <td><button class="btn btn-outline-secondary btn-sm">Update</button></td>
                                 <td><button class="btn btn-outline-secondary btn-sm">Delete</button></td>
                             </tr>
                             <!-- Add more rows as needed -->
                         </tbody>
                     </table>
                     <!-- Add/Edit Staff Form -->
                     <div class="form-wrapper d-none" id="userForm">
                         <h4>ADD New Staff</h4>
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
                                 <input type="text" class="form-control" id="HostellerPhoneNo" placeholder="Enter Phone no" required>
                             </div>
                             <div class="mb-3">
                                 <label for="HostellerUsername" class="form-label">Username *</label>
                                 <input type="text" class="form-control" id="HostellerUsername" placeholder="Enter Username" required>
                             </div>
                             <div class="mb-3">
                                 <label for="HostellerPassword" class="form-label">Password *</label>
                                 <input type="password" class="form-control" id="HostellerPassword" placeholder="Enter password" required>
                             </div>
                             <button type="button" class="btn btn-primary" id="saveHostellerBtn">Save</button>
                             <button type="button" class="btn btn-secondary" id="cancelHostellerBtn">Cancel</button>
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
         const userForm = document.getElementById('userForm');
         const saveHostellerBtn = document.getElementById('saveHostellerBtn');
         const cancelHostellerBtn = document.getElementById('cancelHostellerBtn');
         const hostellerTable = document.getElementById('hostellerTable').getElementsByTagName('tbody')[0];
         const searchInput = document.getElementById('searchInput');
         const searchBtn = document.getElementById('searchBtn');
 
         // Show form when clicking 'Add New Hosteller'
         addNewHostellerBtn.addEventListener('click', () => {
             userForm.classList.remove('d-none');
         });
 
         // Hide form when clicking 'Cancel'
         cancelHostellerBtn.addEventListener('click', () => {
             userForm.classList.add('d-none');
         });
 
         // Function to dynamically add a new row in the table
         function addHostellerToTable(email, name, phoneNo, username, password) {
             const newRow = hostellerTable.insertRow(); // Create new row
 
             // Insert new cells in the row
             const emailCell = newRow.insertCell(0);
             const nameCell = newRow.insertCell(1);
             const phoneNoCell = newRow.insertCell(2);
             const usernameCell = newRow.insertCell(3);
             const passwordCell = newRow.insertCell(4);
             const editCell = newRow.insertCell(5);
             const updateCell = newRow.insertCell(6);
             const deleteCell = newRow.insertCell(7);
 
             // Set cell values
             emailCell.innerText = email;
             nameCell.innerText = name;
             phoneNoCell.innerText = phoneNo;
             usernameCell.innerText = username;
             passwordCell.innerText = password;
 
             // Add buttons for edit, update, and delete (currently, they are non-functional)
             editCell.innerHTML = `<button class="btn btn-outline-secondary btn-sm">Edit</button>`;
             updateCell.innerHTML = `<button class="btn btn-outline-secondary btn-sm">Update</button>`;
             deleteCell.innerHTML = `<button class="btn btn-outline-secondary btn-sm">Delete</button>`;
         }
 
         // Show notification
         function showSuccessNotification() {
             successAlert.classList.remove('d-none');
             setTimeout(() => {
                 successAlert.classList.add('d-none');
             }, 3000); // Hide after 3 seconds
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
                 showSuccessNotification(); // Show success alert
                 userForm.classList.add('d-none'); // Hide form after saving
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