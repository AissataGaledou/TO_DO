<?php 
session_start();

echo "Login successful!";
exit();
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
        <ul id="list-items" class="list-items"></ul></div>
    
    <script src="todo.js"></script>
</body>
</html> 