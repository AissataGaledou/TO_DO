<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli('localhost', 'root', 'AYE@@GLD', 'todo_db');
$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT Verified FROM sign_in WHERE id = $user_id");
$row = $result->fetch_assoc();
$verified = $row['Verified'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Welcome</title>
</head>
<body>
    <h1>Welcome, <?php echo $_SESSION['user_name']; ?>!</h1>

    <?php if (!$verified): ?>
        <div style="color: red;">
            Your email is not verified. <a href="verify_prompt.php">Click here to verify.</a>
        </div>
    <?php else: ?>
        <p>Your email is verified ✅</p>
    <?php endif; ?>
</body>
</html>
