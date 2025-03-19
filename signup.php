<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); // Enable Error Reporting in PHP
session_start();

// Connect to the database
$conn = new mysqli('localhost', 'root', 'AYE@@GLD', 'todo_db');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Form submission handling
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Fname = htmlspecialchars(trim($_POST['Fname']));
    $Lname = htmlspecialchars(trim($_POST['Lname']));
    $mail = htmlspecialchars(trim($_POST['mail']));
    $password = htmlspecialchars(trim($_POST['pwd']));

    if (empty($Fname) || empty($Lname) || empty($mail) || empty($password)) {
        echo "All fields are required!";
        exit();
    }

    // Check if the email already exists
    $checkEmail = $conn->prepare("SELECT id FROM sign_in WHERE Email = ?");
    $checkEmail->bind_param("s", $mail);
    $checkEmail->execute();
    $checkEmail->store_result();

    if ($checkEmail->num_rows > 0) {
        echo "Email is already registered!";
        exit();
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user into the 'sign_in' table
    $stmt = $conn->prepare("INSERT INTO sign_in (Fname, Lname, Email, Password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $Fname, $Lname, $mail, $hashedPassword);

    if ($stmt->execute()) {
        // Set session variables
        $_SESSION['user_id'] = $stmt->insert_id; // Auto-generated ID
        $_SESSION['user_name'] = $Fname . ' ' . $Lname;
        $_SESSION['user_email'] = $mail;

        // Redirect to dashboard or todo page
        header("Location: todo.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close statements
    $stmt->close();
    $checkEmail->close();
}


// Close connection
$conn->close();
?>
