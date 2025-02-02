document.addEventListener("DOMContentLoaded", function () {
    // Getting references to the login and registration forms and API URL
    const loginForm = document.getElementById("login-form");
    const registerForm = document.getElementById("register-form");
    const apiUrl = 'http://dev.todo/api';

    // Getting reference to the logout button
    const logoutButton = document.getElementById('logout-button');

    // Adding event listener to the logout button to handle logout functionality
    if (logoutButton) {
        logoutButton.addEventListener('click', (e) => {
            e.preventDefault();

            // Making a request to the server to log out the user
            fetch(apiUrl + '/logout', {
                method: 'POST', // Using POST method
                headers: {
                    'Content-Type': 'application/json' // Setting content type as JSON
                },
                body: JSON.stringify({
                    // Add any required data, such as token, if needed
                })
            })
                .then(response => response.json()) // Getting JSON response from the server
                .then(data => {
                    if (data.success) {
                        // If successful, clear localStorage tokens and redirect to login page
                        console.log('Logout successful!');

                        localStorage.removeItem('token');  // Remove token from localStorage
                        localStorage.removeItem('refresh_token');  // Remove refresh_token if used

                        // Redirecting user to login page
                        window.location.href = '/public/login'; // Redirecting to the login page
                    } else {
                        // If there's an error, log it to the console
                        console.error('Logout error:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Network error:', error);
                });
        });
    }

    // Function to create a message element for displaying validation or error messages
    function createMessageElement() {
        const messageElement = document.createElement("div");
        messageElement.className = "message"; // Adding a class for styling
        return messageElement; // Returning the created element
    }

    // Function to validate user input for email and password
    function validateInput(email, password) {
        if (!email || email.length < 3) {
            return "Email must be at least 3 characters long.";
        }
        if (!password || password.length < 6) {
            return "Password must be at least 6 characters long.";
        }
        return null; // Validation is successful
    }

    // Handling form submission for registration
    if (registerForm) {
        registerForm.addEventListener("submit", function (event) {
            event.preventDefault(); // Preventing default form submission
            const email = document.getElementById("email").value.trim();
            const password = document.getElementById("password").value.trim();

            if (registerForm.querySelector('.message')) {
                registerForm.querySelector('.message').remove() // Remove previous message if any
            }

            // Validate email and password
            const validationError = validateInput(email, password);
            if (validationError) {
                // If validation fails, create and show an error message
                const messageElement = createMessageElement();
                messageElement.textContent = validationError;
                messageElement.classList.add("error");
                registerForm.appendChild(messageElement); // Append the message to the form
                return;
            }

            // Create an element for displaying the result message
            const messageElement = createMessageElement();
            registerForm.appendChild(messageElement); // Append it to the form

            // Sending a request to the server to register the user
            fetch(apiUrl + '/register', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email, password })
            })
                .then(response => response.json())
                .then(data => {
                    messageElement.textContent = data.message; // Show registration status message
                    messageElement.classList.remove("error"); // Remove error class if any
                    if (data.message === 'User registered successfully') {
                        messageElement.classList.add("success"); // Add success class if registration is successful
                        registerForm.reset(); // Reset the form after successful registration

                        setTimeout(() => {
                            location.href = '/public/login' // Redirecting to login page
                        }, 500);
                    } else {
                        messageElement.classList.add("error"); // Add error class if registration fails
                    }
                })
                .catch(error => {
                    messageElement.textContent = "An error occurred: " + error.message; // Network error handling
                    messageElement.classList.add("error");
                });
        });
    }

    // Handling form submission for login
    if (loginForm) {
        loginForm.addEventListener("submit", function (event) {
            event.preventDefault(); // Preventing default form submission
            const email = document.getElementById("email").value.trim();
            const password = document.getElementById("password").value.trim();

            if (loginForm.querySelector('.message')) {
                loginForm.querySelector('.message').remove() // Remove previous message if any
            }

            // Validate email and password
            const validationError = validateInput(email, password);
            if (validationError) {
                // If validation fails, create and show an error message
                const messageElement = createMessageElement();
                messageElement.textContent = validationError;
                messageElement.classList.add("error");
                loginForm.appendChild(messageElement); // Append the message to the form
                return;
            }

            // Create an element for displaying the result message
            const messageElement = createMessageElement();
            loginForm.appendChild(messageElement); // Append it to the form

            // Sending a request to the server to log in the user
            fetch(apiUrl + '/login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email, password })
            })
                .then(response => response.json())
                .then(data => {
                    console.log(data)
                    messageElement.textContent = data.message; // Show login status message
                    messageElement.classList.remove("error");
                    if (data.message === 'Login successful') {
                        messageElement.classList.add("success"); // Add success class if login is successful

                        if (data.token) {
                            // Save token to localStorage if it's provided
                        }

                        setTimeout(() => {
                            location.href = '/public/' // Redirecting to tasks page after successful login
                        }, 500);
                    } else {
                        messageElement.classList.add("error"); // Add error class if login fails
                    }
                })
                .catch(error => {
                    messageElement.textContent = "An error occurred: " + error.message; // Network error handling
                    messageElement.classList.add("error");
                });
        });
    }
});
