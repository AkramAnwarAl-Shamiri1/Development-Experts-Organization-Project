<?php

namespace App\Models;

use App\Core\App;

class Activity {
    //اسم جدول 
     private string $table = 'activities'; 

    // خصائص النشاط
    public int $id;
    public string $title;
    public string $description;
    public ?string $cover = null; 

    //  عرض كل الأنشطة
    public function all(): array {
        $stmt = App::db()->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    //  null لو ما موجود ترجع  id تجيب النشاط حسب  
    public function find(int $id): ?array {
        $stmt = App::db()->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $activity = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $activity ?: null; 
    }

    //  انشاء نشاط  
    public function create(array $data): array {
        if(isset($data['csrf_token'])) unset($data['csrf_token']); 

        $columns = implode(",", array_keys($data));
        $placeholders = ":" . implode(",:", array_keys($data)); 

        $stmt = App::db()->prepare("INSERT INTO {$this->table} ($columns) VALUES ($placeholders)");
        $stmt->execute($data);

        return $this->find((int) App::db()->lastInsertId());
    }

    // تحدث نشاط 
    public function updateActivity(int $id, array $data): int {
        $fields = "title=:title, description=:description"; 
        if(isset($data['cover'])){
            $fields .= ", cover=:cover"; 
        }

        $stmt = App::db()->prepare("UPDATE {$this->table} SET $fields WHERE id=:id");
        $params = [
            'title' => $data['title'],
            'description' => $data['description'],
            'id' => $id
        ];
        if(isset($data['cover'])){
            $params['cover'] = $data['cover'];
        }

        $stmt->execute($params);
        return $stmt->rowCount(); 
    }

    //  نحذف نشاط
    public function delete(int $id): bool {
        $stmt = App::db()->prepare("DELETE FROM {$this->table} WHERE id=:id");
        return $stmt->execute(['id'=>$id]);
    }

    //حفظ النشاط  
    public function save() {
        $stmt = App::db()->prepare("INSERT INTO {$this->table} (title, description, cover) VALUES (?, ?, ?)");
        $stmt->execute([$this->title, $this->description, $this->cover]);
    }
}
