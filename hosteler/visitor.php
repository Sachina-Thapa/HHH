<?php
session_start();
require('../admin/inc/db.php');
require('inc/hsidemenu.php');

// Initialize message and input variables
$success_message = '';
$error_message = '';
$vname = '';
$relation = '';
$reason = '';
$days = 1; // Default value for days

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

// Insert visitor data in visitorform table if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    // Get the values from the form
    $vname = htmlspecialchars($_POST['name']);
    $relation = htmlspecialchars($_POST['relation']);
    $reason = htmlspecialchars($_POST['reason']);
    $days = (int)$_POST['days']; // Ensuring days is an integer

    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO visitorform (vname, relation, reason, days, hid) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $vname, $relation, $reason, $days, $hid);

    // Execute the statement and check for success
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Visitor's Request is Pending.";
        // Clear the form inputs after submission
        $vname = '';
        $relation = '';
        $reason = '';
        $days = 1; // Reset to default value
    } else {
        $_SESSION['error_message'] = "Error: " . $stmt->error;
    }
    $stmt->close();
    
    // After processing the form, redirect to prevent form resubmission on page refresh
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch previously submitted visitor forms
$stmt = $conn->prepare("SELECT * FROM visitorform WHERE hid = ?");
$stmt->bind_param("i", $hid);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $visitorform[] = $row;
    }
}
$stmt->close();
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
            box-sizing: border-box;
            max-height: 80vh; /* Set a maximum height for the card */
            overflow-y: auto;
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
                <select class="form-control" id="reason" name="reason" required onchange="toggleDaysInput()">
                    <option value="" disabled selected>Select reason</option>
                    <option value="stay">Stay</option>
                    <option value="visit">Visit</option>
                </select>
            </div>
                <div class="mb-3" id="daysContainer" style="display: none;">
                    <label for="days" class="form-label">No of Days of Visit</label>
                    <div class="input-group">
                        <button type="button" class="btn btn-outline-secondary" onclick="decrementDays()">-</button>
                        <input type="number" class="form-control" id="days" name="days" min="1" value="<?php echo htmlspecialchars($days); ?>" required>
                        <button type="button" class="btn btn-outline-secondary" onclick="incrementDays()">+</button>
                    </div>
                    <div id="warningMessage" class="alert alert-warning mt-2" style="display: none;">
                        Visitor's Expense will be included as per the number of staying days.
                    </div>
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

    <div class="signup-card">

    <!-- Table for previously submitted visitor forms -->
    <div class="container mt-4">
        <h3 class="card-title">Visitors Form </h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Relationship</th>
                    <th>Reason</th>
                    <th>No of Days</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($visitorform)): ?>
                    <?php foreach ($visitorform as $form): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($form['vname']); ?></td>
                            <td><?php echo htmlspecialchars($form['relation']); ?></td>
                            <td><?php echo htmlspecialchars($form['reason']); ?></td>
                            <td><?php echo $form['reason'] === 'stay' ? htmlspecialchars($form['days']) : ''; ?></td>
                            <td><?php echo htmlspecialchars($form['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">No visitor forms submitted yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
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

        function toggleDaysInput() {
        const reason = document.getElementById('reason').value;
        const daysContainer = document.getElementById('daysContainer');
        const warningMessage = document.getElementById('warningMessage');
        
        if (reason === 'stay') {
            daysContainer.style.display = 'block';
            warningMessage.style.display = 'block'; // Show warning message
            
            // Hide the warning message after 5 seconds
            setTimeout(() => {
                warningMessage.style.display = 'none';
            }, 5000);
        } else {
            daysContainer.style.display = 'none';
            warningMessage.style.display = 'none'; // Hide warning message
        }
    }

    function incrementDays() {
        const daysInput = document.getElementById('days');
        daysInput.value = parseInt(daysInput.value) + 1;
    }

    function decrementDays() {
        const daysInput = document.getElementById('days');
        if (daysInput.value > 1) {
            daysInput.value = parseInt(daysInput.value) - 1;
        }
    }
    </script>
</body>
</html>