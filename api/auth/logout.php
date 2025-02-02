<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/helpers/auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    clearAuthSession();
    echo json_encode(['success' => true, 'message' => 'Logout successful']);
} else {
    http_response_code(405);
    echo json_encode(['message' => 'Method Not Allowed']);
}
