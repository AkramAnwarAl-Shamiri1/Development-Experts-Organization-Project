<?php
namespace App\Models;

use App\Core\App;

class Program {
    //  جدول البرامج
    private string $table = 'programs'; 

    public string $name = '';
    public string $Description = '';
    public string $icon = '';

    // عرض كل البرامج
    public function all(){
        $stmt = App::db()->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll();
    }

    //  false لو ما موجود ترجع  id تجيب البرنامج حسب  
    public function find($id){
        $stmt = App::db()->prepare("SELECT * FROM {$this->table} WHERE id=:id");
        $stmt->execute(['id'=>$id]);
        return $stmt->fetch(); 
    }

    // إنشاء برنامج جديد من مصفوفة البيانات
    public function create($data){
        if(isset($data['csrf_token'])) unset($data['csrf_token']); 
        $columns = implode(",", array_keys($data));
        $placeholders = ":" . implode(",:", array_keys($data));
        $stmt = App::db()->prepare("INSERT INTO {$this->table} ($columns) VALUES ($placeholders)");
        $stmt->execute($data);
        return $this->find(App::db()->lastInsertId()); 
    }

    // تحديث البرنامج 
    public function updateProgram($id, $data){
        $stmt = App::db()->prepare("
            UPDATE {$this->table} 
            SET name = :name, Description = :Description, icon = :icon
            WHERE id = :id
        ");
        $stmt->execute([
            'name' => $data['name'],
            'Description' => $data['Description'],
            'icon' => $data['icon'],
            'id' => $id
        ]);
        return $stmt->rowCount(); 
    }

    // حذف البرنامج
    public function delete($id){
        $stmt = App::db()->prepare("DELETE FROM {$this->table} WHERE id=:id");
        return $stmt->execute(['id'=>$id]);
    }

    // حفظ البرنامج  
     public function save() {
        $stmt = App::db()->prepare("INSERT INTO {$this->table} (name, Description, icon) VALUES (?, ?, ?)");
        $stmt->execute([$this->name, $this->Description, $this->icon]);
    }
}
