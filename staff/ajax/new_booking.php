<?php
header('Content-Type: application/json');

require('../../admin/inc/db.php'); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate input data
    if (!isset($_POST['bid']) || !isset($_POST['hosteler_id']) || !isset($_POST['action'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Missing required parameters'
        ]);
        exit;
    }

    // Sanitize inputs
    $bid = intval($_POST['bid']);
    $hosteler_id = intval($_POST['hosteler_id']);
    $action = $_POST['action'];

    // Validate action
    if (!in_array($action, ['confirm', 'cancel'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid action specified'
        ]);
        exit;
    }

    // Set the new status based on action
    $new_status = ($action === 'confirm') ? 'confirmed' : 'canceled';

    try {
        // Prepare and execute the update
        $sql = "UPDATE booking SET bstatus = ? WHERE bid = ? AND id = ?";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("sii", $new_status, $bid, $hosteler_id);
        
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        if ($stmt->affected_rows > 0) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Booking status updated successfully to ' . $new_status
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'No booking found with the provided ID'
            ]);
        }

        $stmt->close();
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }

    // Close the connection
    $conn->close();
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method'
    ]);
}