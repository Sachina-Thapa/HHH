<?php 
        require('inc/db.php');?>
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

 
         /* Card styling for stats */
         .stats-card {
             background-color: white;
             border-radius: 10px;
             padding: 20px;
             box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
             text-align: center;
         }
 
         .stats-card h5 {
             font-size: 18px;
             color: #6c757d;
         }
 
         .stats-card h3 {
             font-size: 28px;
             font-weight: bold;
             color: #343a40;
         }
 
         .table-wrapper {
             margin-top: 2rem;
             background-color: white;
             padding: 10px;
             border-radius: 10px;
             box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
         }
 
         .chart-wrapper {
             background-color: white;
             padding: 18px;
             border-radius: 50px;
             box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
             margin-top: 20px;
             width: 100px5px;
             text-align: center;
         }
 
         .header-title {
             font-size: 20px;
             font-weight: bold;
             margin-bottom: 15px;
         }
 
         .chart-wrapper h5 {
             margin-bottom: 30px;
             font-size: 15px;
             font-weight: bold;
         }
 
         /* Flexbox for side-by-side charts */
      /* Adjust the gap between the charts */
 .chart-container {
     display: flex;
     justify-content: space-between;
     gap: 2px; /* Reduce the gap between charts */
 }
 
        /* Add this CSS for chart canvas sizing */
 .chart-container .chart canvas {
     width: 100% !important;
     height: 350px !important; /* Set a fixed height */
 }
 
         /* Responsive adjustments */
         @media (max-width: 500px) {
             .content {
                 margin-left: 0;
                 padding: 50px;
             }
 
             .chart-container {
                 flex-direction: column;
             }
 
             .chart-container .chart {
                 max-width: 50%;
             }
         }
     </style>
 </head>
 <body>
 
   <div class="container-fluid">
         <div class="row">
  <!-- Sidebar -->
  <?php 
  require('inc/sideMenu.php');  

        // Query to count total staff
        $sql = "SELECT COUNT(*) as total FROM staff_data;";
        $result = $conn->query($sql);
        $total_staff = 0; // Initialize the variable

        if ($result) {
            $row = $result->fetch_assoc();
            $total_staff = $row['total']; // Get the total count
        } else {
            echo "Error in query: " . $conn->error; // Handle query error
        }

         // Query to count total Hosteler
         $sql = "SELECT COUNT(*) as total FROM hostelers;";
         $result = $conn->query($sql);
         $total_hostelers = 0; // Initialize the variable
 
         if ($result) {
             $row = $result->fetch_assoc();
             $total_hostelers= $row['total']; // Get the total count
         } else {
             echo "Error in query: " . $conn->error; // Handle query error
         }

         // Query to count total Guestes
         $sql = "SELECT COUNT(*) as total FROM visitorform;";
         $result = $conn->query($sql);
         $total_visitor = 0; // Initialize the variable
 
         if ($result) {
             $row = $result->fetch_assoc();
             $total_visitor= $row['total']; // Get the total count
         } else {
             echo "Error in query: " . $conn->error; // Handle query error
         }
?>
             <!-- Main Content -->
             <div class="col-md-10 content-wrapper py-4 px-4">
                 <!-- Success Notification -->
                 <div id="successAlert" class="alert alert-success d-none">
                 </div>
 
                 <!-- Statistics Section -->
                 <div class="row">
                     <div class="col-md-4 mb-4">
                         <div class="card stats-card">
                             <h5>Total Hostellers</h5>
                             <h3><?php echo $total_hostelers; ?></h3>
                         </div>
                     </div>
                     <div class="col-md-4 mb-4">
                         <div class="card stats-card">
                             <h5>Total Staff</h5>
                             <h3><?php echo $total_staff; ?></h3> <!-- Display the total staff count -->                         </div>
                     </div>
                     <div class="col-md-4 mb-4">
                         <div class="card stats-card">
                         <h5>Total Visitors</h5>
                         <h3><?php echo $total_visitor; ?></h3>
                         </div>
                     </div>
                 </div>
 
                 <!-- Table Section -->
                 <div class="table-wrapper">
                     <h5>Room Occupancy and Pricing</h5>
                     <table class="table table-bordered">
                     
         <?php
            // Query to select room data
                $sql = "SELECT rid, rno, rtype, rprice FROM room"; 
                $result = $conn->query($sql);
                     
                if ($result && $result->num_rows > 0) 
                {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td id='rno_" . $row["rid"] . "'>" . $row["rno"] . "</td>";
                        echo "<td id='rtype_" . $row["rid"] . "'>" . $row["rtype"] . "</td>";
                        echo "<td id='rprice_" . $row["rid"] . "'> रु " . number_format($row["rprice"], 2) . "</td>";
                        echo "</tr>";
                    }
                }
            
                else {
                echo "<tr><td colspan='4'>No rooms found</td></tr>";
                }
        ?>
                  </table>       
                 </div>
 
                 <!-- Chart Section -->
                 <div class="chart-wrapper">
                     <h5>Hostel Overview</h5>
                     <div class="chart-container">
                         <!-- Line Chart on the Left -->
                         <div class="chart">
                             <canvas id="lineChart"></canvas>
                         </div>
 
                         <!-- Pie Chart on the Right -->
                         <div class="chart">
                             <canvas id="pieChart"></canvas>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
     </div>
 
     <!-- Bootstrap JS -->
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
 
     <!-- Line Chart Script -->
     <script>
         const lineData = {
             labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July','Aug','Sep','Oct','Nov','Dec'],
             datasets: [{
                 label: 'Monthly Revenue',
                 data: [3000, 4000, 3500, 4500, 5000, 4800, 5200,5000,5300,5400,5800,6000],
                 borderColor: '#007bff',
                 backgroundColor: 'rgba(0, 123, 255, 0.1)',
                 fill: true
             }]
         };
 
         const lineConfig = {
             type: 'line',
             data: lineData,
             options: {
                 responsive: true,
                 maintainAspectRatio: true,
                 scales: {
                     x: {
                         beginAtZero: true
                     },
                     y: {
                         beginAtZero: true
                     }
                 },
                 plugins: {
                     legend: {
                         position: 'top'
                     }
                 }
             }
         };
 
         const lineChart = new Chart(
             document.getElementById('lineChart'),
             lineConfig
         );
     </script>
 
     <!-- Pie Chart Script -->
     <script>
         const pieData = {
             labels: ['Hostellers', 'Staff', 'Guests'],
             datasets: [{
                 label: 'Hostel Overview',
                 data: [50, 10, 5],
                 backgroundColor: ['#007bff', '#28a745', '#ffc107'],
                 hoverOffset: 4
             }]
         };
 
         const pieConfig = {
             type: 'pie',
             data: pieData,
             options: {
                 responsive: true,
                 maintainAspectRatio: true,
                 plugins: {
                     legend: {
                         position: 'bottom'
                     }
                 }
             }
         };
 
         const pieChart = new Chart(
             document.getElementById('pieChart'),
             pieConfig
         );
     </script>
 
 </body>
 </html>