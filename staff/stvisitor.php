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

                 <button class="logout-btn w-100">LOG OUT</button>
             </div>

 
             <!-- Main Content -->
             <div class="col-md-10 p-4">
                 <!-- Success Notification -->
                 <div id="successAlert" class="alert alert-success d-none">
                     Action completed successfully.
                 </div>
 
                 <!-- Section: Manage Visitor Form -->
                 <h4 class="section-heading">Manage Visitor Form</h4>
                 <div class="table-wrapper">
                     <h5>Visitor List</h5>
                     <table class="table mt-3" id="visitorTable">
                         <thead>
                             <tr>
                                 <th>Visitor Name</th>
                                 <th>Hosteller Name</th>
                                 <th>Purpose</th>
                                 <th>Status</th>
                                 <th>Accept/Decline</th>
                             </tr>
                         </thead>
                         <tbody>
                             <tr>
                                 <td>Smriti Pandit</td>
                                 <td>Aakriti Pandit</td>
                                 <td>Personal</td>
                                 <td>Pending</td>
                                 <td>
                                     <button class="btn btn-outline-success btn-sm">Accept</button>
                                     <button class="btn btn-outline-danger btn-sm">Decline</button>
                                 </td>
                             </tr>
                             <!-- Add more visitor rows as needed -->
                         </tbody>
                     </table>
                 </div>
 
 
     <!-- Bootstrap JS -->
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
 
     <script>
         const successAlert = document.getElementById('successAlert');
         const saveServiceBtn = document.getElementById('saveServiceBtn');
 
        
     </script>
 </body>
 
 </html>