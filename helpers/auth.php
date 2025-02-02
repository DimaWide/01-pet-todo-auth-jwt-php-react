<?php

// Import JWT and Key classes from the Firebase JWT library
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Function to generate a JWT (JSON Web Token)
function generateJWT($userId, $email) {
    $issuedAt = time();  // Current timestamp when the token is issued
    $expirationTime = $issuedAt + 11 * 60;  // Token will expire after 11 minutes
    $payload = [
        'iat' => $issuedAt,  // Issued at time
        'exp' => $expirationTime,  // Expiration time
        'userId' => $userId,  // User ID (to identify the user)
        'email' => $email,  // User's email
    ];

    // Encode the payload with the secret key and return the token
    return JWT::encode($payload, JWT_SECRET, 'HS256');
}

// Function to verify the JWT (decode and check if it's valid)
function verifyJWT($token) {
    try {
        // Try to decode the JWT using the secret key
        $decoded = JWT::decode($token, new Key(JWT_SECRET, 'HS256'));
        return $decoded;  // Return the decoded payload if the token is valid
    } catch (Exception $e) {
        return null;  // Return null if there is an error (e.g., expired or invalid token)
    }
}

// Function to check if the user is authenticated (based on Authorization header)
function isAuthenticated() {
    // Get all HTTP headers
    $headers = getallheaders();

    // Check if the Authorization header is set
    if (isset($headers['Authorization'])) {
        // Split the value into type and token (e.g., Bearer <token>)
        list($type, $token) = explode(' ', $headers['Authorization']);

        // Check if the type is 'Bearer' and the token is not empty
        if ($type === 'Bearer' && $token) {
            try {
                // Decode the token and verify it's valid
                $decoded = JWT::decode($token, new Key(JWT_SECRET, 'HS256'));

                // Return the user ID if the token is valid
                return $decoded->userId;
            } catch (Exception $e) {
                return false;  // Return false if the token is invalid or expired
            }
        }
    }

    return false;  // Return false if the Authorization header is missing or invalid
}

// Function to get user data from the token
function getUserFromToken() {
    // Get all HTTP headers
    $headers = getallheaders();

    // Check if the Authorization header is set
    if (!isset($headers['Authorization'])) {
        return null;  // Return null if no Authorization header is provided
    }

    // Remove the 'Bearer ' prefix from the token
    $token = str_replace('Bearer ', '', $headers['Authorization']);

    try {
        // Decode the token and get the user data
        $decoded = JWT::decode($token, new Key(JWT_SECRET, 'HS256'));

        // Return the user's email and ID as an object
        return (object) [
            'email' => $decoded->email,
            'user_id' => $decoded->userId,
        ];
    } catch (Exception $e) {
        return null;  // Return null if the token is invalid or expired
    }
}

// Function to check if the user is authenticated in the backend (using session)
function isAuthenticatedBackEnd() {
    // Check if there is a token in the session
    if (isset($_SESSION['token'])) {
        $token = $_SESSION['token'];

        try {
            // Decode the token and verify it's valid
            $decoded = JWT::decode($token, new Key(JWT_SECRET, 'HS256'));

            // Return the decoded token data if it's valid
            return $decoded;
        } catch (Exception $e) {
            return false;  // Return false if the token is invalid or expired
        }
    }

    return false;  // Return false if no token is found in the session
}

// Function to clear the authentication session (logout)
function clearAuthSession() {
    // Unset the token from the session
    unset($_SESSION['token']);
    // Delete the token cookie by setting its expiry time in the past
    setcookie('token', '', time() - 3600, "/");
}
