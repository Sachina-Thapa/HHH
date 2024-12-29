<!DOCTYPE html>
 <html lang="en">
 
 <head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Fee calculation</title>
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
         * {
   box-sizing: border-box;
 }
 
 body {
   font-family: Arial, sans-serif;
   background-color: #f8f9fa;
   color: #333;
   margin: 0;
   padding: 0;
 }
 
 .container {
   max-width: 80%;
   margin: 20px auto;
  
 }
 
 .title {
   font-size: 24px;
   font-weight: bold;
   margin-bottom: 20px;
   text-align: left;
 }
 
 .card {
   background-color: #fff;
   border: 1px solid #e0e0e0;
   border-radius: 8px;
   margin-bottom: 20px;
   padding: 20px;
 }
 
 .card-header {
   border-bottom: 1px solid #e0e0e0;
   padding-bottom: 10px;
   margin-bottom: 10px;
 }
 
 .card-title {
   font-size: 18px;
   font-weight: bold;
 }
 
 .card-content {
   padding-top: 10px;
 }
 
 .form-group {
   margin-bottom: 20px;
 }
 
 label {
   display: block;
   font-weight: bold;
   margin-bottom: 5px;
 }
 
 input[type="number"],
 input[type="file"] {
   width: 100%;
   padding: 8px;
   border: 1px solid #e0e0e0;
   border-radius: 4px;
   font-size: 16px;
 }
 
 .checkbox-group {
   margin-top: 10px;
 }
 
 .checkbox-item {
   display: flex;
   align-items: center;
   margin-top: 8px;
 }
 
 .checkbox-item label {
   margin-left: 5px;
 }
 
 .total-fee {
   font-size: 18px;
   font-weight: bold;
 }
 
 button {
   display: inline-block;
   background-color: #007bff;
   color: #fff;
   padding: 10px 20px;
   font-size: 16px;
   font-weight: bold;
   border: none;
   border-radius: 4px;
   cursor: pointer;
   margin-top: 10px;
 }
 
 button:disabled {
   background-color: #ccc;
   cursor: not-allowed;
 }
 
 .alert {
   display: flex;
   align-items: center;
   background-color: #ffeedb;
   padding: 10px;
   border-radius: 4px;
   margin-top: 20px;
 }
 
 .alert-icon {
   font-size: 20px;
   color: #ff9800;
   margin-right: 10px;
 }
 
 .alert-title {
   font-weight: bold;
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
             <div class="container">
     <h1 class="title"> Fee Management</h1>
 
     <!-- Stay Duration and Services Card -->
     <div class="card">
       <div class="card-header">
         <h2 class="card-title">Stay Duration and Services</h2>
       </div>
       <div class="card-content">
         <div class="form-group">
           <label for="duration">Duration (days)</label>
           <input id="duration" type="number" min="1" value="1">
         </div>
         <div class="form-group">
           <label>Select Services</label>
           <div class="checkbox-group">
             <div class="checkbox-item">
               <input type="checkbox" id="service-1">
               <label for="service-1">Breakfast (Rs.5/day)</label>
             </div>
             <div class="checkbox-item">
               <input type="checkbox" id="service-2">
               <label for="service-2">Lunch (Rs.8/day)</label>
             </div>
             <div class="checkbox-item">
               <input type="checkbox" id="service-3">
               <label for="service-3">Dinner (Rs.10/day)</label>
             </div>
             <div class="checkbox-item">
               <input type="checkbox" id="service-4">
               <label for="service-4">Laundry (Rs.15/day)</label>
             </div>
           </div>
         </div>
       </div>
     </div>
 
     <!-- Fee Summary Card -->
     <div class="card">
       <div class="card-header">
         <h2 class="card-title">Fee Summary</h2>
       </div>
       <div class="card-content">
         <p class="total-fee">Total Fee: Rs.<span id="total-fee">50.00</span></p>
         <button id="notify-button">Notify Hosteler</button>
       </div>
     </div>
 
     <!-- Payment Verification Card -->
     <div class="card">
       <div class="card-header">
         <h2 class="card-title">Payment Verification</h2>
       </div>
       <div class="card-content">
         <div class="form-group">
           <label for="voucher-upload">Upload Payment Voucher</label>
           <input id="voucher-upload" type="file">
         </div>
         <div id="verification-alert" class="alert" hidden>
           <span class="alert-icon">&#9888;</span>
           <div>
             <p class="alert-title">Verification Pending</p>
             <p>Your voucher has been uploaded and is awaiting verification.</p>
           </div>
         </div>
         <button id="verify-button" disabled>Verify Voucher</button>
       </div>
     </div>
   </div>
             </div>
         </div>
     </div>
   
 </body>
 
 </html>
  <script>
     document.addEventListener("DOMContentLoaded", () => {
       const baseRate = 50;
       const availableServices = [
         { id: 1, name: "Breakfast", price: 5 },
         { id: 2, name: "Lunch", price: 8 },
         { id: 3, name: "Dinner", price: 10 },
         { id: 4, name: "Laundry", price: 15 }
       ];
 
       let duration = 1;
       let selectedServices = [];
       let voucherUploaded = false;
       let voucherVerified = false;
 
       const durationInput = document.getElementById("duration");
       const totalFeeElement = document.getElementById("total-fee");
       const notifyButton = document.getElementById("notify-button");
       const voucherUploadInput = document.getElementById("voucher-upload");
       const verifyButton = document.getElementById("verify-button");
       const verificationAlert = document.getElementById("verification-alert");
 
       const calculateTotalFee = () => {
         const serviceFees = selectedServices.reduce((total, serviceId) => {
           const service = availableServices.find(s => s.id === serviceId);
           return total + (service ? service.price : 0);
         }, 0);
         const totalFee = (baseRate + serviceFees) * duration;
         totalFeeElement.textContent = totalFee.toFixed(2);
       };
 
       // Update duration and total fee on change
       durationInput.addEventListener("input", (event) => {
         duration = parseInt(event.target.value) || 1;
         calculateTotalFee();
       });
 
       // Handle service selection
       availableServices.forEach(service => {
         const checkbox = document.getElementById(`service-${service.id}`);
         checkbox.addEventListener("change", () => {
           if (checkbox.checked) {
             selectedServices.push(service.id);
           } else {
             selectedServices = selectedServices.filter(id => id !== service.id);
           }
           calculateTotalFee();
         });
       });
 
       // Notify Hosteler button
       notifyButton.addEventListener("click", () => {
         alert(`Your total fee for ${duration} day(s) is $${totalFeeElement.textContent}. Please upload your payment voucher.`);
       });
 
       // Handle voucher upload
       voucherUploadInput.addEventListener("change", (event) => {
         if (event.target.files && event.target.files.length > 0) {
           voucherUploaded = true;
           verifyButton.disabled = false;
           verificationAlert.hidden = false;
           alert("Your voucher has been uploaded and is pending verification.");
         }
       });
 
       // Verify voucher button
       verifyButton.addEventListener("click", () => {
         if (voucherUploaded) {
           setTimeout(() => {
             voucherVerified = true;
             verificationAlert.hidden = true;
             verifyButton.disabled = true;
             alert("Your payment has been verified. Thank you!");
           }, 2000); // Simulating verification delay
         }
       });
 
       // Initial calculation
       calculateTotalFee();
     });
   </script>