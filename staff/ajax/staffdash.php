<?php
// Include database connection
include __DIR__ . '/inc/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        // Handling Confirm Booking
        if ($action == 'confirm') {
            $id = intval($_POST['id']);
            $seid = intval($_POST['seid']);

            error_log("Confirming booking with id: $id and seid: $seid");

            $update_query = "UPDATE hservice SET status = 'confirmed' WHERE id = ?";
            if ($stmt = $mysqli->prepare($update_query)) {
                $stmt->bind_param('i', $id);
                if ($stmt->execute()) {
                    if ($stmt->affected_rows > 0) {
                        $message = "Your booking has been confirmed!";
                        $notif_query = "INSERT INTO notifications (id, message) VALUES (?, ?)";
                        if ($notif_stmt = $mysqli->prepare($notif_query)) {
                            $notif_stmt->bind_param('is', $seid, $message);
                            if ($notif_stmt->execute()) {
                                echo '1'; // Successfully confirmed
                            } else {
                                echo "Error inserting notification: " . $mysqli->error;
                            }
                        } else {
                            echo "Error preparing notification insert query: " . $mysqli->error;
                        }
                    } else {
                        echo "No rows affected. Booking may not exist.";
                    }
                } else {
                    echo "Error executing booking update query: " . $stmt->error;
                }
            } else {
                echo "Error preparing booking update query: " . $mysqli->error;
            }
        }

        // Handling Cancel Booking
        if ($action == 'cancel') {
            $id = intval($_POST['id']);
            $seid = intval($_POST['seid']);

            error_log("Canceling booking with id: $id and seid: $seid");

            $update_query = "UPDATE hservice SET status = 'canceled' WHERE id = ?";
            if ($stmt = $mysqli->prepare($update_query)) {
                $stmt->bind_param('i', $id);
                if ($stmt->execute()) {
                    if ($stmt->affected_rows > 0) {
                        $message = "Your booking has been canceled.";
                        $notif_query = "INSERT INTO notifications (id, message) VALUES (?, ?)";
                        if ($notif_stmt = $mysqli->prepare($notif_query)) {
                            $notif_stmt->bind_param('is', $seid, $message);
                            if ($notif_stmt->execute()) {
                                echo '1'; // Successfully canceled
                            } else {
                                echo "Error inserting notification: " . $mysqli->error;
                            }
                        } else {
                            echo "Error preparing notification insert query: " . $mysqli->error;
                        }
                    } else {
                        echo "No rows affected. Booking may not exist.";
                    }
                } else {
                    echo "Error executing booking cancel query: " . $stmt->error;
                }
            } else {
                echo "Error preparing booking cancel query: " . $mysqli->error;
            }
        }
    } else {
        echo "No action parameter received.";
    }
} else {
    echo "Invalid request method.";
} 

?>