<?php
session_start();

// Database connection
$conn = new mysqli('localhost', 'root', 'AYE@@GLD', 'todo_db');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $otp = $_POST['otp'];

    // 1. Check if OTP matches
    $stmt = $conn->prepare("SELECT * FROM email_verification WHERE email = ? AND otp = ?");
    $stmt->bind_param("ss", $email, $otp);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // 2. OTP is valid, update Verified to 1
        $update = $conn->prepare("UPDATE sign_in SET Verified = 1 WHERE Email = ?");
        $update->bind_param("s", $email);
        if ($update->execute()) {
            echo json_encode(['success' => true, 'message' => 'Email verified successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update verification status.']);
        }
        $update->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid OTP']);
    }

    $stmt->close();
}

$conn->close();
?>
