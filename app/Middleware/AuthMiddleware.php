<?php


namespace App\Middleware;

use App\Services\JwtService;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;

class AuthMiddleware
{
    // Проверка наличия и валидности JWT в заголовке запроса
    public static function handle($request)
    {
        $authHeader = $request->getHeader('Authorization'); // Получаем заголовок Authorization
        var_dump($authHeader);

        if (empty($authHeader)) {
            // Если заголовка нет, возвращаем ошибку
            return self::unauthorized('Authorization header is missing');
        }

        // Разбираем токен из заголовка (Authorization: Bearer <token>)
        $matches = [];
        if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return self::unauthorized('Authorization token is missing or malformed');
        }

        $jwt = $matches[1]; // Токен после "Bearer"

        try {
            // Проверяем валидность токена через сервис JWT
            $decoded = JwtService::decode($jwt);

            // Добавляем информацию о пользователе в запрос для дальнейшего использования
            $request->user = $decoded;

        } catch (ExpiredException $e) {
            // Если токен просрочен
            return self::unauthorized('Token has expired');
        } catch (SignatureInvalidException $e) {
            // Если подпись токена неверна
            return self::unauthorized('Invalid token signature');
        } catch (\Exception $e) {
            // Для любых других ошибок
            return self::unauthorized('Authorization failed');
        }

        return null; // Если все прошло успешно, запрос передается дальше
    }

    // Метод для отправки ответа об ошибке авторизации
    private static function unauthorized($message)
    {
        http_response_code(401);
        echo json_encode([
            'error' => 'Unauthorized',
            'message' => $message
        ]);
        exit();
    }
}





// namespace App\Middleware;

// use App\Services\JwtService;

// class AuthMiddleware {

//     private $jwtService;

//     public function __construct(JwtService $jwtService) {
//         $this->jwtService = $jwtService;
//     }

//     public function handle() {
//         // Проверка наличия токена в заголовках
//         $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? null;

//         if (!$authHeader) {
//             $this->sendError("Authorization token is missing", 401);
//         }

//         // Убираем префикс 'Bearer '
//         $token = str_replace('Bearer ', '', $authHeader);

//         // Проверка токена
//         if (!$this->jwtService->verifyToken($token)) {
//             $this->sendError("Unauthorized", 401);
//         }

//         // Если токен валиден, продолжаем выполнение запроса
//     }

//     private function sendError($message, $statusCode) {
//         http_response_code($statusCode);
//         echo json_encode(["error" => $message]);
//         exit();
//     }
// }
