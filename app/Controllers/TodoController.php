<?php

namespace App\Controllers;

use App\Services\JwtService;

use App\Models\Todo;  // Подключаем модель Todo

class TodoController {
    private $todoModel;

    public function __construct() {
        // Создаем экземпляр модели внутри контроллера
        $this->todoModel = new Todo();
    }

    // Получение всех задач
    public function index($user_id) {
        $user_id = $this->getUserFromToken();
        $todos = $this->todoModel->getAll($user_id);
        echo json_encode($todos);
    }

    // Получение задачи по ID
    public function show($id, $user_id) {
        $todo = $this->todoModel->getById($id, $user_id);
        if ($todo) {
            echo json_encode($todo);
        } else {
            $this->sendError("Task with ID $id not found.", 404);
        }
    }

    public function create() {
        $data = json_decode(file_get_contents("php://input"), true);

        // Проверка на ошибки при декодировании JSON
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->sendError("Invalid JSON provided.", 400);
        }

        // Проверка наличия обязательного поля 'title'
        if (!isset($data['title'])) {
            $this->sendError("Title field is required.", 400);
        }

        // Получаем user_id из токена
        $user_id = $this->getUserFromToken();

        if (!$user_id) {
            $this->sendError("Unauthorized", 401);
        }

        // Извлекаем необходимые данные из массива $data
        $title = $data['title'];
        // Преобразуем значение completed в 0 или 1
        $completed = isset($data['completed']) ? (int)$data['completed'] : 0;  // Если не указан, считаем, что задача не завершена

        // Создаем новую задачу, передавая правильные параметры
        $id = $this->todoModel->create($title, $completed, $user_id);
        echo json_encode(['id' => $id] + $data);
    }


    // Обновление задачи по ID
    public function update($data) {
        $id = $this->getTodoId();

        $data = json_decode(file_get_contents("php://input"), true);

        $user_id = $this->getUserFromToken();
        if (!$user_id) {
            $this->sendError("Unauthorized", 401);
        }

        if (empty($data)) {
            $this->sendError("No data provided for update.", 400);
        }

        // Проверяем и извлекаем данные из массива $data
        $title = isset($data['title']) ? $data['title'] : null;
        $completed = isset($data['completed']) ? (int)$data['completed'] : 0;  // Если не указан, считаем, что задача не завершена

        // Убедимся, что хотя бы одно из полей (title или completed) передано
        if (is_null($title) && is_null($completed)) {
            $this->sendError("At least one of 'title' or 'completed' must be provided for update.", 400);
        }

        // Обновление задачи в базе данных
        $updated = $this->todoModel->update($id, $title, $completed, $user_id);
        if ($updated) {
            echo json_encode(["message" => "Task updated successfully"]);
        } else {
            $this->sendError("Task with ID $id not found.", 404);
        }
    }

    // Удаление задачи по ID
    public function delete() {
        $id = $this->getTodoId();
        $user_id = $this->getUserFromToken();

        if (!$user_id) {
            $this->sendError("Unauthorized", 401);
        }

        $deleted = $this->todoModel->delete($id, $user_id);

        if ($deleted) {
            echo json_encode(["message" => "Task deleted successfully"]);
        } else {
            $this->sendError("Task with ID $id not found.", 404);
        }
    }

    private function sendError($message, $statusCode) {
        http_response_code($statusCode);
        echo json_encode(["error" => $message]);
        exit();
    }

    private function getUserFromToken() {
        $headers = apache_request_headers();
        $token = $headers['Authorization'] ?? '';
        $token = str_replace('Bearer ', '', $token);
        $userData = JwtService::verifyToken($token);
        return $userData['user_id'];
    }

    private function getTodoId() {
        $request_uri = $_SERVER['REQUEST_URI'];
        preg_match('/\/todos\/(\d+)/', $request_uri, $matches);
        $id = null;

        if (isset($matches[1])) {
            $id = (int)$matches[1];
        }

        return $id;
    }
}
