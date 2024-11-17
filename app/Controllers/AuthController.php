<?php 
namespace App\Controllers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\User;
use Exception;

class AuthController {
    private $userModel;
    private $secretKey;
    private $refreshSecretKey;

    public function __construct() {
        $this->userModel = new User();
        $this->secretKey = getenv('JWT_SECRET_KEY') ?: 'default_secret_key';
        $this->refreshSecretKey = getenv('JWT_REFRESH_SECRET_KEY') ?: 'default_refresh_secret_key';
    }

    public function login() {
        $data = json_decode(file_get_contents("php://input"), true);

        // Проверка на обязательные поля
        if (empty($data['username']) || empty($data['password'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Invalid data. Username and password are required.']);
            exit;
        }

        $user = $this->userModel->findByUsername($data['username']);
        // var_dump($user);
        // var_dump($data['password']);
        if ($user && password_verify($data['password'], $user['password'])) {
            $issuedAt = time();
            $accessExpirationTime = $issuedAt + 15 * 60; // 15 минут
            $refreshExpirationTime = $issuedAt + 7 * 24 * 60 * 60; // 7 дней

            // Платежи для Access и Refresh токенов
            $accessPayload = [
                'iat' => $issuedAt,
                'exp' => $accessExpirationTime,
                'username' => $user['username'],
                'user_id' => $user['id']
            ];

            $refreshPayload = [
                'iat' => $issuedAt,
                'exp' => $refreshExpirationTime,
                'username' => $user['username'],
                'user_id' => $user['id']
            ];

            // Генерация токенов
            $accessToken = JWT::encode($accessPayload, $this->secretKey, 'HS256');
            $refreshToken = JWT::encode($refreshPayload, $this->refreshSecretKey, 'HS256');

            // Устанавливаем токены в заголовки и куки
            header('Authorization: Bearer ' . $accessToken);
            setcookie("token", $accessToken, $accessExpirationTime, "/", "");
            setcookie("refresh_token", $refreshToken, $refreshExpirationTime, "/", "");

            echo json_encode(['message' => 'Login successful', 'token' => $accessToken]);
            exit;
        } else {
            http_response_code(401);
            echo json_encode(['message' => 'Invalid credentials']);
            exit;
        }
    }

    public function refresh() {
        if (isset($_COOKIE['refresh_token'])) {
            try {
                $refreshToken = $_COOKIE['refresh_token'];
                $decoded = JWT::decode($refreshToken, new Key($this->refreshSecretKey, 'HS256'));

                if ($decoded->exp < time()) {
                    http_response_code(401);
                    echo json_encode(['message' => 'Refresh token expired']);
                    exit;
                }

                $issuedAt = time();
                $accessExpirationTime = $issuedAt + 15 * 60; // 15 минут
                $accessPayload = [
                    'iat' => $issuedAt,
                    'exp' => $accessExpirationTime,
                    'username' => $decoded->username,
                    'user_id' => $decoded->user_id
                ];

                $newAccessToken = JWT::encode($accessPayload, $this->secretKey, 'HS256');
                header('Authorization: Bearer ' . $newAccessToken);
                echo json_encode(['access_token' => $newAccessToken]);
                exit;
            } catch (Exception $e) {
                http_response_code(401);
                echo json_encode(['message' => 'Invalid or expired refresh token']);
                exit;
            }
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Refresh token is missing']);
            exit;
        }
    }

    public function register() {
        $data = json_decode(file_get_contents("php://input"), true);

        // Проверка на обязательные поля
        if (empty($data['username']) || empty($data['password'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Invalid data. Username and password are required.']);
            exit;
        }

        $username = $data['username'];
        $password = $data['password'];

        // Минимальная длина пароля
        if (strlen($password) < 1) {
            http_response_code(400);
            echo json_encode(['message' => 'Password too short, must be at least 8 characters']);
            exit;
        }

        $existingUser = $this->userModel->findByUsername($username);

        if ($existingUser) {
            http_response_code(400);
            echo json_encode(['message' => 'Username already exists']);
            exit;
        }

        // Создание нового пользователя
        $this->userModel->create($username, $password);
        echo json_encode(['message' => 'User registered successfully']);
        exit;
    }
}
