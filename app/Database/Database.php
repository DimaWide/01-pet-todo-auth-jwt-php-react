<?php

namespace App\Database;

use PDO;
use PDOException;

class Database {
    private static $connection;

    public static function getConnection($config = null) {
        if (self::$connection === null) {
            $configPath = __DIR__ . '/../../config/config_db.php';
            if (file_exists($configPath)) {
                $config = require_once $configPath;
            } else {
                die('Конфигурационный файл не найден!');
            }

            try {
                $dsn = "mysql:host={$config['DB_HOST']};dbname={$config['DB_NAME']};port={$config['DB_PORT']}";
                self::$connection = new PDO($dsn, $config['DB_USER'], $config['DB_PASSWORD']);
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Database connection failed: " . $e->getMessage());
            }
        }

        return self::$connection;
    }
}
