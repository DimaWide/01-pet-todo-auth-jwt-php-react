<?php

namespace App;

use App\Request;

class Router {
    private $routes = [];
    private $middleware = [];

    // Регистрация маршрутов
    public function add($method, $route, $controller, $middleware = []) {
        $this->routes[] = [
            'method' => $method,
            'route' => $route,
            'controller' => $controller,
            'middleware' => $middleware
        ];
    }

    // Запуск маршрутизатора
    public function run() {
        $requestMethod = $_SERVER['REQUEST_METHOD']; // Метод запроса (GET, POST, PUT, DELETE)
        $requestUri = $this->formatRequestUri($_SERVER['REQUEST_URI']); // Отформатированный URI

        // Создаем объект запроса
        $request = new Request();

        foreach ($this->routes as $route) {
            if ($this->matchRoute($requestMethod, $requestUri, $route)) {
                // Применяем middleware
                // Применяем middleware
                foreach ($route['middleware'] as $middleware) {
                    // var_dump($middleware);  // Для дебага
                    // Проверяем, существует ли класс и вызываем его
                    if (class_exists($middleware)) {
                        $response = $middleware::handle($request); // Передаем объект запроса
                        if ($response) {
                            //   echo $response;  // Отправка ответа, если middleware вернул результат
                            return;
                        }
                    } else {
                        echo "Middleware class not found: " . $middleware;
                        return;
                    }
                }
                // Вызов контроллера и метода
                list($controller, $method) = explode('@', $route['controller']);
                $controller = "App\\Controllers\\$controller";
                $controllerInstance = new $controller;
                echo $controllerInstance->$method($request);
                return;
            }
        }

        // Если маршрут не найден, возвращаем ошибку 404
        http_response_code(404);
        echo 'Route not found';
    }

    // Сопоставление маршрута с запросом
    private function matchRoute($requestMethod, $requestUri, $route) {
        $routePattern = $route['route'];
        // Преобразуем маршруты с параметрами в регулярные выражения
        $routePattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $routePattern);
        $routePattern = '#^' . $routePattern . '$#';

        // Сравниваем метод и URI запроса с маршрутом
        return ($requestMethod === $route['method'] && preg_match($routePattern, $requestUri));
    }

    // Метод для форматирования URI
    private function formatRequestUri($uri) {
        // Убираем префикс "/dev_02.todo-php-rest-crud-1/public" или любой другой, если необходимо
        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = str_replace('/dev_02.todo-php-rest-crud-1/public', '', $uri); // Заменить на свой путь
        $uri = str_replace('/dev_02.todo-php-rest-crud-1', '', $uri); // Замените на нужный путь
        return $uri;
    }
}
