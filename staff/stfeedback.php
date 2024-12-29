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

                                 
                 <!-- Section: View Feedback -->
                 <h4 class="section-heading">View Feedback</h4>
                 <div class="table-wrapper">
                     <h5>Feedback List</h5>
                     <table class="table mt-3" id="feedbackTable">
                         <thead>
                             <tr>
                                 <th>Hosteller Name</th>
                                 <th>Feedback</th>
                                 <th>Rating</th>
                             </tr>
                         </thead>
                         <tbody>
                             <tr>
                                 <td>Aakriti Pandit</td>
                                 <td>Great service and food!</td>
                                 <td>4.5/5</td>
                             </tr>
                             <!-- Add more feedback rows as needed -->
                         </tbody>
                     </table>
                 </div>
             </div>
         </div>
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