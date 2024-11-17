<?php 

namespace App\Models;

use PDO;
use App\Database\Database;

class User {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection(); // Используем Database для подключения
    }

    // Метод для поиска пользователя по имени
    public function findByUsername($username) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Метод для создания нового пользователя
    public function create($username, $password) {
        // Хешируем пароль перед сохранением
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $this->pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->execute([$username, $hashedPassword]);
    }

    // Метод для проверки пароля
    public function verifyPassword($password, $storedHash) {
        return password_verify($password, $storedHash);
    }
}
