<?php
// /app/config.php

// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// Define the base URL for the project (public directory)
define('BASE_URL', 'http://dev.todo/public/');

// Define the base path for the project (the root directory of the project)
define('BASE_PATH', dirname(__DIR__) . '/'); // Absolute path to the root directory of your project

// Configuration for error handling (for development/debugging)
ini_set('log_errors', 1); // Enable error logging
ini_set('error_log', __DIR__ . '/../logs/error.log'); // Define the error log file path (relative to the current script)

// Return configuration settings for database and session
return [
    'database' => [
        'host' => 'localhost',        // Database host (e.g., localhost)
        'dbname' => 'dev.todo',       // Database name
        'username' => 'root',         // Database username
        'password' => 'root',         // Database password
        'port' => '5000',             // Database port (default for MySQL is 3306, here it's set to 5000)
    ],
    'session' => [
        'timeout' => 1800,            // Session timeout in seconds (1800 seconds = 30 minutes)
    ],
];
