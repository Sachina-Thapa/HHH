<!DOCTYPE html>
 <html lang="en">
 
 <head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Staff Management</title>
     <!-- Bootstrap 5 -->
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
     <style>
         body {
             background-color: #f8f9fa;
         }
 
            /* Sidebar CSS */
            .sidebar {
             margin: 0px;
             height: 150vh;
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
         .section-heading {
             margin-top: 2rem;
             margin-bottom: 1rem;
             font-weight: bold;
             color: #343a40;
         }
 
         .table-wrapper, .form-wrapper {
             background-color: white;
             padding: 1.5rem;
             border-radius: 0.5rem;
             box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
         }
 
         .form-wrapper {
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
                 <a href="staffdash.php">Dashboard</a>
                 <a href="sthosteller.php">Hosteller</a>
                 <a href="stvisitor.php">Booking/Visitor</a>
                 <a href="stfee.php">Fee Management</a>
                 <a href="stservices.php">Services</a>
                 <a href="stfeedback.php">Feedback</a>
                 <a href="billing.php">Billing</a>

                 <button class="logout-btn w-100">LOG OUT</button>
             </div>
 
             <!-- Main Content -->
             <div class="col-md-10 p-4">
                 <!-- Success Notification -->
                 <div id="successAlert" class="alert alert-success d-none">
                     Action completed successfully.
                 </div>
 
                 <!-- Section: Manage Hosteller -->
                 <h4 class="section-heading">Total Hosteller</h4>
                 <div class="table-wrapper">
            
                     <table class="table mt-3" id="hostellerTable">
                         <thead>
                             <tr>
                                 <th>Email</th>
                                 <th>Name</th>
                                 <th>Phone No</th>
                                 <th>Username</th>
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
                                 <td><button class="btn btn-outline-secondary btn-sm">Edit</button></td>
                                 <td><button class="btn btn-outline-secondary btn-sm">Update</button></td>
                                 <td><button class="btn btn-outline-secondary btn-sm">Delete</button></td>
                             </tr>
                             <tr>
                                 <td>anitalamichanel1@gmail.com</td>
                                 <td>Anita Lamichane</td>
                                 <td>254684635</td>
                                 <td>lamichaneanita537</td>
                                 <td><button class="btn btn-outline-secondary btn-sm">Edit</button></td>
                                 <td><button class="btn btn-outline-secondary btn-sm">Update</button></td>
                                 <td><button class="btn btn-outline-secondary btn-sm">Delete</button></td>
                             </tr>
                             <tr>
                                 <td>ritapoudel11@gmail.com</td>
                                 <td>Rita Poudel</td>
                                 <td>254646</td>
                                 <td>rita.poudel537</td>
                                 <td><button class="btn btn-outline-secondary btn-sm">Edit</button></td>
                                 <td><button class="btn btn-outline-secondary btn-sm">Update</button></td>
                                 <td><button class="btn btn-outline-secondary btn-sm">Delete</button></td>
                             </tr>
                             <tr>
                                 <td>gitakafle00@gmail.com</td>
                                 <td>Gita Kafle</td>
                                 <td>7777777</td>
                                 <td>Gita.kfle321</td>
                                 <td><button class="btn btn-outline-secondary btn-sm">Edit</button></td>
                                 <td><button class="btn btn-outline-secondary btn-sm">Update</button></td>
                                 <td><button class="btn btn-outline-secondary btn-sm">Delete</button></td>
                             </tr>
                             <tr>
                                 <td>smupandit21@gmail.com</td>
                                 <td>Smriti pandit</td>
                                 <td>7777777</td>
                                 <td>smriti537</td>

                                 <td><button class="btn btn-outline-secondary btn-sm">Edit</button></td>
                                 <td><button class="btn btn-outline-secondary btn-sm">Update</button></td>
                                 <td><button class="btn btn-outline-secondary btn-sm">Delete</button></td>
                             </tr>
                             <!-- Add more hosteller rows as needed -->
                         </tbody>
                     </table>
                 </div>

     <!-- Bootstrap JS -->
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
 
     <script>
         const successAlert = document.getElementById('successAlert');
         const saveServiceBtn = document.getElementById('saveServiceBtn');
 
         // Function to show success notification
         function showSuccessNotification() {
             successAlert.classList.remove('d-none');
             setTimeout(() => {
                 successAlert.classList.add('d-none');
             }, 3000);
         }
 
         // Function to add a new service
         saveServiceBtn.addEventListener('click', () => {
             const serviceType = document.getElementById('serviceType').value;
             const serviceDescription = document.getElementById('serviceDescription').value;
             const servicePrice = document.getElementById('servicePrice').value;
 
             if (serviceType && serviceDescription && servicePrice) {
                 // You can handle adding this data to the table or backend here
 
                 showSuccessNotification(); // Show success alert
             } else {
                 alert('Please fill in all required fields.');
             }
         });
     </script>
 </body>
 
 </html>