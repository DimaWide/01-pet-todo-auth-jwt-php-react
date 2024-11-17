<?php 

namespace App\Controllers;

class HomeController
{
    public function index()
    {
        // Возвращаем HTML для главной страницы
        include_once __DIR__ . '/../Views/home.php';
    }
}