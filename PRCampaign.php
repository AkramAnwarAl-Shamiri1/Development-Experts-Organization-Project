<?php

namespace App\Models;

use App\Core\App;

class PRCampaign {
    // اسم الجدول
    private string $table = 'prcampaigns'; 

    // خصائص الحملة
    public int $id;
    public string $title;
    public string $description;
    public string $cover;

    //  عرض كل الحملات
    public function all(){
        $stmt = App::db()->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll(); 
    }

     //false لو ما موجود ترجع  id نجيب الحملات التبرع  حسب 
    public function find($id){
        $stmt = App::db()->prepare("SELECT * FROM {$this->table} WHERE id=:id");
        $stmt->execute(['id'=>$id]);
        return $stmt->fetch(); 
    }

    //  انشاء  حملة تبرع    
    public function create($data){
        if(isset($data['csrf_token'])) unset($data['csrf_token']); 

        $columns = implode(",", array_keys($data));
        $placeholders = ":" . implode(",:", array_keys($data));

        $stmt = App::db()->prepare("INSERT INTO {$this->table} ($columns) VALUES ($placeholders)");
        $stmt->execute($data);

        
        return $this->find(App::db()->lastInsertId());
    }

    //   تحديث بيانات حملة التبرع
    public function update($id, $data) {
        $stmt = App::db()->prepare("
            UPDATE {$this->table} 
            SET title = :title, description = :description
            WHERE id = :id
        ");
        $stmt->execute([
            'title' => $data['title'],
            'description' => $data['description'],
            'id' => $id
        ]);

        return $stmt->rowCount(); 
    }

    //     true نحذف حملة التبرع اذا تم يرجع 
    public function delete($id){
        $stmt = App::db()->prepare("DELETE FROM {$this->table} WHERE id=:id");
        return $stmt->execute(['id'=>$id]); 
    }

    //  حفظ حملات التبرع 
    public function save() {
        $stmt = App::db()->prepare("INSERT INTO {$this->table} (title, description, cover) VALUES (?, ?, ?)");
        $stmt->execute([$this->title, $this->description, $this->cover]);
    }

}
