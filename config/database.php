<?php

try {
    // Определение параметров подключения
    $host = $config['database']['host'];               // Hostname of the database server (e.g., localhost)
    $dbname = $config['database']['dbname'];           // The name of the database to connect to
    $username = $config['database']['username'];       // The username for database access
    $password = $config['database']['password'];       // The password for database access
    $port = $config['database']['port'] ?? 5000;       // Port for the database connection, default is 5000 if not set

    // Creating the PDO instance to connect to the database
    $pdo = new PDO("mysql:host={$host};port={$port};dbname={$dbname}", $username, $password);

    // Set the error mode to exception to handle errors via exceptions
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // In case of an exception (error), terminate the script and display the error message
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

// Return the PDO instance for further use
return $pdo;
