<?php
session_start();
// Add authentication check here

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - Her Home Hostel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container mt-4">
        <h1>Staff Dashboard</h1>
        <div class="row">
            <div class="col-md-6">
                <h2>Your Records</h2>
                <!-- Add a table with staff records here -->
            </div>
            <div class="col-md-6">
                <h2>Analysis Graph</h2>
                <canvas id="staffAnalysisChart"></canvas>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add Chart.js code here to create the analysis graph
        const ctx = document.getElementById('staffAnalysisChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Performance Score',
                    data: [12, 19, 3, 5, 2, 3],
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>

<div class="col-md-4">
            <div class="stat-card">
                <h2><?php echo $checkin; ?></h2>
                <p>Check-In</p>
                <!-- <i class="bi bi-person-plus"></i> -->
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <h2><?php echo $checkout; ?></h2>
                <p>Check-Out</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <h2><?php echo $feecollected; ?></h2>
                <p>Fee Collected</p>
            </div>
        </div>