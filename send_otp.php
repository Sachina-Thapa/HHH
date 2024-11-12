<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if(isset($_POST['email'])) {
    $email = $_POST['email'];
    $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    $_SESSION['email_otp'] = $otp;
    $_SESSION['email_otp_time'] = time();

    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'hhhherhomehostel@gmail.com';
        $mail->Password = 'aqca djbg uohh vxim'; // Gmail App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('hhhherhomehostel@gmail.com', 'Her Home Hostel');
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Email Verification OTP';
        $mail->Body = "Your OTP for email verification is: $otp";

        $mail->send();
        die(json_encode([
            'success' => true,
            'message' => 'OTP sent successfully'
        ]));
    } catch (Exception $e) {
        die(json_encode([
            'success' => false,
            'message' => 'Failed to send OTP: ' . $mail->ErrorInfo
        ]));
    }
}

die(json_encode([
    'success' => false,
    'message' => 'Email not provided'
]));
