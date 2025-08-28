<?php

namespace App\Controllers;

use App\Core\BaseController; 
use App\Models\Activity; 

class ActivityController extends BaseController
{
    private Activity $activityModel;

    public function __construct()
    {
    
        $this->activityModel = new Activity();

       // لو ما فيش جلسة مفتوحة، نفتح جلسة جديدة
        if(session_status() === PHP_SESSION_NONE){
            session_start();
        }
    }

    // هعرض الانشطةذ
    public function all() {
        try {
            // أتأكد أن المستخدم معه صلاحية من خلال التوكن
            $this->authenticateToken();

            // أجيب كل الأنشطة
            $activities = $this->activityModel->all();

            // أرجع البيانات كـ JSON ومعها حالة النجاح
            $this->jsonResponse(["success" => true, "activities" => $activities]);
        } catch(\Throwable $e) {
            // لو حصل أي خطأ، أتعامل معه عبر  handleError
            $this->handleError($e);
        }
    }

    // id  عرض نشاط  بحسب 
    public function find($id) {
        try {
            $this->authenticateToken(); 
            $activity = $this->activityModel->find($id); 
            // هنا بأجيب النشاط من الموديل

            $this->jsonResponse(["success" => true, "activity" => $activity]);
            // أرجع النشاط كـ JSON
        } catch(\Throwable $e) {
            $this->handleError($e);
        }
    }

    //   تخزن نشاط جديد
    public function store() {
        $this->authenticateToken(); 
       //نتحقق من المستخدم

        // لو المستخدم ما رفع صورة، أرجع رسالة خطأ
        if (!isset($_FILES['cover'])) {
            $this->jsonResponse(["success" => false, "message" => "لم يتم رفع صورة"]);
            return;
        }

        // هنا بأخذ بيانات الصورة اللي رفعها
        $file = $_FILES['cover'];

        //  الامتداد حق الصورة 
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);

        //  اسم جديد عشوائي للصورة
        $newName = uniqid(rand(), true) . "." . $ext;

        // أحدد مكان التخزين حق الصورة في السيرفر
        $target = __DIR__ . "/../../public/uploads/covers/" . $newName;

        // لو الصورة اترفعت بنجاح
        if (move_uploaded_file($file['tmp_name'], $target)) {
           // أنشئ نشاط جديد وأخزن البيانات
           $activity = new Activity();
           $activity->title = $_POST['title'];
           $activity->description = $_POST['description'];
           $activity->cover = "/uploads/covers/" . $newName;
           $activity->save();

           // أرجع النشاط اللي انحفظ
            $this->jsonResponse(["success" => true, "activity" => $activity]);
        } else {
            // لو ما  أرتفعت الصورة أرجع خطأ
            $this->jsonResponse(["success" => false, "message" => "فشل في رفع الصورة"]);
        }
    }

    //   نحدث نشاط 
    public function update($id) {
        try {
            $this->authenticateToken();
            // أدور النشاط أول
            $activity = $this->activityModel->find($id);

            // لو النشاط مش موجود
            if (!$activity) {
                $this->jsonResponse(["success" => false, "message" => "النشاط غير موجود"]);
                return;
            }

            //  بيانات التحديث
            $updatedData = [];
            $updatedData['title'] = $_POST['title'] ?? $activity['title'];
            $updatedData['description'] = $_POST['description'] ?? $activity['description'];

            // لو المستخدم رفع صورة جديدة
            if (isset($_FILES['cover']) && $_FILES['cover']['error'] === 0) {
                $file = $_FILES['cover'];
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $newName = uniqid(rand(), true) . "." . $ext;
                $target = __DIR__ . "/../../public/uploads/covers/" . $newName;

                if (move_uploaded_file($file['tmp_name'], $target)) {
                    // أغير رابط الصورة للملف الجديد
                    $updatedData['cover'] = "/uploads/covers/" . $newName;
                }
            }

            // أرسل البيانات للموديل علشان يحدث النشاط
            $this->activityModel->updateActivity($id, $updatedData);

            $this->jsonResponse(["success" => true, "message" => "تم تحديث النشاط"]);
        } catch(\Throwable $e) {
            $this->handleError($e);
        }
    }

    //   حذف النشاط
    public function delete($id) {
        try {
            $this->authenticateToken();
            $activity = $this->activityModel->find($id);

            // لو النشاط مش موجود
            if (!$activity) {
                $this->jsonResponse(["success" => false, "message" => "النشاط غير موجود"]);
                return;
            }

            // لو موجود نحذفه
            $this->activityModel->delete($id);
            $this->jsonResponse(["success" => true]);
        } catch(\Throwable $e) {
            $this->handleError($e);
        }
    }
}
