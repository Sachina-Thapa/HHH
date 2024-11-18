<?php
session_start(); // Start the session

// Include your database connection file
require('inc/db.php');
require('inc/hsidemenu.php'); // Assuming this includes your sidebar

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    echo "<div class='alert alert-danger'>Please log in first.</div>";
    exit(); // Stop further execution
}

$username = $_SESSION['username']; // Get the logged-in username

// Function to get user information by username
function getUserInfoByUsername($conn, $username) {
    $sql = "SELECT id, name, email, phone_number, picture_path, address, date_of_birth, username FROM hostelers WHERE username = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error); // Error handling
    }
    $stmt->bind_param("s", $username);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc(); // Fetch the associative array
}

// Fetch the user information
$userInfo = getUserInfoByUsername($conn, $username);

// Check if user information was found
if (!$userInfo) {
    echo "<div class='alert alert-danger'>User  not found.</div>";
    exit(); // Stop further execution
}

// Now you can access the user's information
$hosteler_id = $userInfo['id'];
$name = $userInfo['name'];
$email = $userInfo['email'];
$phone_number = $userInfo['phone_number'];
$address = $userInfo['address'];
$date_of_birth = $userInfo['date_of_birth'];
$picture_path = $userInfo['picture_path'];

// Fetch room types for the dropdown
$room_sql = "SELECT rid, rno, rtype, rprice FROM room";
$room_result = $conn->query($room_sql);
if (!$room_result) {
    die("Query failed: " . $conn->error); // Error handling
}

// Handle form submission
$message = '';
$bookingdate = date('Y-m-d'); // Set the booking date to today
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the posted data
    $room_id = $_POST['room_type'];
    $check_in = $_POST['checkin_date'];
    $check_out = $_POST['checkout_date'];

    // Insert the data into the booking table
    $sql = "INSERT INTO booking (id, rid, bookingdate, check_in, check_out) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error); // Error handling
    }
    $stmt->bind_param("iiiss", $hosteler_id, $room_id, $bookingdate, $check_in, $check_out);
    $stmt->execute();

    // Check if the insertion was successful
    if ($stmt->affected_rows > 0) {
        $message = "<div class='alert alert-success'>Your booking is pending. You will be notified soon.</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error submitting booking. Please try again.</div>";
    }
    // Redirect to the same page to prevent form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Form</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            min-height: 100vh; /* Minimum height of 100% of the viewport */
            margin: 0; /* Remove default margin */
            background-color: #f8f9fa; /* Light background color */
        }

        .content {
            flex: 1; /* Take the remaining space */
            padding: 20px; /* Padding inside the content area */
        }

        .alert {
            margin-top: 20px; /* Margin for alerts */
        }
    </style>
</head>
<body>
    <div class="content">
        <h1>Booking Form</h1>
        <?php if ($message) echo $message; ?>

        <!-- Display user information -->
        <p><strong>Name:</strong> <?= htmlspecialchars($name); ?></p>
        <p><strong >Email:</strong> <?= htmlspecialchars($email); ?></p>
        <p><strong>Phone Number:</strong> <?= htmlspecialchars($phone_number); ?></p>
        <p><strong>Address:</strong> <?= htmlspecialchars($address); ?></p>
        <p><strong>Date of Birth:</strong> <?= htmlspecialchars($date_of_birth); ?></p>
        <p><strong>Booking Date:</strong> <?= htmlspecialchars($bookingdate); ?></p> 

        <form method="POST" action="">
            <div class="mb-3">
                <label for="room_type" class="form-label">Select Room Type</label>
                <select name="room_type" id="room_type" class="form-select" required>
                    <?php while ($row = $room_result->fetch_assoc()): ?>
                        <option value="<?= $row['rid']; ?>"><?= htmlspecialchars($row['rtype'] . ' - ' . $row['rprice']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="checkin_date" class="form-label">Check-in Date</label>
                <input type="date" name="checkin_date" id="checkin_date" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="checkout_date" class="form-label">Check-out Date</label>
                <input type="date" name="checkout_date" id="checkout_date" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Book Now</button>
            <a href="dashboard.php" class="btn btn-secondary">Cancel</a> <!-- Cancel button -->
        </form>
    </div>
</body>
</html>