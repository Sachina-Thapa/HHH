<?php
require('../inc/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Confirm Booking
    if (isset($_POST['confirm_booking'])) {
        $bid = intval($_POST['bid']);
        $hid = intval($_POST['hid']);

        // Update booking status
        $update_query = "UPDATE bookings SET status = 'confirmed' WHERE bid = ?";
        $stmt = $mysqli->prepare($update_query);
        $stmt->bind_param('i', $bid);
        $stmt->execute();

        // Insert notification
        $message = "Your booking has been confirmed!";
        $notif_query = "INSERT INTO notifications (hid, message) VALUES (?, ?)";
        $notif_stmt = $mysqli->prepare($notif_query);
        $notif_stmt->bind_param('is', $hid, $message);
        $notif_stmt->execute();

        echo ($stmt->affected_rows > 0) ? 1 : 0;
        exit;
    }

    // Cancel Booking
    if (isset($_POST['cancel_booking'])) {
        $bid = intval($_POST['bid']);
        $hid = intval($_POST['hid']);

        // Update booking status
        $update_query = "UPDATE bookings SET status = 'canceled' WHERE bid = ?";
        $stmt = $mysqli->prepare($update_query);
        $stmt->bind_param('i', $bid);
        $stmt->execute();

        // Insert notification
        $message = "Your booking has been canceled.";
        $notif_query = "INSERT INTO notifications (hid, message) VALUES (?, ?)";
        $notif_stmt = $mysqli->prepare($notif_query);
        $notif_stmt->bind_param('is', $hid, $message);
        $notif_stmt->execute();

        echo ($stmt->affected_rows > 0) ? 1 : 0;
        exit;
    }
}
?>
