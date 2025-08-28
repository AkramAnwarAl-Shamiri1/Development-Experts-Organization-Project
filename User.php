<?php

namespace App\Models;

use App\Core\App;
use PDO;

class User
{
    //  جدول المستخدمين
    private string $table = 'users'; 

    // إزالة كلمة المرور قبل إرجاع بيانات المستخدم
    private function sanitizeUser(array $user = []): array
    {
        if (isset($user['password'])) {
            unset($user['password']);
        }
        return $user;
    }

    // عرض كل المستخدمين
    public function all(): array
    {
        $stmt = App::db()->query("SELECT * FROM {$this->table}");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map([$this, 'sanitizeUser'], $users); // إزالة كلمات المرور
    }

    // id عرض مستخدم حسب  
    public function find(int $id): ?array
    {
        $stmt = App::db()->prepare("SELECT * FROM {$this->table} WHERE id=:id");
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ? $this->sanitizeUser($user) : null;
        }

    // عرض المستخدم حسب البريد  
    public function findByEmail(string $email): ?array
       {
        $stmt = App::db()->prepare("SELECT * FROM {$this->table} WHERE email=:email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ? $this->sanitizeUser($user) : null;
         }

    // عرض المستخدم حسب البريد مع كلمة المرور
    public function findByEmailWithPassword(string $email): ?array
    {
        $stmt = App::db()->prepare("SELECT * FROM {$this->table} WHERE email=:email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        }

    // إنشاء المستخدم 
    public function create(array $data): array
    {
        if (!isset($data['role'])) $data['role'] = 'user';
        if (isset($data['password'])) $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        $columns = implode(',', array_keys($data));
        $placeholders = ':' . implode(',:', array_keys($data));

        $stmt = App::db()->prepare("INSERT INTO {$this->table} ($columns) VALUES ($placeholders)");
        $stmt->execute($data);

        return $this->find((int) App::db()->lastInsertId());
    }

    // تحديث بيانات المستخدم
    public function update(int $id, array $data): ?array
    {
        if (isset($data['password'])) $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        $fields = '';
        foreach ($data as $key => $value) {
            $fields .= "$key=:$key,";
        }
        $fields = rtrim($fields, ',');
        $data['id'] = $id;

        $stmt = App::db()->prepare("UPDATE {$this->table} SET $fields WHERE id=:id");
        $stmt->execute($data);

        return $this->find($id);
    }

    // حذف المستخدم
    public function delete(int $id): bool
    {
        $stmt = App::db()->prepare("DELETE FROM {$this->table} WHERE id=:id");
        return $stmt->execute(['id' => $id]);
    }
}

