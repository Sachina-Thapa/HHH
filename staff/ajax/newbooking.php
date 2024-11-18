<?php
require('../inc/db.php');

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        // Handling Confirm Booking
        if ($action == 'confirm') {
            $bid = intval($_POST['bid']);
            $id = intval($_POST['id']);

            // Update booking status to 'confirmed'
            $update_query = "UPDATE booking SET bstatus = 'confirmed' WHERE bid = ?";
            if ($stmt = $mysqli->prepare($update_query)) {
                $stmt->bind_param('i', $bid);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    // Insert notification
                    $message = "Your booking has been confirmed!";
                    $notif_query = "INSERT INTO notifications (id, message) VALUES (?, ?)";
                    if ($notif_stmt = $mysqli->prepare($notif_query)) {
                        $notif_stmt->bind_param('is', $id, $message);
                        $notif_stmt->execute();
                        echo '1'; // Successfully confirmed
                    } else {
                        echo "Error inserting notification: " . $mysqli->error;
                    }
                } else {
                    echo "Failed to update booking status.";
                }
            } else {
                echo "Error preparing booking update query: " . $mysqli->error;
            }
        }

        // Handling Cancel Booking
        if ($action == 'cancel') {
            $bid = intval($_POST['bid']);
            $id = intval($_POST['id']);

            // Update booking status to 'canceled'
            $update_query = "UPDATE booking SET bstatus = 'canceled' WHERE bid = ?";
            if ($stmt = $mysqli->prepare($update_query)) {
                $stmt->bind_param('i', $bid);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    // Insert notification
                    $message = "Your booking has been canceled.";
                    $notif_query = "INSERT INTO notifications (id, message) VALUES (?, ?)";
                    if ($notif_stmt = $mysqli->prepare($notif_query)) {
                        $notif_stmt->bind_param('is', $id, $message);
                        $notif_stmt->execute();
                        echo '1'; // Successfully canceled
                    } else {
                        echo "Error inserting notification: " . $mysqli->error;
                    }
                } else {
                    echo "Failed to update booking status.";
                }
            } else {
                echo "Error preparing booking cancel query: " . $mysqli->error;
            }
        }
    } else {
        echo "No action parameter received.";
    }
}
?>
