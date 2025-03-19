<?php
// tasks_handler.php
session_start();

// Set content type to JSON for all responses
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

// Connect to the database
$conn = new mysqli('localhost', 'root', 'AYE@@GLD', 'todo_db');

// Check connection
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

// Get user ID from session
$userId = $_SESSION['user_id'];

// Handle different actions
$action = isset($_POST['action']) ? $_POST['action'] : '';

switch ($action) {
    case 'add':
        // Add new task
        if (isset($_POST['task']) && !empty($_POST['task'])) {
            $task = htmlspecialchars(trim($_POST['task']));
            
            $stmt = $conn->prepare("INSERT INTO tasks (user_id, task, completed, created_at) VALUES (?, ?, 0, NOW())");
            $stmt->bind_param("is", $userId, $task);
            
            if ($stmt->execute()) {
                $taskId = $stmt->insert_id;
                echo json_encode(['success' => true, 'task_id' => $taskId]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to add task: ' . $stmt->error]);
            }
            
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'No task provided']);
        }
        break;
        
    case 'get':
        // Get all tasks for the user
        $stmt = $conn->prepare("SELECT id, task, completed FROM tasks WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $tasks = [];
        while ($row = $result->fetch_assoc()) {
            $tasks[] = $row;
        }
        
        echo json_encode($tasks);
        $stmt->close();
        break;
        
    case 'update':
        // Update task status
        if (isset($_POST['id']) && isset($_POST['completed'])) {
            $taskId = (int)$_POST['id'];
            $completed = (int)$_POST['completed'];
            
            $stmt = $conn->prepare("UPDATE tasks SET completed = ? WHERE id = ? AND user_id = ?");
            $stmt->bind_param("iii", $completed, $taskId, $userId);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update task: ' . $stmt->error]);
            }
            
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
        }
        break;
        
    case 'delete':
        // Delete task
        if (isset($_POST['id'])) {
            $taskId = (int)$_POST['id'];
            
            $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
            $stmt->bind_param("ii", $taskId, $userId);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete task: ' . $stmt->error]);
            }
            
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'No task ID provided']);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action: ' . $action]);
}

$conn->close();
?>