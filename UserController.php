<?php

namespace App\Controllers;

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Core\BaseController;
use App\Models\User;
use Firebase\JWT\JWT;

class UserController extends BaseController
{
    // هنا بخزن نسخة من موديل User علشان أتعامل مع قاعدة البيانات
    private User $userModel;

    public function __construct()
    {
        parent::__construct();
       
        $this->userModel = new User();
    }

    //   ابتحقق من صحة الايميل
    private function validateEmail(string $email): bool
    {
        return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    //   تتحقق من قوة كلمة المرور (٨ )
    private function validatePasswordStrength(string $pw): bool
    {
        return mb_strlen($pw) >= 8;
    }

    //  تسجيل الدخول
    public function login(): void
    {
        try {
            
            $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;


            if ($_SESSION['login_attempts'] > 20) {
                $this->jsonResponse(
                    ['success' => false, 'message' => 'محاولات الدخول كثيرة'],
                    429
                );
            }

            // نجيب البيانات من body JSON
            $data = json_decode(file_get_contents('php://input'), true) ?? [];
            $this->validateCsrf($data['csrf_token'] ?? '');

            $email = trim($data['email'] ?? '');
            $password = (string) ($data['password'] ?? '');

            // تحقق أن الحقول مش فاضية
            if ($email === '' || $password === '') {
                $this->jsonResponse(
                    ['success' => false, 'message' => 'الرجاء إدخال البريد وكلمة المرور'],
                    400
                );
            }

            // تحقق من صحة صيغة الايميل
            if (!$this->validateEmail($email)) {
                $this->jsonResponse(
                    ['success' => false, 'message' => 'صيغة البريد مش صحيحة'],
                    400
                );
            }

            // أبحث عن المستخدم في قاعدة البيانات
            $user = $this->userModel->findByEmailWithPassword($email);

            // تحقق كلمة المرور
            if (!$user || !password_verify($password, $user['password'])) {
                $this->jsonResponse(
                    ['success' => false, 'message' => 'البريد أو كلمة المرور خطأ'],
                    401
                );
            }

            // أشيل كلمة المرور قبل إرسال البيانات
            unset($user['password']);

            // أجهز بيانات التوكن JWT
            $payload = [
                'iat'     => time(),
                'exp'     => time() + $this->tokenExpire,
                'user_id' => $user['id'],
                'role'    => $user['role'],
                'email'   => $user['email'],
            ];

            // انشاء JWT
            $jwt = JWT::encode($payload, $this->secretKey, 'HS256');

            //أخزن التوكن في كوكيز
            setcookie(
                'token',
                $jwt,
                [
                    'expires'  => time() + $this->tokenExpire,
                    'path'     => '/',
                    'secure'   => false,
                    'httponly' => true,
                    'samesite' => 'Strict',
                ]
            );

            // أعيد عدد محاولات الدخول للصفر
            $_SESSION['login_attempts'] = 0;
            $redirect = $user['role'] === 'admin' ? '/dashboard' : '/home';

            // أرجع التوكن ومسار التحويل
            $this->jsonResponse(['success' => true, 'token' => $jwt, 'redirect' => $redirect]);
        } catch (\Throwable $e) {
            $this->handleError($e);
        }
    }

    //  تسجيل مستخدم جديد
    public function register(): void
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true) ?? [];
            $this->validateCsrf($data['csrf_token'] ?? '');

            $name     = trim($data['name'] ?? '');
            $email    = trim($data['email'] ?? '');
            $password = $data['password'] ?? '';

            // تحقق أن الحقول كلها مش فاضية
            if (empty($name) || empty($email) || empty($password)) {
                $this->jsonResponse(
                    ['success' => false, 'message' => 'كل الحقول مطلوبة'],
                    400
                );
            }

            // تحقق من صحة الايميل وكلمة المرور
            if (!$this->validateEmail($email)) {
                $this->jsonResponse(['success' => false, 'message' => 'الايميل خطأ'], 400);
            }

            if (!$this->validatePasswordStrength($password)) {
                $this->jsonResponse(['success' => false, 'message' => 'كلمة المرور ضعيفة'], 400);
            }

            // تحقق أن المستخدم مش موجود مسبقاً
            if ($this->userModel->findByEmailWithPassword($email)) {
                $this->jsonResponse(['success' => false, 'message' => 'المستخدم موجود'], 409);
            }

            // نشفر كلمة المرور قبل الحفظ
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
            $user = $this->userModel->create($data);
            unset($user['password']);

            $this->jsonResponse(['success' => true, 'user' => $user], 201);
        } catch (\Throwable $e) {
            $this->handleError($e);
        }
    }

    //  تسجيل الخروج
    public function logout(): void
    {
        parent::logout();
    }

    //  عرض كل المستخدمين 
    public function allUsers(): void
    {
        try {
            $this->requireRole('admin'); 
            $users = $this->userModel->all();
            $this->jsonResponse(['success' => true, 'users' => $users]);
        } catch (\Throwable $e) {
            $this->handleError($e);
        }
    }

    //  عرض مستخدم واحد 
    public function find(int $id): void
    {
        try {
            $this->requireRole('admin');
            $user = $this->userModel->find($id);

            if (!$user) {
                $this->jsonResponse(['success' => false, 'message' => 'المستخدم مش موجود'], 404);
            }

            $this->jsonResponse(['success' => true, 'user' => $user]);
        } catch (\Throwable $e) {
            $this->handleError($e);
        }
    }

    //  إنشاء مستخدم 
    public function store(): void
    {
        try {
            $this->requireRole('admin');
            $data = json_decode(file_get_contents('php://input'), true) ?? [];
            $this->validateCsrf($data['csrf_token'] ?? '');

           
            if (isset($data['password'])) {
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }

            $user = $this->userModel->create($data);
            $this->jsonResponse(['success' => true, 'user' => $user], 201);
        } catch (\Throwable $e) {
            $this->handleError($e);
        }
    }

    //  تحديث مستخدم 
    public function update(int $id): void
    {
        try {
            $this->requireRole('admin');
            $data = json_decode(file_get_contents('php://input'), true) ?? [];
            $this->validateCsrf($data['csrf_token'] ?? '');

            if (isset($data['password'])) {
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }

            $updated = $this->userModel->update($id, $data);
            $this->jsonResponse(['success' => true, 'user' => $updated]);
        } catch (\Throwable $e) {
            $this->handleError($e);
        }
    }

    //  نحذف مستخدم 
    public function delete(int $id): void
    {
        try {
            $this->requireRole('admin');
            $data = json_decode(file_get_contents('php://input'), true) ?? [];
            $this->validateCsrf($data['csrf_token'] ?? '');

            $deleted = $this->userModel->delete($id);

            if (!$deleted) {
                $this->jsonResponse(['success' => false, 'message' => 'المستخدم مش موجود'], 404);
            }

            $this->jsonResponse(['success' => true, 'deleted' => $deleted]);
        } catch (\Throwable $e) {
            $this->handleError($e);
        }
    }
}
