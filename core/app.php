<?php
namespace App\Core;

use PDO;
use PDOException;

final class App
{
    private static ?PDO $pdo = null;

    public static function init(): void
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
                    ]
                );
            } catch (PDOException $e) {
                die("Database connection failed: " . $e->getMessage());
            }
        }
    }

    
    public static function db(): PDO
    {
        if (self::$pdo === null) {
            self::init();
        }

        return self::$pdo;
    }
}
