<?php
require('../admin/inc/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bid = $_POST['bid'];
    $hosteler_id = $_POST['hosteler_id'];
    $action = $_POST['action'];

    // Determine the new status based on the action
    $new_status = ($action === 'confirm') ? 'confirmed' : 'canceled';

    // Prepare the SQL statement to update the status
    $sql = "UPDATE booking SET bstatus = ? WHERE bid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $new_status, $bid);

    // Execute the statement
    if ($stmt->execute()) {
        // Return a success response
        echo "Booking status updated successfully.";
    } else {
        // Handle error if needed
        echo "Error updating booking status: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}

$conn->close();
?>
