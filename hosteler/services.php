<?php
session_start();
require('inc/db.php');
require('inc/hsidemenu.php');

// Initialize message and input variables
$success_message = '';
$error_message = '';
$total_price = 0;  // Track the total price

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
    $_SESSION['error_message'] = "User  not found.";
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

// Fetch services from the database
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
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f8f9fa;
        }

        .signup-card {
            width: 100%;
            max-width: 600px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            box-sizing: border-box;
            max-height: 80vh;
            overflow-y: auto;
        }

        .card -title {
            color: #007bff;
            margin-bottom: 20px;
        }

        .form-label {
            color: #495057;
        }

        .form-control {
            border: 1px solid #ced4da;
            border-radius: 4px;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .mb-3 {
            margin-bottom: 15px;
        }

        .alert {
            transition: opacity 0.5s ease;
        }

        #service-form {
            display: block;
        }

        #pending-message {
            display: none;
        }
    </style>
</head>
<body>
    <div class="signup-card">
        <h3 class="card-title">Select Services</h3>

        <!-- Show success or error messages -->
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success" id="pending-message">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <!-- Service selection form -->
        <div id="service-form">
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
        </div>
    </div>

    <script>
        // If success message is displayed, hide the form and show the message, then hide after 3 seconds
        <?php if (!empty($success_message)): ?>
            document.getElementById('pending-message').style.display = 'block';
            document.getElementById('service-form').style.display = 'none';
            setTimeout(function() {
                document.getElementById('pending-message').style.display = 'none';
                document.getElementById('service-form').style.display = 'block';
            }, 5000); // Wait for 3 seconds before hiding the message and showing the form
        <?php endif; ?>
    </script>
</body>
</html>