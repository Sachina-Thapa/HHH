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

error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json');

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$response = ['success' => false, 'message' => 'Email not provided'];

$data = json_decode(file_get_contents('php://input'), true);

if(isset($data['email'])) {
    try {
        $email = $data['email'];
        
        // Check in adminlogin table
        $admin_query = "SELECT * FROM adminlogin WHERE email = ?";
        $stmt = $conn->prepare($admin_query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows == 0) {
            // Check in hostelers table
            $hosteler_query = "SELECT * FROM hostelers WHERE email = ?";
            $stmt = $conn->prepare($hosteler_query);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if($result->num_rows == 0) {
                // Check in staff_data table
                $staff_query = "SELECT * FROM staff_data WHERE email = ?";
                $stmt = $conn->prepare($staff_query);
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();
            }
        }

        if($result->num_rows > 0) {
            $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $_SESSION['email_otp'] = $otp;
            $_SESSION['email_otp_time'] = time();
            $_SESSION['reset_email'] = $email;

            $mail = new PHPMailer(true);

            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'hhhherhomehostel@gmail.com';
            $mail->Password = 'aqca djbg uohh vxim';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('hhhherhomehostel@gmail.com', 'Her Home Hostel');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset OTP';
            $mail->Body = "Your OTP for password reset is: $otp";

            $mail->send();
            $response = ['success' => true, 'message' => 'OTP sent successfully'];
        } else {
            $response = ['success' => false, 'message' => 'Email not found in our system'];
        }
    } catch (Exception $e) {
        $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

echo json_encode($response);
exit();
