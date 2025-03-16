<?php
require('../inc/db.php');
session_start(); // Start session for error messages

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve input values
    $room_number = $_POST['room_number'];
    $room_price = $_POST['room_price'];

    // ✅ Validate Room Number (Only Digits)
    if (!ctype_digit($room_number)) {
        $_SESSION['error_message'] = "Room number must be numeric only.";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // ✅ Validate Room Price (Must be a 5-digit number)
    if (!preg_match("/^\d{1,5}$/", $room_price)) {
        $_SESSION['error_message'] = "Room price must be a number between 1 and 99,999 with no symbols or characters.";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // ✅ Check if Room Number Already Exists
    $check_sql = "SELECT * FROM room WHERE rno = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $room_number);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $_SESSION['error_message'] = "This room already exists.";
    } else {
        $_SESSION['success_message'] = "Room number is available.";
    }

    $check_stmt->close();
}
