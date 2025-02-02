<?php

// Set the response content type to JSON
header('Content-Type: application/json');

// Include the authentication middleware for verifying the user's identity
require_once $_SERVER['DOCUMENT_ROOT'] . '/middlewares/authMiddleware.php';

// Get the authenticated user ID from the middleware
$userId = authMiddleware();

// Get the HTTP request method (GET, POST, PUT, DELETE)
$method = $_SERVER['REQUEST_METHOD'];

// Load configuration and database connection
$config = require_once dirname(__DIR__) . '/' . 'config/config.php';
$pdo = require_once dirname(__DIR__) . '/' . 'config/database.php';

// Get the current URI for routing
$request_uri = $_SERVER['REQUEST_URI'];
$request = explode("/", trim($request_uri, "/"));

// Extract task ID from the URL (if it's a valid task endpoint like /tasks/{id})
preg_match('/\/tasks\/(\d+)/', $request_uri, $matches);

$task_id = null; // Initialize task ID variable

// If a task ID is found in the URI, assign it to the variable
if (isset($matches[1])) {
    $task_id = $matches[1];
}

// Switch-case based on the HTTP method to handle different actions (GET, POST, PUT, DELETE)
switch ($method) {
    case 'GET':
        getTasks($userId); // Get tasks for the authenticated user
        break;
    case 'POST':
        createTask($userId); // Create a new task
        break;
    case 'PUT':
        updateTask($userId, $task_id); // Update an existing task
        break;
    case 'DELETE':
        deleteTask($userId, $task_id); // Delete a specific task
        break;
    default:
        // If an unsupported HTTP method is used, return 405 Method Not Allowed
        http_response_code(405);
        echo json_encode(['message' => 'Method Not Allowed']);
}

// Function to get all tasks for the authenticated user
function getTasks($userId) {
    global $pdo;
    // Prepare and execute SQL query to fetch tasks for the user
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $userId]);
    // Fetch all tasks as an associative array and return as JSON
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($tasks);
}

// Function to create a new task
function createTask($userId) {
    global $pdo;
    // Decode JSON input data
    $data = json_decode(file_get_contents("php://input"), true);
    // Prepare and execute SQL query to insert a new task for the user
    $stmt = $pdo->prepare("INSERT INTO tasks (user_id, title, completed) VALUES (:user_id, :title, :completed)");
    $stmt->execute([
        'user_id' => $userId,
        'title' => $data['title'],
        'completed' => 0, // Set 'completed' to 0 initially
    ]);

    // Get the last inserted task ID
    $taskId = $pdo->lastInsertId();

    // Return the newly created task as JSON
    echo json_encode([
        'id' => $taskId,
        'title' => $data['title'],
        'completed' => 0
    ]);
}

// Function to update an existing task
function updateTask($userId, $taskId) {
    global $pdo;
    // Decode JSON input data
    $data = json_decode(file_get_contents("php://input"), true);

    // Validate if title and completed are provided in the request
    if (empty($data['title']) || !isset($data['completed'])) {
        http_response_code(400); // Return 400 Bad Request if required fields are missing
        echo json_encode(['message' => 'Title, completed, and priority are required']);
        return;
    }

    // Prepare and execute SQL query to update the task
    $stmt = $pdo->prepare("UPDATE tasks SET title = :title, completed = :completed WHERE id = :id AND user_id = :user_id");
    $stmt->execute([
        'id'        => $taskId,
        'user_id'   => $userId,
        'title'     => $data['title'],
        'completed' => (int)$data['completed'],
    ]);

    // Return a success message
    echo json_encode(['message' => 'Task updated successfully']);
}

// Function to delete a specific task
function deleteTask($userId, $taskId) {
    global $pdo;

    // Prepare and execute SQL query to delete the task
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = :id AND user_id = :user_id");
    $stmt->execute([
        'id' => $taskId,
        'user_id' => $userId
    ]);

    // Check if the task was successfully deleted and return an appropriate message
    if ($stmt->rowCount() > 0) {
        echo json_encode(['message' => 'Task deleted']);
    } else {
        // Return 404 Not Found if no task was found or if the user is unauthorized
        http_response_code(404);
        echo json_encode(['message' => 'Task not found or unauthorized']);
    }
}
