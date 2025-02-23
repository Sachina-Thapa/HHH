<?php
session_start();
require('../../admin/inc/db.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

try {
    $booking_id = $_POST['booking_id'];

    // Start transaction
    $conn->begin_transaction();

    try {
        // Check if the booking exists and get its details
        $check_booking = $conn->prepare("
            SELECT b.check_out, b.bstatus, 
                   f.status AS fee_status,
                   v.voucher_status,
                   hs.payment_status
            FROM booking b
            LEFT JOIN fee f ON b.bid = f.bid
            LEFT JOIN visitorform v ON b.bid = v.bid
            LEFT JOIN hservice hs ON b.bid = hs.bid
            WHERE b.bid = ?
        ");
        
        if (!$check_booking || !$check_booking->bind_param("i", $booking_id) || !$check_booking->execute()) {
            throw new Exception("Failed to check booking");
        }
        
        $result = $check_booking->get_result();
        $booking = $result->fetch_assoc();
        
        if (!$booking) {
            throw new Exception("Booking not found");
        }

        // Validate fee status - do not allow checkout if fee is pending or canceled
        if ($booking['fee_status'] === 'pending' || $booking['fee_status'] === 'canceled') {
            throw new Exception("Checkout not allowed: Your fee payment is pending or canceled. Please complete the payment first.");
        }

        // Validate visitor form voucher status
        if ($booking['voucher_status'] === '0') {
            throw new Exception("Cannot checkout: Visitor form voucher is not approved");
        }

        // Validate hostel services payment status
        if ($booking['payment_status'] === 'pending' || $booking['payment_status'] === 'rejected') {
            throw new Exception("Cannot checkout: Hostel services payment is pending or rejected");
        }

        // Check if checkout date has passed
        $checkout_date = new DateTime($booking['check_out']);
        $today = new DateTime();
        
        if ($checkout_date > $today && $booking['bstatus'] !== 'completed') {
            // Only allow early checkout if explicitly confirmed
            if (!isset($_POST['confirm_early_checkout']) || $_POST['confirm_early_checkout'] !== 'yes') {
                throw new Exception("Cannot checkout before the scheduled checkout date. Please confirm early checkout.");
            }
        }

        // Update booking status
        $update_booking = $conn->prepare("
            UPDATE booking 
            SET bstatus = 'completed',
                check_out = CURRENT_DATE()
            WHERE bid = ?
        ");
        
        if (!$update_booking || !$update_booking->bind_param("i", $booking_id) || !$update_booking->execute()) {
            throw new Exception("Failed to update booking status");
        }

        // Commit transaction
        $conn->commit();

        echo json_encode([
            'status' => 'success',
            'message' => 'Checkout successful. Thank you for staying with us!',
            'redirect' => 'http://localhost/HHH/hosteler/hostelerdash.php'
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