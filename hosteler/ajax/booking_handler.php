<?php
// File: HHH/hosteler/ajax/booking_handler.php
session_start();
require('../../admin/inc/db.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

// Get the action type
$action = $_POST['action'] ?? '';

try {
    // Start transaction
    $conn->begin_transaction();

    switch ($action) {
        case 'extend':
            handleExtension($conn);
            break;
            
        case 'checkout':
            handleCheckout($conn);
            break;
            
        default:
            throw new Exception('Invalid action specified');
    }

    $conn->commit();
    
} catch (Exception $e) {
    if (isset($conn)) {
        $conn->rollback();
    }
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

/**
 * Handle room extension
 */
function handleExtension($conn) {
    $booking_id = $_POST['booking_id'];
    $extension_days = (int)$_POST['extension_days'];
    $current_checkout = $_POST['current_checkout'];
    $room_price = (float)$_POST['room_price'];

    // Validate inputs
    if ($extension_days <= 0 || $extension_days > 30) {
        throw new Exception('Extension period must be between 1 and 30 days.');
    }

    // Get current booking details
    $get_booking = $conn->prepare("
        SELECT b.id, b.rno, b.check_in, b.check_out, b.number_of_days,
               r.rprice, r.rid
        FROM booking b
        JOIN room r ON b.rno = r.rno
        WHERE b.bid = ?
    ");
    
    if (!$get_booking->bind_param("i", $booking_id) || !$get_booking->execute()) {
        throw new Exception("Failed to get booking details");
    }
    
    $booking_result = $get_booking->get_result();
    $booking = $booking_result->fetch_assoc();
    
    if (!$booking) {
        throw new Exception("Booking not found");
    }

    // Calculate new checkout date
    $new_checkout = date('Y-m-d', strtotime($current_checkout . " + $extension_days days"));
    
    // Update existing booking
    $update_booking = $conn->prepare("
        UPDATE booking 
        SET check_out = ?,
            number_of_days = number_of_days + ?,
            bstatus = 'confirmed'
        WHERE bid = ?
    ");
    
    if (!$update_booking->bind_param("sii", $new_checkout, $extension_days, $booking_id) || 
        !$update_booking->execute()) {
        throw new Exception("Failed to update booking");
    }

    // Calculate extension fee
    $extension_fee = $room_price * $extension_days;

    // Create new fee record for extension
    $insert_fee = $conn->prepare("
        INSERT INTO fee (hid, rid, total, status, voucher) 
        VALUES (?, ?, ?, 'pending', NULL)
    ");
    
    if (!$insert_fee->bind_param("iid", $booking['id'], $booking['rid'], $extension_fee) || 
        !$insert_fee->execute()) {
        throw new Exception("Failed to create fee record");
    }

    // Copy existing visitor records with new dates
    $copy_visitors = $conn->prepare("
        INSERT INTO visitorform (vname, relation, reason, days, fee, hid, rid, status)
        SELECT vname, relation, reason, days, fee, hid, rid, 'pending'
        FROM visitorform 
        WHERE hid = ? AND status = 'accepted'
    ");
    
    if (!$copy_visitors->bind_param("i", $booking['id']) || !$copy_visitors->execute()) {
        throw new Exception("Failed to copy visitor records");
    }

    // Copy existing service records
    $copy_services = $conn->prepare("
        INSERT INTO hservice (seid, name, price, hid, status, total)
        SELECT seid, name, price, hid, 'pending', total
        FROM hservice 
        WHERE hid = ? AND status = 'accepted'
    ");
    
    if (!$copy_services->bind_param("i", $booking['id']) || !$copy_services->execute()) {
        throw new Exception("Failed to copy service records");
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Stay extended successfully. Please complete the payment.',
        'new_checkout' => $new_checkout,
        'additional_fee' => $extension_fee
    ]);
}

/**
 * Handle checkout process
 */
function handleCheckout($conn) {
    $booking_id = $_POST['booking_id'];

    // Get current booking details
    $get_booking = $conn->prepare("
        SELECT b.id, b.rno, b.check_out, b.bstatus
        FROM booking b
        WHERE b.bid = ?
    ");
    
    if (!$get_booking->bind_param("i", $booking_id) || !$get_booking->execute()) {
        throw new Exception("Failed to get booking details");
    }
    
    $booking = $get_booking->get_result()->fetch_assoc();
    
    if (!$booking) {
        throw new Exception("Booking not found");
    }

    // Mark current booking as completed
    $update_booking = $conn->prepare("
        UPDATE booking 
        SET bstatus = 'completed'
        WHERE bid = ?
    ");
    
    if (!$update_booking->bind_param("i", $booking_id) || !$update_booking->execute()) {
        throw new Exception("Failed to update booking status");
    }

    // Mark current fee records as completed
    $update_fees = $conn->prepare("
        UPDATE fee 
        SET status = 'completed'
        WHERE hid = ? AND status = 'confirmed'
    ");
    
    if (!$update_fees->bind_param("i", $booking['id']) || !$update_fees->execute()) {
        throw new Exception("Failed to update fee records");
    }

    // Mark visitor records as completed
    $update_visitors = $conn->prepare("
        UPDATE visitorform 
        SET status = 'completed'
        WHERE hid = ? AND status = 'accepted'
    ");
    
    if (!$update_visitors->bind_param("i", $booking['id']) || !$update_visitors->execute()) {
        throw new Exception("Failed to update visitor records");
    }

    // Mark service records as completed
    $update_services = $conn->prepare("
        UPDATE hservice 
        SET status = 'completed'
        WHERE hid = ? AND status = 'accepted'
    ");
    
    if (!$update_services->bind_param("i", $booking['id']) || !$update_services->execute()) {
        throw new Exception("Failed to update service records");
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Checkout successful. Thank you for staying with us!',
        'redirect' => 'hostelerdash.php'
    ]);
}
?>