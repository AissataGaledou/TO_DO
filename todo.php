<?php
session_start();

if (!isset($_SESSION['user_email'])) {
    header("Location: signup.html");
    exit();
}

$conn = new mysqli('localhost', 'root', 'AYE@@GLD', 'todo_db');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email = $_SESSION['user_email'];

$stmt = $conn->prepare("SELECT Verified FROM sign_in WHERE Email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($verified);
$stmt->fetch();
$stmt->close();
$conn->close();

if (!$verified) {
    header("Location: verify_prompt.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <title>To do application</title>
    <link rel="stylesheet" href="todo.css">
    <title>To Do</title>
</head>
<body>
   <div class="container">
        <div class="todo-header">
            <h2>ToDo List</h2>
            <span class="material-symbols-outlined" height="50px">
                note_alt
            </span>
        </div>   

        <div class="todo-body">
            <input type="text" id="todoText"  class="todo-input" placeholder="Add your items"/> 
            <button onclick="addTask()"> <span class="material-symbols-outlined" style="font-size: 50px;">add</span></button>
        </div>

        <h5 id="Alert"></h5>
        <ul id="list-items" class="list-items"></ul>
    </div>
    
    <script src="todo.js"></script>
</body>
</html> 