<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Connect to the database
$conn = new mysqli('localhost', 'root', 'AYE@@GLD', 'todo_db');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Form submission handling
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = htmlspecialchars(trim($_POST['mail']));
    $password = htmlspecialchars(trim($_POST['pwd']));

    if (empty($email) || empty($password)) {
        echo "Email and password are required!";
        exit();
    }

    //  Prepare a single query to fetch all needed data
    $stmt = $conn->prepare("SELECT id, Fname, Lname, Email, Password, Verified FROM sign_in WHERE Email = ?");
    
    if (!$stmt) {
        // Show error if query preparation fails
        die("Prepare failed: " . $conn->error);
    }

    // Bind email to the query
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        // Bind result variables
        $stmt->bind_result($id, $fname, $lname, $email_db, $hashedPassword, $verified);
        $stmt->fetch();

     
        if ($verified == 0) {
            echo "Please verify your email first!";
            exit();
        }

       
        if (password_verify($password, $hashedPassword)) {
            // Set session variables
            $_SESSION['user_id'] = $id;
            $_SESSION['user_name'] = $fname . ' ' . $lname;
            $_SESSION['user_email'] = $email_db;

            // Redirect to dashboard
            header("Location: todo.php");
            exit();
        } else {
            echo "Invalid email or password!";
        }
    } else {
        echo "Invalid email or password!";
    }

    $stmt->close();
}

$conn->close();
?>
