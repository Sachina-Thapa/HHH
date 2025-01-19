<?php
require('inc/db.php');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle accept or decline action
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $id = $_POST['id'];
    $action = $_POST['action'];

    if ($action == 'accept') {
        $stmt = $mysqli->prepare("UPDATE hservice SET status = 'Accepted' WHERE id = ?");
    } else if ($action == 'decline') {
        $stmt = $mysqli->prepare("UPDATE hservice SET status = 'Declined' WHERE id = ?");
    }

    if ($stmt) {
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            // Return a JSON response
            header('Content-Type: application/json'); // Set content type to JSON
            echo json_encode(['success' => true, 'action' => $action]); // Return success
        } else {
            header('Content-Type: application/json'); // Set content type to JSON
            echo json_encode(['success' => false, 'message' => $stmt->error]); // Return error
        }
    } else {
        header('Content-Type: application/json'); // Set content type to JSON
        echo json_encode(['success' => false, 'message' => $mysqli->error]); // Return error
    }
    exit; // Prevent further output
}
?>