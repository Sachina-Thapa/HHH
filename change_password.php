<?php
session_start();
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'hhh';
$errorMessage = '';

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'];
$password = $data['password'];
$response = ['success' => false];

// Update password in adminlogin table
$admin_update = "UPDATE adminlogin SET password = ? WHERE email = ?";
$stmt = $conn->prepare($admin_update);
$stmt->bind_param("ss", $password, $email);
$stmt->execute();

if($stmt->affected_rows == 0) {
    // Try updating hostelers table
    $hosteler_update = "UPDATE hostelers SET password = ? WHERE email = ?";
    $stmt = $conn->prepare($hosteler_update);
    $stmt->bind_param("ss", $password, $email);
    $stmt->execute();
    
    if($stmt->affected_rows == 0) {
        // Try updating staff_data table
        $staff_update = "UPDATE staff_data SET password = ? WHERE email = ?";
        $stmt = $conn->prepare($staff_update);
        $stmt->bind_param("ss", $password, $email);
        $stmt->execute();
    }
}

if($stmt->affected_rows > 0) {
    $response['success'] = true;
    $response['message'] = 'Password updated successfully';
} else {
    $response['message'] = 'Failed to update password';
}

echo json_encode($response);
