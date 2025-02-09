<?php
session_start();
require('inc/db.php');
require('inc/hsidemenu.php');

// Initialize message and input variables
$success_message = '';
$error_message = '';
$total_price = 0;

// Check if user is logged in and retrieve username
if (!isset($_SESSION['username'])) {
    header("Location: http://localhost/hhh/index.php");
    exit();
}

$username = $_SESSION['username'];

// Retrieve the hosteler ID from the database
$stmt = $conn->prepare("SELECT id FROM hostelers WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $hid = $row['id']; // Get the hosteler ID
} else {
    $_SESSION['error_message'] = "User not found.";
    header("Location: http://localhost/hhh/index.php");
    exit();
}

// Insert service data in hservice table if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $services = $_POST['services'] ?? [];
    $total_price = 0;

    if (!empty($services)) {
        foreach ($services as $service_id) {
            // Fetch service details
            $stmt = $conn->prepare("SELECT name, price FROM services WHERE seid = ?");
            $stmt->bind_param("i", $service_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $service = $result->fetch_assoc();
                $service_name = $service['name'];
                $service_price = $service['price'];
                $total_price += $service_price;

                // Insert service into `hservice` table
                $insert_stmt = $conn->prepare(
                    "INSERT INTO hservice (seid, name, price, hid, status, total)
                     VALUES (?, ?, ?, ?, 'pending', ?)"
                );
                $insert_stmt->bind_param("isdid", $service_id, $service_name, $service_price, $hid, $total_price);

                if (!$insert_stmt->execute()) {
                    $error_message = "Error saving service: " . $insert_stmt->error;
                    break;
                }
            } else {
                $error_message = "Service not found for ID: " . $service_id;
                break;
            }
        }

        if (empty($error_message)) {
            $success_message = "Wait, your service request is pending... Total Price: $" . number_format($total_price, 2);
        }
    } else {
        $error_message = "No services selected.";
    }

    $stmt->close();
}

// Fetch all services from the database
$all_services = [];
$stmt = $conn->prepare("SELECT seid, name, price FROM services");
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $all_services[] = $row;
    }
}
$stmt->close();

// Fetch all requested services by the logged-in hosteler
$requested_services = [];
$stmt = $conn->prepare("SELECT seid, name, price, status FROM hservice WHERE hid = ?");
$stmt->bind_param("i", $hid);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $requested_services[] = $row;
    }
}
$stmt->close();

// Create arrays for pending and accepted services
$pending_services = [];
$accepted_services = [];
$accepted_service_ids = []; // Track accepted service IDs

foreach ($requested_services as $service) {
    if ($service['status'] === 'pending') {
        $pending_services[] = $service;
    } elseif ($service['status'] === 'accepted') {
        $accepted_services[] = $service;
        $accepted_service_ids[] = $service['seid'];
    }
}

// Filter available services to exclude those that are pending or accepted
$available_services = array_filter($all_services, function($service) use ($pending_services, $accepted_service_ids) {
    // Check if service is not in pending or accepted state
    $is_pending = false;
    foreach ($pending_services as $pending) {
        if ($pending['seid'] == $service['seid']) {
            $is_pending = true;
            break;
        }
    }
    return !$is_pending && !in_array($service['seid'], $accepted_service_ids);
});

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Submission</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            background-color: #f8f9fa;
            padding: 20px;
            overflow-y: auto;
        }

        .signup-card {
            width: 100%;
            max-width: 800px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            box-sizing: border-box;
            margin: 0 auto;
        }

        table {
            width: 100%;
            margin-top: 20px;
        }

        .service-item {
            padding: 15px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            margin-bottom: 10px;
            background-color: #fff;
        }

        .service-item.accepted {
            background-color: #d4edda;
            border-color: #c3e6cb;
        }

        .service-status {
            font-size: 0.9em;
            padding: 3px 8px;
            border-radius: 12px;
            display: inline-block;
            margin-left: 10px;
        }

        .status-accepted {
            background-color: #28a745;
            color: white;
        }

        .status-pending {
            background-color: #ffc107;
            color: #000;
        }
    </style>
</head>
<body>
<div class="signup-card">
    <h3 class="card-title">Select Services</h3>

    <!-- Show success or error messages -->
    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
    <?php endif; ?>
    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <!-- Service selection form -->
    <form method="POST" action="">
        <!-- Display Available Services -->
        <?php if (!empty($available_services)): ?>
            <h4 class="mt-4 mb-3">Available Services</h4>
            <?php foreach ($available_services as $service): ?>
                <div class="service-item">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="services[]" 
                               value="<?php echo htmlspecialchars($service['seid']); ?>" 
                               id="service_<?php echo htmlspecialchars($service['seid']); ?>">
                        <label class="form-check-label" for="service_<?php echo htmlspecialchars($service['seid']); ?>">
                            <?php echo htmlspecialchars($service['name']); ?> 
                            (Rs <?php echo htmlspecialchars($service['price']); ?>)
                        </label>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Display Accepted Services -->
        <?php if (!empty($accepted_services)): ?>
            <h4 class="mt-4 mb-3">Your Active Services</h4>
            <?php foreach ($accepted_services as $service): ?>
                <div class="service-item accepted">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>
                            <?php echo htmlspecialchars($service['name']); ?> 
                            (Rs <?php echo htmlspecialchars($service['price']); ?>)
                            <span class="service-status status-accepted">Active</span>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (!empty($available_services)): ?>
            <button type="submit" name="submit" class="btn btn-primary mt-4">Request Selected Services</button>
        <?php endif; ?>
    </form>

    <!-- Pending Services Section -->
    <?php if (!empty($pending_services)): ?>
        <h4 class="mt-4 mb-3">Pending Service Requests</h4>
        <?php foreach ($pending_services as $service): ?>
            <div class="service-item">
                <div class="d-flex justify-content-between align-items-center">
                    <span>
                        <?php echo htmlspecialchars($service['name']); ?> 
                        ($<?php echo htmlspecialchars($service['price']); ?>)
                        <span class="service-status status-pending">Pending</span>
                    </span>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>