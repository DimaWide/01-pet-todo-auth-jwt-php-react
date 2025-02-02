<?php

// Import required classes from the Firebase JWT library
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;

// Set the response content type to JSON
header('Content-Type: application/json');

// Check if the token exists in the cookies
if (!isset($_COOKIE['token'])) {
    // If no token is found, return a 401 Unauthorized response
    http_response_code(401);
    echo json_encode(['error' => 'Token not found']);
    exit; // Stop further execution
}

$token = $_COOKIE['token']; // Retrieve the token from the cookies

try {
    // Decode the JWT token using the secret key and algorithm (HS256)
    $decoded = JWT::decode($token, new Key(JWT_SECRET, 'HS256'));

    // Get the expiration time (exp) from the decoded token
    $expirationTime = $decoded->exp;

    // Check if the token has expired (i.e., if the expiration time is less than the current time)
    if ($expirationTime < time()) {
        // Throw an exception if the token is expired
        throw new ExpiredException('Expired token');
    }

    // If everything is fine, return the expiration time in the response
    echo json_encode(['exp' => $expirationTime]);
} catch (ExpiredException $e) {
    // Catch the exception for expired tokens and return a 401 Unauthorized response
    http_response_code(401);
    echo json_encode(['error' => 'Token expired']);
} catch (Exception $e) {
    // Catch other errors (e.g., invalid token) and return a 401 Unauthorized response
    http_response_code(401);
    echo json_encode(['error' => 'Invalid token']);
}

