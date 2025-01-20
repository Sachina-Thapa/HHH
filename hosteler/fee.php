<?php
session_start(); // Start the session
require('inc/db.php'); // Database connection
require('inc/hsidemenu.php'); // Sidebar

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

// Fetch hosteler ID from the database
$stmt = $conn->prepare("SELECT id FROM hostelers WHERE username = ?");
if ($stmt === false) {
    // Output error message if query preparation fails
    die('Query preparation failed: ' . $conn->error);
}

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

// Fetch booking details for the logged-in hosteler
$stmt = $conn->prepare("SELECT check_in, check_out, rno, bstatus FROM booking WHERE id = ? AND bstatus = 'confirmed'");
if ($stmt === false) {
    die('Query preparation failed: ' . $conn->error);
}

$stmt->bind_param("i", $hid);
$stmt->execute();
$result = $stmt->get_result();

$totalPrice = 0; // Initialize total price
if ($result->num_rows === 0) {
    $bookingDetails = null; // No booking found
} else {
    $bookingDetails = $result->fetch_assoc();
    $room_no = $bookingDetails['rno'];
    
    // Fetch room price
    $stmt = $conn->prepare("SELECT rprice FROM room WHERE rno = ?");
    if ($stmt === false) {
        die('Query preparation failed: ' . $conn->error);
    }
    
    $stmt->bind_param("i", $room_no);
    $stmt->execute();
    $roomResult = $stmt->get_result();
    
    if ($roomResult->num_rows > 0) {
        $room = $roomResult->fetch_assoc();
        $roomPrice = $room['rprice'];
        
        // Calculate the number of days
        $check_in = new DateTime($bookingDetails['check_in']);
        $check_out = new DateTime($bookingDetails['check_out']);
        $interval = $check_in->diff($check_out);
        $days = $interval->days;

        // Calculate the total price (room price for 30 days, prorated for the days stayed)
        $totalPrice = ($roomPrice / 30) * $days;
    } else {
        $roomPrice = 0;
        $days = 0; // Set default value for days
    }
}

// Fetch total fees from visitorform for the logged-in hosteler
$stmt = $conn->prepare("SELECT SUM(fee) AS total_visitor_fee FROM visitorform WHERE hid = ?");
$stmt->bind_param("i", $hid);
$stmt->execute();
$visitorResult = $stmt->get_result();
$visitorRow = $visitorResult->fetch_assoc();
$totalVisitorFee = $visitorRow['total_visitor_fee'] ? $visitorRow['total_visitor_fee'] : 0;

// Calculate the grand total
$grandTotal = $totalPrice + $totalVisitorFee;

// Handle voucher upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['upload_voucher'])) {
    // Check if a file was uploaded
    if (isset($_FILES['voucher']) && $_FILES['voucher']['error'] == UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['voucher']['tmp_name'];
        $fileName = $_FILES['voucher']['name'];
        $fileSize = $_FILES['voucher']['size'];
        $fileType = $_FILES['voucher']['type'];

        // Specify the directory where the file will be uploaded
        $uploadFileDir = 'uploads/';
        $dest_path = $uploadFileDir . $fileName;

        // Move the file to the specified directory
        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            // Insert voucher information into the database
            $stmt = $conn->prepare("INSERT INTO vouchers (hosteler_id, file_name, upload_date) VALUES (?, ?, NOW())");
            $stmt->bind_param("is", $hid, $fileName);
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Voucher uploaded successfully.";
            } else {
                $_SESSION['error_message'] = "Error uploading voucher: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $_SESSION['error_message'] = "Error moving the uploaded file.";
        }
    } else {
        $_SESSION['error_message'] = "No file uploaded or there was an upload error.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Ensure the main content is aligned properly */
        body {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 210px; /* Sidebar width */
            background-color: #f8f9fa;
            padding-top: 20px;
        }
        .main-content {
            margin-left: 210px; /* Adjust to the sidebar width */
            padding: 20px;
            flex-grow: 1;
            background-color: #f1f1f1;
        }
        .card-footer {
            background-color: #dfe6e9;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <?php include('inc/hsidemenu.php'); ?>
</div>

<!-- Main Content -->
<div class="main-content">
    <!-- Total Price Display -->
    <div class="card-footer gradient-bg text-dark d-flex justify-content-between align-items-center p-3">
        <div class="price-label">
            Total Price: $<span id="totalPrice"><?php echo number_format($grandTotal, 2); ?></span>
        </div>
        <?php if ($bookingDetails): ?>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#detailsModal" 
                    data-bs-whatever="@mdo">View Details</button>
        <?php endif; ?>
    </div>

    <!-- Booking Details Modal -->
    <div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailsModalLabel">Booking Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if ($bookingDetails): ?>
                        <p>Check-in Date: <?php echo $bookingDetails['check_in']; ?></p>
                        <p>Check-out Date: <?php echo $bookingDetails['check_out']; ?></p>
                        <p>Total Staying Days: <?php echo $days; ?> days</p> <!-- Display staying days -->
                        <p>Room ID: <?php echo $bookingDetails['rno']; ?></p>
                        <p>Room Price (30 days): $<?php echo number_format($roomPrice, 2); ?></p>
                        <p>Total Price for Stay: $<?php echo number_format($totalPrice, 2); ?></p>
                        <p>Total Visitor Fees: $<?php echo number_format($totalVisitorFee, 2); ?></p> <!-- Display visitor fees -->
                    <?php else: ?>
                        <p>No booking found.</p>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Voucher Section -->
    <div class="container mt-4">
        <h3>Upload Voucher</h3>
        <?php if (!empty($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($_SESSION['error_message'])): ?>
            <div class="alert alert-danger">
                <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="voucher" class="form-label">Select Voucher File</label>
                <input type="file" class="form-control" name="voucher" id="voucher" required>
            </div>
            <button type="submit" name="upload_voucher" class="btn btn-success">Upload Voucher</button>
        </form>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>