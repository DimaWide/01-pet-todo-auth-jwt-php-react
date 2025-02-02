<?php

require_once '../../middlewares/authMiddleware.php';
require_once '../../helpers/auth.php';
require_once '../../helpers/user.php';

session_start();
header('Content-Type: application/json');

$userId = authMiddleware();

if ($userId) {
    http_response_code(200);
    echo json_encode(['message' => 'Welcome to the protected page!', 'user' => $userId]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'User not found']);
}
