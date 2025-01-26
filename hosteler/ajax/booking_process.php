<?php
session_start(); // Start the session

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include your database connection file
require('../inc/db.php');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "error: Invalid request method";
    exit(); // Stop further execution
}

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    echo "error: User not logged in";
    exit(); // Stop further execution
}

$username = $_SESSION['username']; // Get the logged-in username

// Fetch the user information
$sql = "SELECT id FROM hostelers WHERE username = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("s", $username);
$stmt->execute();
$userInfo = $stmt->get_result()->fetch_assoc();

if (!$userInfo) {
    echo "error: User not found";
    exit(); // Stop further execution
}

$hosteler_id = $userInfo['id'];
$room_no = $_POST['room_no'] ?? null; // This should be the room number
$st_id = $_POST['st_id'] ?? null; // Assuming you have this in your form
$check_in = $_POST['check_in'] ?? null; // Assuming you have this in your form
$check_out = $_POST['check_out'] ?? null; // Assuming you have this in your form
$arrival = $_POST['arrival'] ?? null; // Assuming you have this in your form
$number_of_days = $_POST['number_of_days'] ?? null; // Assuming you have this in your form

// Check if required fields are set
if (!$room_no) {
    echo "error: Missing required fields";
    exit(); // Stop further execution
}

// Check if the room exists
$room_check_sql = "SELECT rno FROM room WHERE rno = ?"; // Ensure using rno
$room_check_stmt = $conn->prepare($room_check_sql);
if (!$room_check_stmt) {
    die("Prepare failed: " . $conn->error);
}
$room_check_stmt->bind_param("i", $room_no);
$room_check_stmt->execute();
$room_check_result = $room_check_stmt->get_result();

if ($room_check_result->num_rows === 0) {
    echo "error: Room does not exist";
    exit(); // Stop further execution
}

// Insert the booking data into the booking table
$booking_sql = "INSERT INTO `booking` (`id`, `rno`, `bookingdate`, `bstatus`) VALUES (?, ?, NOW(), 'pending')";
$stmt = $conn->prepare($booking_sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
    // Responding with success or error
    $response = [
        'status' => 'success', // You can dynamically set this to 'error' based on conditions
        'message' => 'Your booking has been successfully placed. You selected ' . $roomType . ' for ' . $roomNo . '.'
    ];

    // Output JSON response
    echo json_encode($response);
} else {
    // Handle GET or other methods if necessary
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method.'
    ]);
}



$stmt->bind_param("ii", $hosteler_id, $room_no); // Ensure using rno
if ($stmt->execute()) {
    // Get the last inserted booking ID
    $booking_id = $stmt->insert_id;
}
// If booking status is confirmed, allow the user to input additional fields
if ($bstatus === 'confirmed') {
    $check_in = $_POST['check_in'] ?? null;
    $check_out = $_POST['check_out'] ?? null;
    $arrival = $_POST['arrival'] ?? null;
    $number_of_days = $_POST['number_of_days'] ?? null;

    // Perform validation if needed
    if (!$check_in || !$check_out || !$arrival || !$number_of_days) {
        echo json_encode(['status' => 'error', 'message' => 'Please fill in all the required fields']);
        exit(); // Stop further execution
    }

    // Update the booking with the additional fields
    $update_sql = "UPDATE `booking` SET `st_id` = ?,  `check_in` = ?, `check_out` = ?, `arrival` = ?, `number_of_days` = ? WHERE `id` = ?";
    $update_stmt = $conn->prepare($update_sql);
    if (!$update_stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $update_stmt->bind_param("issssi", $st_id,$check_in, $check_out, $arrival, $number_of_days, $booking_id);
    if ($update_stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Your booking is confirmed.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Update failed']);
    }
}
// Close the connection
$conn->close();
?>
