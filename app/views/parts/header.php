<!-- header.php -->

<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/helpers/auth.php';

$title = "Home - JWT Auth"; // Page title
$appName = "JWT Auth"; // Application name
$user = isAuthenticatedBackEnd();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL . 'css/styles.min.css'; ?>">
</head>

<body>

    <style>
        body {
            font-family: 'Ubuntu';
        }
    </style>

    <div class="wcl-body-inner bg-gray-100 ">

        <header class="bg-gradient-to-r from-blue-600 to-blue-800 text-white p-6 shadow-lg">
            <div class="wcl-container mx-auto flex justify-between items-center">
                <!-- Logo -->
                <div class="logo">
                    <a href="<?php echo BASE_URL; ?>" class="text-4xl font-extrabold"><?php echo $appName; ?></a>
                </div>

                <!-- Mobile Hamburger Icon -->
                <div class="block lg:hidden">
                    <button id="hamburger" class="text-white focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>

                <!-- Navigation Links -->
                <nav id="navMenu" class="hidden lg:flex space-x-4">
                    <a href="<?php echo BASE_URL; ?>" class="font-medium hover:text-gray-200 transition-colors duration-300">Home</a>

                    <?php if (!empty($user)): ?>
                        <span class="font-medium"> Hello: <?php echo $user->email; ?></span>
                        <a href="<?php echo BASE_URL . 'logout'; ?>" id="logout-button" class="font-medium hover:text-gray-200 transition-colors duration-300">Logout</a>
                    <?php else: ?>
                        <a href="<?php echo BASE_URL . 'login'; ?>" class="font-medium hover:text-gray-200 transition-colors duration-300">Login</a>
                        <a href="<?php echo BASE_URL . 'register'; ?>" class="font-medium hover:text-gray-200 transition-colors duration-300">Register</a>
                    <?php endif; ?>
                </nav>
            </div>

            <!-- Mobile Menu (hidden by default) -->
            <div id="mobileMenu" class="lg:hidden hidden bg-blue-700 p-4 space-y-4">
                <a href="<?php echo BASE_URL; ?>" class="block font-medium hover:text-gray-200 transition-colors duration-300">Home</a>

                <?php if (!empty($user)): ?>
                    <span class="block font-medium"> Hello: <?php echo $user->email; ?></span>
                    <a href="<?php echo BASE_URL . 'logout'; ?>" id="logout-button" class="block font-medium hover:text-gray-200 transition-colors duration-300">Logout</a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL . 'login'; ?>" class="block font-medium hover:text-gray-200 transition-colors duration-300">Login</a>
                    <a href="<?php echo BASE_URL . 'register'; ?>" class="block font-medium hover:text-gray-200 transition-colors duration-300">Register</a>
                <?php endif; ?>
            </div>
        </header>

        <script>
            // JavaScript to toggle the mobile menu
            const hamburger = document.getElementById('hamburger');
            const mobileMenu = document.getElementById('mobileMenu');

            hamburger.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        </script>