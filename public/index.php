<?php

// Подключаем автозагрузку Composer

// Подключаем конфигурацию
require_once __DIR__ . '/../config/config.php';

// header("Access-Control-Allow-Origin: *");
// header("Access-Control-Allow-Headers: Authorization");
// header("Content-Type: application/json; charset=UTF-8");

// Создаем соединение с базой данных
session_start(); 

use App\Database\Database;

$pdo = Database::getConnection();

// Инициализируем маршрутизатор
$router = new \App\Router();

// var_dump($_SERVER['REQUEST_URI']);

// Главная страница
$router->add('GET', '/', 'HomeController@index');

// Роуты для аутентификации
$router->add('POST', '/api/auth/login', 'AuthController@login');
$router->add('POST', '/api/auth/register', 'AuthController@register');

// // Роуты для задач (с middleware)
$router->add('GET', '/api/todos', 'TodoController@index', [\App\Middleware\AuthMiddleware::class]);
$router->add('GET', '/api/todos/{id}', 'TodoController@show', [\App\Middleware\AuthMiddleware::class]);
$router->add('POST', '/api/todos', 'TodoController@create', [\App\Middleware\AuthMiddleware::class]);
$router->add('PUT', '/api/todos/{id}', 'TodoController@update', [\App\Middleware\AuthMiddleware::class]);
$router->add('DELETE', '/api/todos/{id}', 'TodoController@delete', [\App\Middleware\AuthMiddleware::class]);


// Запуск маршрутов
$router->run();
