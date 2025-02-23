<?php
session_start();
require('../../admin/inc/db.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

try {
    // Get input data
    $booking_id = $_POST['booking_id'];
    $extension_days = (int)$_POST['extension_days'];
    $current_checkout = $_POST['current_checkout'];
    $room_price = (float)$_POST['room_price'];

    // Validate inputs
    if ($extension_days <= 0 || $extension_days > 30) {
        throw new Exception('Invalid extension period. Must be between 1 and 30 days.');
    }

    // Calculate new checkout date
    $new_checkout = date('Y-m-d', strtotime($current_checkout . " + $extension_days days"));
    
    // Start transaction
    $conn->begin_transaction();

    try {
        // Get hosteler ID from the booking
        $get_hosteler = $conn->prepare("SELECT id, rno FROM booking WHERE bid = ?");
        if (!$get_hosteler || !$get_hosteler->bind_param("i", $booking_id) || !$get_hosteler->execute()) {
            throw new Exception("Failed to get booking details");
        }
        $result = $get_hosteler->get_result();
        $booking_data = $result->fetch_assoc();
        
        if (!$booking_data) {
            throw new Exception("Booking not found");
        }

        // Update the booking
        $update_booking = $conn->prepare("
            UPDATE booking 
            SET check_out = ?,
                number_of_days = number_of_days + ?,
                bstatus = 'confirmed'
            WHERE bid = ?
        ");
        
        if (!$update_booking || 
            !$update_booking->bind_param("sii", $new_checkout, $extension_days, $booking_id) ||
            !$update_booking->execute()) {
            throw new Exception("Failed to update booking");
        }

        // Calculate total amount
        $total_amount = $room_price * $extension_days;

        // Create fee record
        $insert_fee = $conn->prepare("
            INSERT INTO fee (hid, rid, total, status) 
            VALUES (?, ?, ?, 'pending')
        ");
        
        if (!$insert_fee || 
            !$insert_fee->bind_param("iid", $booking_data['id'], $booking_data['rno'], $total_amount) ||
            !$insert_fee->execute()) {
            throw new Exception("Failed to create fee record");
        }

        // Commit the transaction
        $conn->commit();

        echo json_encode([
            'status' => 'success',
            'message' => 'Stay extended successfully. Please proceed to make the payment.',
            'new_checkout' => $new_checkout,
            'additional_fee' => $total_amount
        ]);

    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>