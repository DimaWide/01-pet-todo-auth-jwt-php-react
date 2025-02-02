<?php

// Include the authentication helper functions (verifyJWT function is expected here)
require_once __DIR__ . '/../helpers/auth.php';

// Authentication middleware function
function authMiddleware() {
    
    // Check if the 'token' cookie is set
    if (!isset($_COOKIE['token'])) {
        // If the token is not set, respond with a 401 Unauthorized status
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit; // Stop execution
    }

    // Retrieve the token from the cookie
    $token = $_COOKIE['token'];
    
    // Verify the JWT (token) using the helper function 'verifyJWT'
    $decoded = verifyJWT($token);

    // If the token is invalid (couldn't decode), respond with an error
    if (!$decoded) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid token']);
        exit; // Stop execution
    }

    // If the token is valid, return the userId from the decoded JWT to be used in protected routes
    return $decoded->userId;
}
