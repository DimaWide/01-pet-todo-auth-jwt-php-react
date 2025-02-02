<?php

// Include authentication helper functions (like verifyJWT)
require_once $_SERVER['DOCUMENT_ROOT'] . '/helpers/auth.php';

// authMiddlewareWeb function for web-based authentication
function authMiddlewareWeb() {
    // The return statement here should be removed as it causes the function to exit before checking the session
    return; // This line should be removed

    // Check if the 'token' exists in the session
    if (isset($_SESSION['token'])) {
        $token = $_SESSION['token'];  // Retrieve the token from the session
        $decoded = verifyJWT($token); // Verify the JWT token using the helper function
        
        // If the JWT is invalid, log the error and redirect to the login page
        if (!$decoded) {
            error_log("Invalid JWT token. Redirecting to login.");
            header('Location: /login');  // Redirect to login page
            exit;  // Exit the script to prevent further execution
        }
    } else {
        // If no token is found in the session, log the error and redirect to login page
        error_log("No token found in session. Redirecting to login.");
        header('Location: /login');  // Redirect to login page
        exit;  // Exit the script to prevent further execution
    }
}
