<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo Application</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        header {
            background: #007BFF;
            color: white;
            padding: 15px;
            text-align: center;
        }
        main {
            padding: 20px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Welcome to the Todo Application</h1>
    </header>
    <main>
        <p>This is the main page of your Todo application.</p>
    </main>
</body>
</html> -->



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager</title>
    <!-- <link rel="stylesheet" href="./css/styles.min.css"> -->
    <link rel="stylesheet" href="<?php echo BASE_URL . 'css/styles.min.css'; ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/semantic.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Faculty+Glyphic&family=Host+Grotesk:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Host Grotesk', sans-serif;
            font-weight: 300;
        }
    </style>
</head>

<body>
    <div class="wds-body-inner" id="body-inner">
        <header class="cmp-header">
            <div class="data-row">
                <div class="data-col">
                    <div class="data-logo">
                        <a href="/">Task Manager</a>
                    </div>
                </div>

                <div class="data-col">
                    <div class="data-btns">
                        <?php if (empty($user_id)): ?>
                            <a href="javascript:showLoginForm()" class="data-btns-item">
                                <span>Login</span>

                                <i class="sign in icon"></i>
                            </a>
                            <a href="javascript:showRegisterForm()" class="data-btns-item">
                                <span>Register</span>

                                <i class="user plus icon"></i>
                            </a>
                        <?php else: ?>
                            <button href="/logout" onclick="logout()" class="data-btns-item">
                                <i class="sign-out icon"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </header>

        <div class="ui container">
            <!-- Login Form -->
            <div id="login-section" class="ui segment auth-section">
                <h2 class="ui header">Login</h2>
                <form id="login-form" class="ui form" onsubmit="login(event)">
                    <div class="field">
                        <label>Username</label>
                        <input type="text" id="login-username" placeholder="Username" required>
                    </div>
                    <div class="field">
                        <label>Password</label>
                        <input type="password" id="login-password" placeholder="Password" required>
                    </div>
                    <button type="submit" class="ui primary button">Login</button>
                </form>
                <div class="ui message">
                    Don't have an account? <a href="javascript:showRegisterForm()">Register here</a>
                </div>

                <div class="ui negative message" id="login-error-message" style="display:none;">
                    <i class="close icon" onclick="closeError()"></i>
                    <!-- <div class="header">
                          Error occurred
                        </div> -->
                    <p id="login-error-text">Something went wrong, please try again.</p>
                </div>

            </div>

            <!-- Registration Form -->
            <div id="register-section" class="ui segment auth-section register-section" style="display: none;">
                <h2 class="ui header">Register</h2>
                <form id="register-form" class="ui form" onsubmit="register(event)">
                    <div class="field">
                        <label>Username</label>
                        <input type="text" id="register-username" placeholder="Username" required>
                    </div>
                    <div class="field">
                        <label>Password</label>
                        <input type="password" id="register-password" placeholder="Password" required>
                    </div>
                    <button type="submit" class="ui primary button">Register</button>
                </form>
                <div class="ui message">
                    Already have an account? <a href="javascript:showLoginForm()">Login here</a>
                </div>

                <div class="ui negative message" id="register-error-message" style="display:none;">
                    <i class="close icon" onclick="closeError()"></i>
                    <!-- <div class="header">
                          Error occurred
                        </div> -->
                    <p id="register-error-text">Something went wrong, please try again.</p>
                </div>

            </div>
        </div>

        <?php
        if (! empty($e)) {
        ?>
            <div class="cmp-error-token">
                <?php echo "Error: " . $e->getMessage(); ?>
            </div>
        <?php

        } else {
        ?>
            <!-- Главная страница с задачами -->
            <main class="cmp-todo" id="task-manager" style="display: none;">
                <div class="data-container">
                    <div class="data-form-out" id="add-task">
                        <form class="data-form" id="task-form">
                            <input type="text" id="task-title" name="task-title" placeholder="Enter a task"
                                required>
                            <button type="submit" class="">Add</button>
                        </form>
                    </div>

                    <div class="cmm-task data-list " id="task-list">

                    </div>
                </div>
            </main>
        <?php
        }
        ?>



        <footer class="cmp-footer ui inverted segment center aligned">
            <p>© 2024 Task Manager</p>
        </footer>

        <script src="./js/app.js"></script>
    </div>

</body>

</html>
