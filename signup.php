<?php
header("Location: verify_prompt.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

$conn = new mysqli('localhost', 'root', 'AYE@@GLD', 'todo_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Fname = htmlspecialchars(trim($_POST['Fname']));
    $Lname = htmlspecialchars(trim($_POST['Lname']));
    $mail = htmlspecialchars(trim($_POST['mail']));
    $password = htmlspecialchars(trim($_POST['pwd']));

    if (empty($Fname) || empty($Lname) || empty($mail) || empty($password)) {
        echo "All fields are required!";
        exit();
    }

    // Check for existing email
    $checkEmail = $conn->prepare("SELECT id FROM sign_in WHERE Email = ?");
    $checkEmail->bind_param("s", $mail);
    $checkEmail->execute();
    $checkEmail->store_result();
    if ($checkEmail->num_rows > 0) {
        echo "Email is already registered!";
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert user with Verified = 0
    $stmt = $conn->prepare("INSERT INTO sign_in (Fname, Lname, Email, Password, Verified) VALUES (?, ?, ?, ?, 0)");
    $stmt->bind_param("ssss", $Fname, $Lname, $mail, $hashedPassword);

    if ($stmt->execute()) {
        $_SESSION['user_id'] = $stmt->insert_id;
        $_SESSION['user_name'] = $Fname . ' ' . $Lname;
        $_SESSION['user_email'] = $mail;

        header("Location: welcome.php"); // or dashboard
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $checkEmail->close();
}

$conn->close();
?>
