<?php
session_start();
require 'vendor/autoload.php'; // PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Connect to the database
$conn = new mysqli('localhost', 'root', 'AYE@@GLD', 'todo_db');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $otp = rand(100000, 999999);
    $_SESSION['otp'] = $otp;
    $_SESSION['email_temp'] = $email; // Temporarily store email for verification
    
    // 1. Store OTP in email_verification table
    $stmt = $conn->prepare("INSERT INTO email_verification (email, otp) VALUES (?, ?)");
    $stmt->bind_param("ss", $email, $otp);
    
    if ($stmt->execute()) {
        // 2. Send OTP email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'aissatagaledou@gmail.com';
            $mail->Password = 'wsbc qyno ymes qnke'; // You already know to replace this
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('aissatagaledou@gmail.com', 'Todo App');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'OTP Verification';
            $mail->Body = 'Your OTP is: ' . $otp;
            $mail->send();

            echo 'OTP sent to ' . $email;
        } catch (Exception $e) {
            echo 'Failed to send OTP: ', $mail->ErrorInfo;
        }
    } else {
        echo "Failed to store OTP in database.";
    }

    $stmt->close();
}

$conn->close();
?>
