<?php


// Check if the request is an OPTIONS request (preflight request for CORS)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");  // Allow all domains or specify your frontend domain
    header("Access-Control-Allow-Credentials: true");  // Allow sending cookies
    header("Access-Control-Allow-Headers: Content-Type, Authorization");  // Allow specific headers
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");  // Allow specific methods
    header("HTTP/1.1 200 OK");
    exit;
}

// Set the headers for CORS requests
header("Access-Control-Allow-Origin: *");  // Allow all domains or specify your frontend domain
header("Access-Control-Allow-Origin: http://localhost:3000");  // Specify your frontend domain
header("Access-Control-Allow-Credentials: true");  // Allow sending cookies
header("Access-Control-Allow-Headers: Content-Type, Authorization");  // Allow specific headers
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");  // Allow specific methods

// If the request method is OPTIONS, send a 200 OK response
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../vendor/autoload.php';  // Autoload dependencies

// Start the session
session_start();

// Define a secret key for JWT authentication
define('JWT_SECRET', 'your_secret_key');

// Load configuration and database connection
$config = require_once '../config/config.php';
$pdo = require_once '../config/database.php';

// Include the authentication middleware
require_once $_SERVER['DOCUMENT_ROOT'] . '/middlewares/authMiddlewareWeb.php';

// Get the request URI and method
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$request_method = $_SERVER['REQUEST_METHOD'];

// Remove "/public" from the request URI if it is present
$request_uri = ltrim($request_uri, '/');
if (strpos($request_uri, 'public/') === 0) {
    $request_uri = substr($request_uri, strlen('public/'));
}

// If the request URI starts with "/api", handle the API request
if (preg_match('/^api\//', $request_uri)) {
    handleApiRequest($request_uri);  // Process API request
    exit;
}

// Handle regular page requests (e.g., home, login, tasks)
handlePageRequest($request_uri);


/**
 * Function to handle API requests
 */
function handleApiRequest($uri) {
    $route = substr($uri, 4);
    $route_parts = explode('/', $route);
    switch ($route_parts[0]) {
        case 'tasks':
            require_once '../api/tasks.php';  // Handle tasks
            break;
        case 'check-token':
            require_once '../api/check-token.php';  // Check the token validity
            break;
        case 'authentication':
            require_once '../api/auth/authentication.php';  // Handle authentication
            break;
        case 'register':
            require_once '../api/auth/register.php';  // Handle registration
            break;
        case 'login':
            require_once '../api/auth/login.php';  // Handle login
            break;
        case 'logout':
            require_once '../api/auth/logout.php';  // Handle logout
            break;
        default:
            http_response_code(404);  // If API route is not found, return a 404
            echo json_encode(['message' => 'API route not found']);
            break;
    }
}


/**
 * Function to handle regular page requests
 */
function handlePageRequest($uri) {
    // Initialize the content variable for the page
    $content = ''; 

    // Determine the page content based on the URI
    switch ($uri) {
        case '':
            authMiddlewareWeb();  // Check for authorization
            $content = BASE_PATH . '/app/views/content/home.php';  // Home page
            break;
        case 'index':
            $content = BASE_PATH . '/app/views/content/index.php';  // Index page
            break;
        case 'login':
            $content = BASE_PATH . '/app/views/content/login.php';  // Login page
            break;
        case 'register':
            $content = BASE_PATH . '/app/views/content/register.php';  // Register page
            break;
        default:
            http_response_code(404);  // If page not found, return a 404
            $content = BASE_PATH . '/app/views/content/404.php';  // 404 page
            break;
    }

    // If content is set, process the page content
    if (!empty($content)) {
        ob_start();  // Start output buffering
        require_once $content;  // Include the page content file
        $content = ob_get_clean();  // Get the buffered content

        // Include the main template to display the page
        include '../app/views/main.php';
    }
}
