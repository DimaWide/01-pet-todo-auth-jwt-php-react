<?php

namespace App\Middleware;

use App\Services\JwtService;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Exception;

class AuthMiddleware {
    private $secretKey;
    private $refreshSecretKey;

    public function __construct() {
        $this->secretKey = getenv('JWT_SECRET_KEY') ?: 'default_secret_key';
        $this->refreshSecretKey = getenv('JWT_REFRESH_SECRET_KEY') ?: 'default_refresh_secret_key';
    }

    // Проверка наличия и валидности JWT в заголовке запроса
    public function handle($request) {
        $authHeader = $request->getHeader('Authorization');

        if (empty($authHeader)) {
            return $this->unauthorized('Authorization header is missing');
        }

        // Извлечение токена из заголовка
        if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return $this->unauthorized('Authorization token is missing or malformed');
        }

        $jwt = $matches[1];

        if (isset($_SESSION['user_id_refresh_token'])) {
            error_log("Authorization header: " . print_r('refresh 3', true));
            return null;
        }

        try {
            error_log("Authorization header: " . print_r('refresh 1', true));
            // Проверяем валидность токена
            $decoded = JwtService::decode($jwt);
            //   $request->user = $decoded; // Добавляем данные пользователя в запрос

            // Обновляем токен, если близко время истечения
            error_log("Authorization header: " . print_r('refresh 1', true));
            // Проверяем, истек ли токен (поле exp - время истечения в UNIX timestamp)
            if (isset($decoded->exp) && $decoded->exp < time()) {
                // Если истек, удаляем токен из cookies
                // setcookie("token", "", time() - 3600, "/"); // Удаляем куки

                return null;
            }
        } catch (ExpiredException $e) {
            error_log("Authorization header: " . print_r('refresh 2', true));
           $this->refresh();
           return $this->unauthorized('Token has expired');
        } catch (SignatureInvalidException $e) {
            return $this->unauthorized('Invalid token signature');
        } catch (Exception $e) {
            return $this->unauthorized('Authorization failed');
        }

        return null; // Если все в порядке, запрос передается дальше
    }

    // Метод для обновления токенов
    private function refresh() {
        if (isset($_COOKIE['refresh_token'])) {
            try {
                $refreshToken = $_COOKIE['refresh_token'];
                $decodedRefresh = JWT::decode($refreshToken, new Key($this->refreshSecretKey, 'HS256'));

                if ($decodedRefresh->exp < time()) {
                    return $this->unauthorized('Refresh token expired');
                }

                // Генерация нового access-токена
                $issuedAt = time();
                $accessExpirationTime = $issuedAt + 3600; // 1 час
                $accessPayload = [
                    'iat' => $issuedAt,
                    'exp' => $accessExpirationTime,
                    'username' => $decodedRefresh->username,
                    'user_id' => $decodedRefresh->user_id,
                ];

                error_log("Authorization header: " . print_r($accessPayload, true));

                $newAccessToken = JWT::encode($accessPayload, $this->secretKey, 'HS256');

                $_SESSION['user_id_refresh_token'] = $decodedRefresh->user_id;

                // Обновляем cookie и заголовок Authorization
                setcookie("token", $newAccessToken, $accessExpirationTime, "/", "");
                header('Authorization: Bearer ' . $newAccessToken);

            } catch (Exception $e) {
                return $this->unauthorized('Invalid or expired refresh token');
            }
        } else {
            return $this->unauthorized('Refresh token is missing');
        }
    }

    // // Пример метода для добавления задачи
    // public function addTask($request) {
    //     $taskData = $request->getParsedBody();

    //     if (empty($taskData['task_name'])) {
    //         return $this->unprocessableEntity('Task name is required');
    //     }

    //     // Логика для добавления задачи в базу данных или куда-то еще
    //     $task = new Task();
    //     $task->name = $taskData['task_name'];
    //     $task->user_id = $request->user->user_id; // Получаем ID пользователя из токена
    //     $task->save();

    //     return $this->jsonResponse(['message' => 'Task successfully added']);
    // }

    // Ответ об ошибке авторизации
    private function unauthorized($message) {
        http_response_code(401);
        echo json_encode([
            'error' => 'Unauthorized',
            'message' => $message,
        ]);
        exit();
    }
}
