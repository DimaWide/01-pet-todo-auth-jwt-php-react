<?php

// Include necessary files for user handling, authentication, and logging
require_once $_SERVER['DOCUMENT_ROOT'] . '/helpers/users.php'; // File containing functions for user handling
require_once $_SERVER['DOCUMENT_ROOT'] . '/helpers/auth.php';  // File containing functions for authentication
require_once $_SERVER['DOCUMENT_ROOT'] . '/helpers/logger.php'; // File containing logging functions

// Start the session to work with session data
session_start();

// Set the response header as JSON
header('Content-Type: application/json');

// Check if the request method is GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    // Check if the user is authenticated
    $userId = isAuthenticated();

    if ($userId) {
        // If the user is authenticated, get the user data
        $user = getUserFromToken();

        // Send a response with the user data
        echo json_encode(['userId' => $userId, 'user' => $user]);
    } else {
        // If the user is not authenticated, send a 401 Unauthorized error
        http_response_code(401);
        echo json_encode(['message' => 'Unauthorized']);
    }
} else {
    // If the request is not GET, send a 404 Not Found error
    http_response_code(404);
    echo json_encode(['message' => 'Not Found']);
}
