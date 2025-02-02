<?php

// Include the database connection file (assumes it's already defined in /config/database.php)
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.php';

// Function to create a new user in the database
function createUser($email, $password) {
    global $pdo;  // Access the global PDO instance (database connection)

    // Prepare an SQL statement to insert a new user into the 'users' table
    $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (:email, :password)");

    // Execute the query with the provided email and password
    return $stmt->execute(['email' => $email, 'password' => $password]);
}

// Function to find a user by their email address
function findUserByEmail($email) {
    global $pdo;  // Access the global PDO instance (database connection)

    // Prepare an SQL statement to select a user from the 'users' table based on email
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");

    // Execute the query and pass the email parameter
    $stmt->execute(['email' => $email]);

    // Fetch the first matching user record (or null if no match)
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Function to check if an email is already taken (used by another user)
function isEmailTaken($email) {
    global $pdo;  // Access the global PDO instance (database connection)

    // Prepare an SQL statement to count how many users have the provided email
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = :email');

    // Execute the query and pass the email parameter
    $stmt->execute(['email' => $email]);

    // Fetch the result (number of users with the provided email)
    $count = $stmt->fetchColumn();

    // Return true if the email is already taken, otherwise false
    return $count > 0;
}

// Function to find a user by their unique ID
function findUserById($id) {
    global $pdo;  // Access the global PDO instance (database connection)

    // Prepare an SQL statement to select a user from the 'users' table based on ID
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");

    // Execute the query and pass the user ID parameter
    $stmt->execute(['id' => $id]);

    // Fetch the user record (or null if no match)
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
