<?php
session_start();
require 'vendor/autoload.php'; // PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json'); // Add this line to return JSON

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connect to the database
$conn = new mysqli('localhost', 'root', 'AYE@@GLD', 'todo_db');

// Check connection
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $otp = rand(100000, 999999);
    $_SESSION['otp'] = $otp;
    $_SESSION['user_email'] = $email; // Changed from email_temp for consistency
    
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
            $mail->Password = 'wsbc qyno ymes qnke'; // Use your app password here
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            
            $mail->SMTPDebug = 0;  // Set to 0 for production
            
            $mail->setFrom('aissatagaledou@gmail.com', 'Todo App');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'OTP Verification';
            $mail->Body = 'Your OTP is: ' . $otp;
            $mail->send();
            
            echo json_encode(['success' => true, 'message' => 'OTP sent to ' . $email]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Failed to send OTP: ' . $mail->ErrorInfo]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to store OTP in database.']);
    }
    
    $stmt->close();
}

$conn->close();
?>