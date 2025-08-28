<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\Program;

class ProgramController extends BaseController
{
   
    private Program $programModel;

    public function __construct()
    {
      
        $this->programModel = new Program();

        // لو ما فيش جلسة مفتوحة، نفتح جلسة جديدة
        if(session_status() === PHP_SESSION_NONE){
            session_start();
        }
    }

    //   عرض كل البرامج
    public function all() {
        $this->authenticateToken(); // أتأكد من المستخدم 
        $programs = $this->programModel->all(); // أجيب كل البرامج
        $this->jsonResponse(["success" => true, "programs" => $programs]); // أرجعهم JSON
    }

    // id  عرض برنامج  بحسب   
    public function find($id) {
        $this->authenticateToken();
        $program = $this->programModel->find($id); // أجيب البرنامج
        $this->jsonResponse(["success" => true, "program" => $program]); // أرجعه JSON
    }

    //   تخزن برنامج جديد
    public function store() {
        $this->authenticateToken();

        // نجيب البيانات من body request بصيغة JSON
        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        // انشاء برنامج جديد واكتب بياناته
        $program = new Program();
        $program->name = $data['name'] ?? '$$';
        $program->Description = $data['Description'] ?? "re";
        $program->icon = $data['icon'] ?? 'AcademicCapIcon';
        $program->save();

        // أرجع البرنامج اللي انحفظ
        $this->jsonResponse(["success" => true, "program" => $program]);
    }

    //   نتحث برنامج 
    public function update($id) {
        $this->authenticateToken();
        $program = $this->programModel->find($id); 

        if (!$program) {
            // لو البرنامج مش موجود
            $this->jsonResponse(["success" => false, "message" => " غير موجود"]);
            return;
        }

        // بيانات التحديث، لو ما أرسلش المستخدم بيانات بخلي القديمة
        $updatedData = [];
        $updatedData['name'] = $_POST['name'] ?? $program['name'];
        $updatedData['Description'] = $_POST['Description'] ?? $program['Description'];
        $updatedData['icon'] = $_POST['icon'] ?? $program['icon'];

        // لو المستخدم رفع صورة جديدة
        if (isset($_FILES['cover']) && $_FILES['cover']['error'] === 0) {
            $file = $_FILES['cover'];
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $newName = uniqid(rand(), true) . "." . $ext;
            $target = __DIR__ . "/../../public/uploads/covers/" . $newName;

            if (move_uploaded_file($file['tmp_name'], $target)) {
                $updatedData['cover'] = "/uploads/covers/" . $newName;
            }
        }

        // أرسل التحديثات للموديل علشان يحفظها
        $this->programModel->updateProgram($id, $updatedData);
        $this->jsonResponse(["success" => true, "message" => "تم تحديث  "]);
    }

    
    public function delete($id) {
        $this->authenticateToken();
        $program = $this->programModel->find($id); 

        if (!$program) {
          
            $this->jsonResponse(["success" => false, "message" => " غير موجود"]);
            return;
        }

        // حذف البرنامج   
        $this->programModel->delete($id);
        $this->jsonResponse(["success" => true]);
    }
}
