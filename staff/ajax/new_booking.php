<?php
require('../inc/db.php'); // Correct path to your DB connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get POST data
    $bid = $_POST['bid'];
    $hosteler_id = $_POST['hosteler_id'];
    $action = $_POST['action']; // Can be 'confirm' or 'cancel'

    // Determine the new status based on the action
    if ($action === 'confirm') {
        $new_status = 'confirmed';
    } elseif ($action === 'cancel') {
        $new_status = 'canceled';
    } else {
        echo "Invalid action!";
        exit();
    }

    // Prepare the SQL statement to update the status
    $sql = "UPDATE booking SET bstatus = ? WHERE bid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $new_status, $bid);

    // Execute the statement
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Booking status updated successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error updating booking status.']);
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
if (isset($conn)) {
    $conn->close();
}
?>
