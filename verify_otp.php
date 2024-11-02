<?php
session_start();
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$submitted_otp = $data['otp'];

if(isset($_SESSION['email_otp']) && 
   $_SESSION['email_otp'] == $submitted_otp && 
   (time() - $_SESSION['email_otp_time']) < 300) { // 5 minutes validity
    
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
