<?php

namespace App\Core;

use PDO;
use PDOException;

final class App
{
    private static ?PDO $pdo = null;

    public static function db(): PDO
    {
        if (self::$pdo === null) {
            try {
                self::$pdo = new PDO(
                    'mysql:host=localhost;dbname=users_db;charset=utf8mb4',
                    'root',
                    '',
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]
                );
            } catch (PDOException $e) {
                error_log('DB connection failed: ' . $e->getMessage());
                die('Database connection failed');
            }
        }

        return self::$pdo;
    }
}
