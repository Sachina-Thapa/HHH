<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
require('../admin/inc/db.php');

// Verify database connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Roboto', sans-serif;
        }
        .main-content {
            margin-left: 250px; /* Width of your sidenav */
            padding: 20px;
            transition: margin-left 0.3s;
        }

        .stats-card {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            border-left: 5px solid #0d6efd;
            margin-bottom: 20px;
        }

        .stats-card h5 {
            font-size: 18px;
            color: #6c757d;
        }

        .stats-card h3 {
            font-size: 28px;
            font-weight: bold;
            color: #0d6efd;
        }

        .chart-wrapper {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            text-align: center;
        }

        .chart-wrapper h5 {
            margin-bottom: 30px;
            font-size: 15px;
            font-weight: bold;
        }

        .chart-container {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .chart-container .chart canvas {
            width: 100% !important;
            height: 350px !important;
        }

        @media (max-width: 768px) {
            .chart-container {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    

    <?php require('inc/sidemenu.php'); ?>
    
    <div class="main-content">
        <div class="container-fluid p-0">
            <div class="row">
            <?php
            // Query to count total Hosteler
            $sql = "SELECT COUNT(*) as total FROM hostelers;";
            $result = $conn->query($sql);
            $total_hostelers = 0;

            if ($result) {
                $row = $result->fetch_assoc();
                $total_hostelers = $row['total'];
            }

            // Get total number of rooms and occupancy data
            $total_rooms_query = "SELECT COUNT(*) as total FROM room";
            $total_rooms_result = $conn->query($total_rooms_query);
            $total_rooms = 0;
            if ($total_rooms_result) {
                $row = $total_rooms_result->fetch_assoc();
                $total_rooms = $row['total'];
            }

            // Get occupied rooms count
            $occupied_rooms_query = "SELECT COUNT(DISTINCT r.rno) as occupied 
                                    FROM room r 
                                    INNER JOIN booking b ON r.rno = b.rno 
                                    WHERE b.bstatus = 'confirmed' 
                                    AND CURRENT_DATE BETWEEN b.check_in AND b.check_out";
            $occupied_result = $conn->query($occupied_rooms_query);
            $occupied_rooms = 0;
            if ($occupied_result) {
                $row = $occupied_result->fetch_assoc();
                $occupied_rooms = $row['occupied'];
            }

            // Calculate available rooms
            $available_rooms = $total_rooms - $occupied_rooms;

            // Initialize revenue arrays
            $monthly_revenue = array_fill(0, 12, 0);
            $visitor_revenue = array_fill(0, 12, 0);
            $service_revenue = array_fill(0, 12, 0);

            try {
                // Query for hosteler fees
                $fee_query = "SELECT 
                                MONTH(confirmed_date) as month, 
                                SUM(CAST(REPLACE(total, ',', '') AS DECIMAL(10,2))) as monthly_total 
                             FROM fee 
                             WHERE YEAR(confirmed_date) = YEAR(CURRENT_DATE) 
                             AND status = 'confirmed' 
                             GROUP BY MONTH(confirmed_date)";

                $fee_result = $conn->query($fee_query);
                if ($fee_result) {
                    while ($row = $fee_result->fetch_assoc()) {
                        $month_index = (int)$row['month'] - 1;
                        if ($month_index >= 0 && $month_index < 12) {
                            $monthly_revenue[$month_index] += floatval($row['monthly_total']);
                        }
                    }
                }

                // Query for visitor fees
                $visitor_query = "SELECT 
                                MONTH(confirm_date) as month, 
                                SUM(fee) as monthly_total 
                                FROM visitorform 
                                WHERE YEAR(confirm_date) = YEAR(CURRENT_DATE) 
                                AND status = 'accepted' 
                                AND confirm_date IS NOT NULL
                                GROUP BY MONTH(confirm_date)";

                $visitor_result = $conn->query($visitor_query);
                if ($visitor_result) {
                    while ($row = $visitor_result->fetch_assoc()) {
                        $month_index = (int)$row['month'] - 1;
                        if ($month_index >= 0 && $month_index < 12) {
                            $visitor_revenue[$month_index] = floatval($row['monthly_total']);
                            $monthly_revenue[$month_index] += $visitor_revenue[$month_index];
                        }
                    }
                }

                // Query for hostel services
                $service_query = "SELECT 
                            MONTH(payment_date) as month, 
                            SUM(total) as monthly_total 
                         FROM hservice 
                         WHERE YEAR(payment_date) = YEAR(CURRENT_DATE) 
                         AND payment_status = 'confirmed' 
                         GROUP BY MONTH(payment_date)";

                $service_result = $conn->query($service_query);
                if ($service_result) {
                    while ($row = $service_result->fetch_assoc()) {
                        $month_index = (int)$row['month'] - 1;
                        if ($month_index >= 0 && $month_index < 12) {
                            $service_revenue[$month_index] = floatval($row['monthly_total']);
                            $monthly_revenue[$month_index] += $service_revenue[$month_index];
                        }
                    }
                }
            } catch (Exception $e) {
                error_log("Error calculating revenue: " . $e->getMessage());
            }

            // Convert to JavaScript arrays
            $revenue_data = json_encode(array_values($monthly_revenue));
            $visitor_data = json_encode(array_values($visitor_revenue));
            $service_data = json_encode(array_values($service_revenue));
            ?>

            <!-- Statistics Cards -->
            <div class="col-md-4 mb-4">
                <div class="stats-card">
                    <h5>Total Hostellers</h5>
                    <h3><?php echo $total_hostelers; ?></h3>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="stats-card">
                    <h5>Available Rooms</h5>
                    <h3><?php echo $available_rooms; ?></h3>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="stats-card">
                    <h5>Booked Rooms</h5>
                    <h3><?php echo $occupied_rooms; ?></h3>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="chart-wrapper">
                <h5>Hostel Reports Overview</h5>
                <div class="chart-container">
                    <!-- Line Chart -->
                    <div class="chart">
                        <canvas id="lineChart"></canvas>
                    </div>

                    <!-- Pie Chart -->
                    <div class="chart">
                        <canvas id="pieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Charts Scripts -->
    <script>
        // Line Chart
        const lineData = {
            labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [
                {
                    label: 'Total Revenue (Rs)',
                    data: <?php echo $revenue_data; ?>,
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    fill: true
                },
                {
                    label: 'Visitor Revenue (Rs)',
                    data: <?php echo $visitor_data; ?>,
                    borderColor: '#ffc107',
                    backgroundColor: 'rgba(255, 193, 7, 0.1)',
                    fill: true
                },
                {
                    label: 'Service Revenue (Rs)',
                    data: <?php echo $service_data; ?>,
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    fill: true
                }
            ]
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
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rs ' + value.toLocaleString();
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': Rs ' + context.raw.toLocaleString();
                            }
                        }
                    }
                }
            }
        };

        const lineChart = new Chart(
            document.getElementById('lineChart'),
            lineConfig
        );

        // Pie Chart
        const pieData = {
            labels: ['Available Rooms', 'Occupied Rooms'],
            datasets: [{
                label: 'Room Status',
                data: [<?php echo $available_rooms; ?>, <?php echo $occupied_rooms; ?>],
                backgroundColor: [
                    '#28a745',  // Green for Available
                    '#dc3545'   // Red for Occupied
                ],
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
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = <?php echo $total_rooms; ?>;
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
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