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

// Fetch services from the database for selection
$services = [];
$stmt = $conn->prepare("SELECT seid, name, price FROM services");
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $services[] = $row;
    }
} else {
    $error_message = "No services found.";
}
$stmt->close();

// Fetch services requested by the logged-in hosteler
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
            padding: 20px; /* Optional for spacing */
            overflow-y: auto; /* Ensures vertical scrolling */
        }

        .signup-card {
            width: 100%;
            max-width: 800px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            box-sizing: border-box;
            margin: 0 auto; /* Center horizontally */
        }

        table {
            width: 100%;
            margin-top: 20px;
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
        <?php foreach ($services as $service): ?>
            <div class="mb-3 form-check">
                <input class="form-check-input" type="checkbox" name="services[]" value="<?php echo htmlspecialchars($service['seid']); ?>" id="service_<?php echo htmlspecialchars($service['seid']); ?>">
                <label class="form-check-label" for="service_<?php echo htmlspecialchars($service['seid']); ?>">
                    <?php echo htmlspecialchars($service['name']); ?> ($<?php echo htmlspecialchars($service['price']); ?>)
                </label>
            </div>
        <?php endforeach; ?>
        <button type="submit" name="submit" class="btn btn-primary">Request Services</button>
    </form>

    <!-- Requested services table -->
    <h3 class="mt-4">Your Requested Services</h3>
    <?php if (!empty($requested_services)): ?>
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Service ID</th>
                <th>Name</th>
                <th>Price</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($requested_services as $service): ?>
                <tr>
                    <td><?php echo htmlspecialchars($service['seid']); ?></td>
                    <td><?php echo htmlspecialchars($service['name']); ?></td>
                    <td>$<?php echo htmlspecialchars($service['price']); ?></td>
                    <td><?php echo htmlspecialchars(ucfirst($service['status'])); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No services requested yet.</p>
    <?php endif; ?>
</div>
</body>
</html>
