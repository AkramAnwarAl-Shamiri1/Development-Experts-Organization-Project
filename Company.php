<?php

namespace App\Models;

use App\Core\App;

class Company {
    // اسم جدول  
    private string $table = 'companies'; 

    // خصائص الشراكة
    public int $id;
    public string $name;
    public string $type;
    public string $cover;

    //  عرض كل الشراكات
    public function all(){
        $stmt = App::db()->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll(); 
    }

   //  false لو ما موجود ترجع  id تجيب الشراكة حسب  
    public function find($id){
        $stmt = App::db()->prepare("SELECT * FROM {$this->table} WHERE id=:id");
        $stmt->execute(['id'=>$id]);
        return $stmt->fetch(); 
    }

    //  انشاء شراكة جديدة 
    public function create($data){
        if(isset($data['csrf_token'])) unset($data['csrf_token']); 

        $columns = implode(",", array_keys($data)); 
        $placeholders = ":" . implode(",:", array_keys($data));

        $stmt = App::db()->prepare("INSERT INTO {$this->table} ($columns) VALUES ($placeholders)");
        $stmt->execute($data);

       
        return $this->find(App::db()->lastInsertId());
    }

    //  تحديث بيانات شراكة موجودة
    public function updateCompany($id, $data) {
        $stmt = App::db()->prepare("
            UPDATE {$this->table} 
            SET name = :name, type = :type
            WHERE id = :id
        ");
        $stmt->execute([
            'name' => $data['name'],
            'type' => $data['type'],
            'id' => $id
        ]);

        return $stmt->rowCount(); 
    }

    //  نحذف شركة
    public function delete($id){
        $stmt = App::db()->prepare("DELETE FROM {$this->table} WHERE id=:id");
        return $stmt->execute(['id'=>$id]); 
    }

    //  حفظ الشركة  
    public function save() {
        $stmt = App::db()->prepare("INSERT INTO {$this->table} (name, type, cover) VALUES (?, ?, ?)");
        $stmt->execute([$this->name, $this->type, $this->cover]);
    }

}
