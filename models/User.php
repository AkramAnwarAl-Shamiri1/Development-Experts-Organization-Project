<?php
namespace App\Models;
use App\Core\App;

class User
{
    public static function findByEmail(string $email): ?array
    {
        $stmt = App::db()->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    public static function getAll(): array
    {
        $stmt = App::db()->query("SELECT * FROM users");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
