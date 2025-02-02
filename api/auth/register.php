<?php
// Include necessary helper files for user operations and logging
require_once $_SERVER['DOCUMENT_ROOT'] . '/helpers/users.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/helpers/logger.php';

// Set the response content type to JSON
header('Content-Type: application/json');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the JSON data from the request body and decode it into a PHP array
    $data = json_decode(file_get_contents("php://input"), true);

    // Validate and sanitize the email (removes unwanted characters)
    $email = filter_var($data['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = $data['password'] ?? ''; // Get password or set it as an empty string if not provided

    // Check if the email format is valid
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid email format.']);
        exit; // Stop further execution
    }

    // Check if the password is empty
    if (empty($password)) {
        http_response_code(400);
        echo json_encode(['error' => 'Password is required.']);
        exit; // Stop further execution
    }

    // Check if the password is at least 6 characters long
    if (strlen($password) < 6) {
        http_response_code(400);
        echo json_encode(['error' => 'Password must be at least 6 characters long.']);
        exit; // Stop further execution
    }

    // Check if the email is already taken by another user
    if (isEmailTaken($email)) {
        http_response_code(400);
        echo json_encode(['error' => 'Email is already in use.']);
        exit; // Stop further execution
    }

    // Hash the password using BCRYPT to store it securely in the database
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Try to create the user in the database and handle the result
    if (createUser($email, $hashedPassword)) {
        // Log the user registration action
        logUserAction('register', $email);

        http_response_code(201);
        echo json_encode(['message' => 'User registered successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to register user.']);
    }
}
