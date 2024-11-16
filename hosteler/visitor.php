<?php
session_start(); // Start the session
require('../admin/inc/db.php');
require('inc/hsidemenu.php');

// Initialize message and input variables
$success_message = '';
$error_message = '';
$vname = '';
$relation = '';
$reason = '';
$days = 1; // Default value for days, can be set to any reasonable default

// Check if user is logged in and retrieve username
if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header("Location: http://localhost/hhh/index.php");
    exit();
}

$username = $_SESSION['username']; // Assuming username is stored in session

// Retrieve the hosteler ID from the database
$stmt = $conn->prepare("SELECT id FROM hostelers WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $hid = $row['id']; // Get the hosteler ID
} else {
    // Handle the case where the user is not found in the database
    $_SESSION['error_message'] = "User  not found.";
    header("Location: http://localhost/hhh/index.php");
    exit();
}

// Insert visitor data in visitorform table
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO visitorform (vname, relation, reason, days, hid) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $vname, $relation, $reason, $days, $hid);
    
    // Get the values from the form
    $vname = $_POST['name']; // Corrected to match the input name
    $relation = $_POST['relation']; // Added to capture relationship
    $reason = $_POST['reason']; // Capture reason of visit
    $days = $_POST['days']; // Corrected to match the input name

    // Execute the statement and check for success
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Visitor's Request is Pending."; // Store success message in session
        // Clear the form inputs after submission
        $vname = '';
        $relation = '';
        $reason = '';
        $days = 1; // Reset to default value
    } else {
        $_SESSION['error_message'] = "Error: " . $stmt->error; // Store error message in session
    }
    $stmt->close();
}

// Check for messages in session
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']); // Clear the message after displaying
}

if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']); // Clear the message after displaying
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Form</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center; /* Center horizontally */
            align-items: center; /* Center vertically */
            height: 100vh; /* Full viewport height */
            margin: 0; /* Remove default margin */
            background-color: #f8f9fa; /* Light background color */
        }

        .signup-card {
            width: 100%; /* Full width for the card */
            max-width: 600px; /* Increased max width for the signup card */
            background-color: #ffffff; /* White background for the card */
            border-radius: 8px; /* Rounded corners */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Subtle shadow */
            padding: 30px; /* Increased padding inside the card */
            box-sizing: border-box; /* Include padding in width calculation */
        }

        .card-title {
            color: #007bff; /* Bootstrap primary color for the title */
            margin-bottom: 20px; /* Space below the title */
        }

        .form-label {
            color: #495057; /* Darker color for labels */
        }

        .form-control {
            border: 1px solid #ced4da; /* Default border color */
            border-radius: 4px; /* Rounded corners for input fields */
        }

        .form-control:focus {
            border-color: #007bff; /* Change border color on focus */
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25); /* Focus shadow */
        }

        .btn-primary {
            background-color: #007bff; /* Bootstrap primary color */
            border-color: #007bff; /* Border color for the button */
        }

        .btn-primary:hover {
            background-color: #0056b3; /* Darker shade on hover */
            border-color: #0056b3; /* Darker border on hover */
        }

        .mb-3 {
            margin-bottom: 15px; /* Adjust spacing between form elements */
        }

        .alert {
            transition: opacity 0.5s ease; /* Smooth transition for fading out */
        }
    </style>
</head>
<body>
    <div class="signup-card">
        <h3 class="card-title">Visitor Form</h3>
        <form action="" method="POST" autocomplete="">
            <div class="mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input class="form-control" type="text" name="name" id="name" placeholder="Enter your full name" value="<?php echo htmlspecialchars($vname); ?>" required>
            </div>
            <div class="mb-3">
                <label for="relation" class="form-label">Relationship</label>
                <input class="form-control" type="text" name="relation" id="relation" placeholder="Enter your relationship with the visitor" value="<?php echo htmlspecialchars($relation); ?>" required>
            </div>
            <div class="mb-3">
                <label for="reason" class="form-label">Reason of Visit</label>
                <textarea class="form-control" id="reason" name="reason" rows="4" placeholder="Enter reason for visit" required><?php echo htmlspecialchars($reason); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="days" class="form-label">No of Days of Visit</label>
                <input type="number" class="form-control" id="days" name="days" min="1" placeholder="Enter number of days" value="<?php echo htmlspecialchars($days); ?>" required>
            </div>
            <div class="mb-3">
                <input class="form-control button" type="submit" name="submit" value="Submit">
                <input class="form-control button" type="button" name="cancel" value="Cancel" onclick="window.location.href = window.location.pathname;">
            </div>
        </form>
        <?php if ($success_message): ?>
            <div class="alert alert-success" role="alert" id="success-message">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="alert alert-danger" role="alert" id="error-message">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Function to hide messages after 3 seconds
        setTimeout(function() {
            var successMessage = document.getElementById('success-message');
            var errorMessage = document.getElementById('error-message');
            if (successMessage) {
                successMessage.style.opacity = '0';
                setTimeout(function() { successMessage.style.display = 'none'; }, 500); // Hide after fade out
            }
            if (errorMessage) {
                errorMessage.style.opacity = '0';
                setTimeout(function() { errorMessage.style.display = 'none'; }, 500); // Hide after fade out
            }
        }, 3000);
    </script>
</body>
</html>