<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require('../inc/db.php');

// Check request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit();
}

// Check login status
if (!isset($_SESSION['username'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit();
}

// Get user info
$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT id FROM hostelers WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$userInfo = $stmt->get_result()->fetch_assoc();

if (!$userInfo) {
    echo json_encode(['status' => 'error', 'message' => 'User not found']);
    exit();
}

// Get form data
$hosteler_id = $userInfo['id'];
$room_no = $_POST['rno'] ?? null;
$arrival = (int)($_POST['arrival'] ?? 0);

// Debug log
error_log("Received POST data: " . print_r($_POST, true));

// Validate fields
if (!$room_no) {
    echo json_encode(['status' => 'error', 'message' => 'Room number is required']);
    exit();
}

if (!$arrival) {
    echo json_encode(['status' => 'error', 'message' => 'Arrival time is required']);
    exit();
}

// Validate arrival time value
if (!in_array($arrival, [1, 2, 3, 4])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid arrival time']);
    exit();
}

// Check room availability
$room_check_sql = "SELECT rno FROM room WHERE rno = ? AND rno NOT IN (
    SELECT rno FROM booking 
    WHERE bstatus = 'pending')";

$stmt = $conn->prepare($room_check_sql);
$stmt->bind_param("i", $room_no);
$stmt->execute();
if ($stmt->get_result()->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Room is not available']);
    exit();
}

// Begin transaction
$conn->begin_transaction();

try {

// Set current date for bookingdate
$current_date = date('Y-m-d');

// Insert booking with NULL for check_in and check_out
$booking_sql = "INSERT INTO booking (id, rno, bookingdate, check_in, check_out, arrival, bstatus, number_of_days) 
                VALUES (?, ?, NOW(), NULL, NULL, ?, 'pending', 1)";
$stmt = $conn->prepare($booking_sql);
$stmt->bind_param("iis", $hosteler_id, $room_no, $arrival);

if (!$stmt->execute()) {
    throw new Exception($stmt->error);
}

// Commit transaction
$conn->commit();
echo json_encode(['status' => 'success', 'message' => 'Booking placed successfully']);
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    error_log("Booking error: " . $e->getMessage()); // Add error logging
    echo json_encode(['status' => 'error', 'message' => 'Booking failed: ' . $e->getMessage()]);
}

$conn->close();
?>