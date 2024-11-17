<?php
namespace App\Controllers;

use App\Models\Todo;  // Подключаем модель Todo

class TodoController {
    private $todoModel;

    public function __construct() {
        // Создаем экземпляр модели внутри контроллера
        $this->todoModel = new Todo();
    }

    public function getAllTodos($user_id) {
        $todos = $this->todoModel->getAll($user_id);
        echo json_encode($todos);
    }

    public function getTodoById($id, $user_id) {
        $todo = $this->todoModel->getById($id, $user_id);
        if ($todo) {
            echo json_encode($todo);
        } else {
            $this->sendError("Task with ID $id not found.", 404);
        }
    }

    public function createTodo() {
        $user = $this->getUserFromToken();
        if (!$user) {
            $this->sendError("Unauthorized", 401);
        }

        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['title'])) {
            $this->sendError("Title field is required.", 400);
        }

        $id = $this->todoModel->create($data, $user->user_id);
        echo json_encode(['id' => $id] + $data);
    }

    public function updateTodoById($id, $data) {
        $user = $this->getUserFromToken();
        if (!$user) {
            $this->sendError("Unauthorized", 401);
        }

        if (empty($data)) {
            $this->sendError("No data provided for update.", 400);
        }

        $updated = $this->todoModel->update($id, $data, $user->user_id);
        if ($updated) {
            echo json_encode(["message" => "Task updated successfully"]);
        } else {
            $this->sendError("Task with ID $id not found.", 404);
        }
    }

    public function deleteTodoById($id) {
        $user = $this->getUserFromToken();
        if (!$user) {
            $this->sendError("Unauthorized", 401);
        }

        $deleted = $this->todoModel->delete($id, $user->user_id);
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
        return validate_token(str_replace('Bearer ', '', $token));
    }
}