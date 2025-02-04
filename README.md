## JWT Authentication with Token Expiry Countdown & Todo Task Rest Aoi

This project implements a web application with JWT-based authentication, token expiration management, and a REST API for managing Todo tasks. It includes features for user registration, login, session management, task creation, updating, and deletion, with a countdown timer to notify users about token expiration.

Screenshots:    
[JWT Expiration](https://github.com/DimaWide/01-pet-todo-auth-jwt-php-react/blob/main/public/img/jwt.png)     
[Login](https://github.com/DimaWide/01-pet-todo-auth-jwt-php-react/blob/main/public/img/login.png) 

![Main](https://github.com/DimaWide/01-pet-todo-auth-jwt-php-react/blob/main/public/img/jwt.png)

## Features

- **JWT Authentication**: Secure login and session management using JSON Web Tokens (JWT).
- **Token Expiration Countdown**: Displays a countdown timer to show how much time remains before the token expires.
- **User Registration**: Allows users to register with an email and password.
- **User Login**: Provides a login form for users to authenticate.
- **Logout**: Users can log out, which removes the token from local storage and cookies.
- **Todo Task Management**:
  - **Create**: Users can create new tasks.
  - **Update**: Users can update existing tasks.
  - **Delete**: Users can delete tasks.
  - **List**: Users can view their tasks.

## Technologies

- **PHP**: Backend server-side logic.
- **MySQL**: Database for storing user and task data.
- **JavaScript**: Frontend logic to handle the countdown timer and token validation.
- **JWT**: For secure user authentication.
