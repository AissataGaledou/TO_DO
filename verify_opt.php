<?php
session_start();
header('Content-Type: application/json');

// Connect to database
$conn = new mysqli('localhost', 'root', 'AYE@@GLD', 'todo_db');

// Check connection
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get email from POST instead of session
    $email = $_POST['email'];
    $otp = $_POST['otp'];
    
    if (empty($email)) {
        echo json_encode(['success' => false, 'message' => 'No email provided for OTP verification.']);
        exit();
    }
    
    // Check if the OTP matches the one stored in the database
    $stmt = $conn->prepare("SELECT * FROM email_verification WHERE email = ? AND otp = ?");
    $stmt->bind_param("ss", $email, $otp);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // OTP is valid, update Verified status in the sign_in table
        $update = $conn->prepare("UPDATE sign_in SET Verified = 1 WHERE Email = ?");
        $update->bind_param("s", $email);
        
        if ($update->execute()) {
            // Set session variable for email verification status
            $_SESSION['email_verified'] = true;
            $_SESSION['user_email'] = $email;
            
            echo json_encode(['success' => true, 'message' => 'Email verified successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error updating verification status.']);
        }
        $update->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid OTP.']);
    }
    
    $stmt->close();
}

$conn->close();
?>