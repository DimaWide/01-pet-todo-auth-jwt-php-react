<?php

namespace App\Services;

use \Firebase\JWT\JWT;

class JwtService {

    // Секретный ключ для подписи JWT токенов
    private $secretKey = 'your-secret-key';

    // Время жизни токена (например, 3600 секунд = 1 час)
    private $issuedAt;
    private $expirationTime = 3600; // 1 час
    private $issuer = 'your-issuer'; // Может быть вашим доменом или сервисом

    public function __construct() {
        $this->issuedAt = time();
    }

    public static function encode($payload) {
        return JWT::encode($payload, self::$secretKey);
    }

    public static function decode($jwt) {
        return (array)JWT::decode($jwt, self::$secretKey, ['HS256']);
    }

    // Создание JWT токена
    public function createToken($userId) {
        // Данные, которые будут включены в токен
        $payload = [
            'iat' => $this->issuedAt,         // Время создания
            'exp' => $this->issuedAt + $this->expirationTime, // Время истечения
            'iss' => $this->issuer,           // Источник
            'sub' => $userId                  // Идентификатор пользователя
        ];

        // Подпись и создание токена
        return JWT::encode($payload, $this->secretKey);
    }

    // Верификация JWT токена
    public function verifyToken($token) {
        try {
            // Расшифровка токена и верификация
            $decoded = JWT::decode($token, $this->secretKey, ['HS256']);
            return (array) $decoded; // Возвращаем расшифрованные данные токена
        } catch (\Exception $e) {
            return false; // Если токен невалиден
        }
    }

    // Извлечение данных из токена
    public function getDataFromToken($token) {
        $decoded = $this->verifyToken($token);
        return $decoded ? $decoded : null;
    }
}
