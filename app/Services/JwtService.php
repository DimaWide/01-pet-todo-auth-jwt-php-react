<?php

namespace App\Services;

use \Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtService {

    // Секретный ключ для подписи JWT токенов
    private static $secretKey = 'default_secret_key';

    // Время жизни токена (например, 3600 секунд = 1 час)
    private $issuedAt;
    private $expirationTime = 3600; // 1 час
    private $issuer = 'your-issuer'; // Может быть вашим доменом или сервисом

    public function __construct() {
        $this->issuedAt = time();
    }

    public static function encode($payload) {
        return JWT::encode($payload, self::$secretKey, 'HS256');
    }

    public static function decode($jwt) {
        return JWT::decode($jwt, new Key(self::$secretKey, 'HS256'));
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
        return JWT::encode($payload, self::$secretKey, 'HS256');
    }

    // Верификация JWT токена
    // public function verifyToken($token) {
    //     try {
    //         // Расшифровка токена и верификация
    //         $decoded = JWT::decode($token, new Key(self::$secretKey, 'HS256'));
    //         return (array) $decoded; // Возвращаем расшифрованные данные токена
    //     } catch (\Exception $e) {
    //         return false; // Если токен невалиден
    //     }
    // }

    // Метод для верификации токена
    public static function verifyToken($token) {
        try {
            // Расшифровка и верификация токена
            $decoded = JWT::decode($token, new Key(self::$secretKey, 'HS256'));

            // Возвращаем расшифрованные данные из токена
            return (array) $decoded; // (array) для того чтобы привести объект в массив
        } catch (\Exception $e) {
            // Если токен невалиден или произошла ошибка при расшифровке, возвращаем false
            return false;
        }
    }

    // Извлечение данных из токена
    public function getDataFromToken($token) {
        $decoded = self::verifyToken($token);
        return $decoded ? $decoded : null;
    }
}
