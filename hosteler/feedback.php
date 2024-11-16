<?php
session_start(); // Start the session
require('../admin/inc/db.php');
require('inc/hsidemenu.php');

// Initialize messages
$success_message = '';
$error_message = '';

// Check if user is logged in and retrieve username
if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header("Location: http://localhost/hhh/index.php");
    exit();
}

$username = $_SESSION['username']; // Assuming username is stored in session

// Retrieve the user ID from the database
$stmt = $conn->prepare("SELECT id FROM hostelers WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $hid = $row['id']; // Get the user ID
} else {
    // Handle the case where the user is not found in the database
    $_SESSION['error_message'] = "User  not found.";
    header("Location: http://localhost/hhh/index.php");
    exit();
}

// Insert feedback into the database
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    // Get the values from the form
    $ftext = $_POST['ftext']; // Feedback text
    $fdate = date('Y-m-d H:i:s'); // Get the current date and time

    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO feedback (ftext, username, hid, fdate) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $ftext, $username, $hid, $fdate); // 's' for string, 'i' for integer

    // Execute the statement and check for success
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Your feedback has been sent."; // Store success message in session
        $stmt->close();
        // Redirect to the same page to prevent resubmission
        header("Location: " . $_SERVER['PHP_SELF']);
        exit(); // Stop further script execution
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
    <title>Hosteler Panel - Feedback</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .main-content {
            flex-grow: 1; /* This will take the remaining space */
            padding: 20px; /* Add some padding */
            display: flex;
            flex-direction: column; /* Stack children vertically */
            align-items: center; /* Center items horizontally */
        }

        .feedback-card, .pfeedback-card {
            width: 100%; /* Full width for the card */
            max-width: 600px; /* Optional: Set a max width for the feedback card */
            background-color: #ffffff; /* White background for the card */
            border-radius: 8px; /* Rounded corners */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Subtle shadow */
            padding: 20px; /* Padding inside the card */
            margin-bottom: 20px; /* Space between cards */
        }

        .card-title {
            color: #007bff; /* Bootstrap primary color for the title */
            margin-bottom : 20px; /* Space below the title */
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
    </style>
</head>
<body>
    <div class="col-md-10 p-4">
        <div class="row">
            <div class="col-md-10 main-content">
                <!-- New Feedback Section -->
                <div class="feedback-card">
                    <h3 class="ab-0 h-font card-title">Feedback</h3>
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <form action="" method="POST" autocomplete="">
                                <div class="mb-3">
                                    <label for="feedback" class="form-label">Your Feedback</label>
                                    <textarea class="form-control" id="feedback" name="ftext" rows="4" placeholder="Enter your feedback here..." required></textarea>
                                </div>
                                <button type="submit" name="submit" class="btn btn-primary">Submit Feedback</button>
                                <button type="button" class="btn btn-secondary" onclick="document.getElementById('feedback').value='';">Cancel</button>
                            </form>
                        </div>
                    </div>
                </div>

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

            <!-- Previous Feedback Section -->
            <div class="pfeedback-card">
                <h3 class="ab-0 h-font card-title">Previous Feedback</h3>
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <form id="pfeedbackForm">
                            <div class="mb-3">
                                <label for="pfeedback" class="form-label">Your Feedback</label>
                                <textarea class="form-control" id="pfeedback" rows="4" placeholder="Enter your feedback here..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Delete Feedback</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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