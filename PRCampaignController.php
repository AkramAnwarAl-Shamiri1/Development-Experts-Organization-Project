<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\PRCampaign;

class PRCampaignController extends BaseController
{

    private PRCampaign $PRCampaignModel;

    public function __construct()
    {
        $this->PRCampaignModel = new PRCampaign();

        // لو ما فيش جلسة مفتوح،نفتح جلسة جديدة
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    //   عرض ي كل الحملات
    public function all()
    {
        $this->authenticateToken(); // أتأكد من المستخدم 
        $PRCampaign = $this->PRCampaignModel->all(); // عرض كل الحملات
        $this->jsonResponse(["success" => true, "PRCampaign" => $PRCampaign]); // أرجعهم كـ JSON
    }

    //     id عرض حملة حسب  
    public function find($id)
    {
        $this->authenticateToken();
        $PRCampaign = $this->PRCampaignModel->find($id); // أجيب الحملة
        $this->jsonResponse(["success" => true, "PRCampaign" => $PRCampaign]); // أرجعها
    }

    //   تخزن حملة جديدة
    public function store() {
        $this->authenticateToken();

        // لو ما فيش صورة مرفوعة
        if (!isset($_FILES['cover'])) {
            $this->jsonResponse(["success" => false, "message" => "لم يتم رفع صورة"]);
            return;
        }

        // أخذ بيانات الصورة
        $file = $_FILES['cover'];
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newName = uniqid(rand(), true) . "." . $ext;
        $target = __DIR__ . "/../../public/uploads/covers/" . $newName;

        // أحاول أرفع الصورة
        if (move_uploaded_file($file['tmp_name'], $target)) {
            // انشاء حملة جديدة وأعبي بياناتها
            $PRCampaign = new PRCampaign();
            $PRCampaign->title = $_POST['title'];
            $PRCampaign->description = $_POST['description'];
            $PRCampaign->cover = "/uploads/covers/" . $newName;
            $PRCampaign->save();

            // لو ما  أرتفعت الصورة أرجع خطأ
            $this->jsonResponse(["success" => true, "PRCampaign" => $PRCampaign]);
        } else {
            $this->jsonResponse(["success" => false, "message" => "فشل في رفع الصورة"]);
        }
    }

    //   نحدث بيانات حملة موجودة
    public function update($id) {
        $this->authenticateToken();
        $PRCampaign = $this->PRCampaignModel->find($id); // أدور على الحملة

        if (!$PRCampaign) {
            // لو الحملة مش موجودة
            $this->jsonResponse(["success" => false, "message" => " غير موجود"]);
            return;
        }

        // بيانات التحديث، لو ما أرسلش المستخدم بيانات بخلي القديمة
        $updatedData = [];
        $updatedData['title'] = $_POST['title'] ?? $PRCampaign['title'];
        $updatedData['description'] = $_POST['description'] ?? $PRCampaign['description'];

        // لو أرسل صورة جديدة
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
        $this->PRCampaignModel->updatePRCampaign($id, $updatedData);

        $this->jsonResponse(["success" => true, "message" => "تم تحديث  "]);
    }

    //   نحذف حملة
    public function delete($id)
    {
        $this->authenticateToken();

        // أدور على الحملة
        $PRCampaign = $this->PRCampaignModel->find($id);
        if (!$PRCampaign) {
            $this->jsonResponse(["success" => false, "message" => "غير موجود"]);
            return;
        }

        // لو للحملة صورة، أحذفها من السيرفر
        if ($PRCampaign->cover) {
            $coverPath = __DIR__ . "/../../public" . $PRCampaign->cover;
            if (file_exists($coverPath)) unlink($coverPath);
        }

        // أحذف الحملة من قاعدة البيانات
        $this->PRCampaignModel->delete($id);
        $this->jsonResponse(["success" => true]);
    }
}
