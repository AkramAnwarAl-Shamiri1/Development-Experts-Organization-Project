<?php
namespace App\Controllers;

use App\Core\BaseController;
use App\Models\Company;

class CompanieController extends BaseController
{

    private Company $companieModel;

    public function __construct()
    {
      
        $this->companieModel = new Company();

        // لو ما فيش جلسة مفتوحة نفتح جلسة جديدة
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    //    عرض كل الشراكات 
    public function all()
    {
        $this->authenticateToken(); // أتأكد من المستخدم 
        $companies = $this->companieModel->all(); // أجيب كل الشركات
        $this->jsonResponse(["success" => true, "companies" => $companies]); // أرجعهم كـ JSON
    }

    //   id تجيب شراكة بحسب  
    public function find($id)
    {
        $this->authenticateToken(); 
        $company = $this->companieModel->find($id); // أجيب الشركة
        $this->jsonResponse(["success" => true, "company" => $company]); // أرجعها
    }

    //   تخزن شركة جديدة
    public function store() {
        $this->authenticateToken();

        // بخزن هنا اسم الصورة الجديد لو المستخدم رفع صورة
        $newName = null;
        if (isset($_FILES['cover']) && $_FILES['cover']['error'] === 0) {
            // أخذ بيانات الصورة
            $file = $_FILES['cover'];
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $newName = uniqid(rand(), true) . "." . $ext;
            $target = __DIR__ . "/../../public/uploads/covers/" . $newName;

            // لو ما  أرتفعت الصورة أرجع خطأ
            if (!move_uploaded_file($file['tmp_name'], $target)) {
                $this->jsonResponse(["success" => false, "message" => "فشل في رفع الصورة"]);
                return;
            }
        }

        // انشاء شراكة جديدة  
        $company = new Company();
        $company->name = $_POST['name'] ?? '';
        $company->type = $_POST['type'] ?? '';
        $company->cover = $newName ? "/uploads/covers/" . $newName : null;
        $company->save();

        // أرجع الشركة اللي انحفظت
        $this->jsonResponse(["success" => true, "company" => $company]);
    }

    //   تحدث  شراكة
    public function update($id) {
        $this->authenticateToken();
        $Company = $this->companieModel->find($id); // أدور على الشراكة

        if (!$Company) {
            // لو الشركة مش موجودة
            $this->jsonResponse(["success" => false, "message" => " غير موجود"]);
            return;
        }

        // بيانات التحديث، لو ما أرسلش المستخدم بيانات بخلي القديمة
        $updatedData = [];
        $updatedData['name'] = $_POST['name'] ?? $Company['name'];
        $updatedData['type'] = $_POST['type'] ?? $Company['type'];

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
        $this->companieModel->updateCompany($id, $updatedData);

        $this->jsonResponse(["success" => true, "message" => "تم تحديث  "]);
    }

    //   تحذف الشراكة
    public function delete($id)
    {
        $this->authenticateToken();

        // أدور على الشركة
        $company = $this->companieModel->find($id);
        if (!$company) {
            $this->jsonResponse(["success" => false, "message" => "غير موجود"]);
            return;
        }

        // لو للشركة صورة، أحذفها من السيرفر
        if ($company->cover) {
            $coverPath = __DIR__ . "/../../public" . $company->cover;
            if (file_exists($coverPath)) unlink($coverPath);
        }

        // أحذف الشراكة من قاعدة البيانات
        $this->companieModel->delete($id);
        $this->jsonResponse(["success" => true]);
    }
}
