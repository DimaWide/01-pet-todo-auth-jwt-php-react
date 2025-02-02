<?php

// Include necessary helper files for users, authentication, and logging
require_once $_SERVER['DOCUMENT_ROOT'] . '/helpers/users.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/helpers/auth.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/helpers/logger.php';

// Start a new session for the user
session_start();

// Set the response content type to JSON
header('Content-Type: application/json');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Decode the incoming JSON data into a PHP array
    $data = json_decode(file_get_contents("php://input"), true);
    $email = $data['email'] ?? ''; // Get email from data or set it as an empty string if not provided
    $password = $data['password'] ?? ''; // Get password from data or set it as an empty string if not provided

    // Check if email or password is empty
    if (empty($email) || empty($password)) {
        http_response_code(400);
        echo json_encode(['error' => 'Email and password are required.']);
        exit; // Stop further execution
    }

    // Validate the email format using PHP's filter_var function
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid email format.']);
        exit; // Stop further execution
    }

    // Try to find the user by email in the database
    $user = findUserByEmail($email);
    
    // Check if a user was found and if the password matches the hashed password in the database
    if ($user && password_verify($password, $user['password'])) {
        // Generate a JWT token for the authenticated user
        $token = generateJWT($user['id'], $user['email']);

        // Log the user action (login)
        logUserAction('login', $user['id']);

        // Set a cookie with the JWT token, which expires in 1 hour
        setcookie('token', $token, [
            'expires' => time() + 3600, // Token expires in 1 hour
            'path' => '/',              // Available across the whole site
            'domain' => $_SERVER['HTTP_HOST'], // Current domain
            'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on', // Use secure flag if HTTPS is enabled
            'httponly' => true,          // Prevent JavaScript from accessing the cookie
            'samesite' => 'Strict'       // Prevent CSRF attacks by only sending the cookie for same-site requests
        ]);

        // Store the token in the session for easy access
        $_SESSION['token'] = $token;

        // Send a 200 OK response with the success message and token
        http_response_code(200);
        echo json_encode(['message' => 'Login successful', 'token' => $token]);
    } else {
        // Send a 401 Unauthorized response if login failed (invalid email or password)
        http_response_code(401);
        echo json_encode(['error' => 'Invalid email or password.']);
    }
}
