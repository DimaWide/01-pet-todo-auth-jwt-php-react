<?php

namespace App\Models;

use PDO;
use App\Database\Database;

class Todo
{
    private PDO $pdo;

    // Конструктор класса Todo, который использует статическое подключение
    public function __construct()
    {
        // Получаем подключение из Database::getConnection()
        $this->pdo = Database::getConnection();
    }

    // Получить все задачи для конкретного пользователя
    public function getAll(int $userId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM todos WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Получить задачу по id для конкретного пользователя
    public function getById(int $id, int $userId): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM todos WHERE id = :id AND user_id = :user_id");
        $stmt->execute(['id' => $id, 'user_id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // Создать новую задачу
    public function create(string $title, bool $completed, int $userId): int
    {
        $stmt = $this->pdo->prepare("INSERT INTO todos (title, completed, user_id) VALUES (:title, :completed, :user_id)");
        $stmt->execute([
            'title' => $title,
            'completed' => $completed,
            'user_id' => $userId
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    // Обновить задачу
    public function update(int $id, string $title, bool $completed, int $userId): int
    {
        $stmt = $this->pdo->prepare("UPDATE todos SET title = :title, completed = :completed WHERE id = :id AND user_id = :user_id");
        $stmt->execute([
            'id' => $id,
            'title' => $title,
            'completed' => $completed,
            'user_id' => $userId
        ]);
        return $stmt->rowCount();
    }

    // Удалить задачу
    public function delete(int $id, int $userId): int
    {
        $stmt = $this->pdo->prepare("DELETE FROM todos WHERE id = :id AND user_id = :user_id");
        $stmt->execute(['id' => $id, 'user_id' => $userId]);
        return $stmt->rowCount();
    }
}
?>
