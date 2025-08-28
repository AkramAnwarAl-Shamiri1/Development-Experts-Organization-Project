<?php

namespace App\Core;

require_once __DIR__ . '/../../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class BaseController
{
    // مفتاح  لتشفير وفك تشفير التوكن
    protected string $secretKey = 'dfklnbvnhjkjkfge2w3477huH=p[JGH{&k44555egjrigh$#$fdjvmfgbighcifjitcbhgbh';
    //  صلاحية التوكن بالثواني
    protected int $tokenExpire = 3600;

    public function __construct()
    {
        // لو ما فيش جلسة شغالة، نبدأ جلسة جديدة
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // نسجل آخر نشاط للمستخدم
        $_SESSION['last_activity'] = $_SESSION['last_activity'] ?? time();

        // إذا مر 30 دقيقة بدون نشاط، نسجل خروج تلقائي
        if (time() - $_SESSION['last_activity'] > 60 * 30) {
            $this->logoutSilently();
        }

        // تحديث وقت النشاط الأخير
        $_SESSION['last_activity'] = time();
    }

    // دالة ترجع JSON response
    protected function jsonResponse(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // توليد CSRF token وحفظه في السيشن
    protected function generateCsrf(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    // التحقق من CSRF token
    protected function validateCsrf(string $token): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            if (empty($token) || $token !== ($_SESSION['csrf_token'] ?? '')) {
                $this->jsonResponse(['success' => false, 'message' => 'CSRF validation failed'], 403);
            }
        }
    }

    // نرجع CSRF token للفرونتند
    public function csrfToken(): void
    {
        $token = $this->generateCsrf();
        $this->jsonResponse(['csrf_token' => $token]);
    }

    // نجيب التوكن من الهيدر أو الكوكيز
    protected function getTokenFromRequest(): ?string
    {
        $auth = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['Authorization'] ?? null;

        if ($auth && preg_match('/Bearer\s+(.+)/i', $auth, $m)) {
            return trim($m[1]);
        }

        if (!empty($_COOKIE['token'])) {
            return $_COOKIE['token'];
        }

        return null;
    }

    // تحقق من التوكن وأرجع البيانات
    public function authenticate(): void
    {
        $token = $this->getTokenFromRequest();

        if (!$token) {
            $this->jsonResponse(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));

            if ($decoded->exp < time()) {
                $this->jsonResponse(['success' => false, 'message' => 'Token expired'], 401);
            }

            $this->jsonResponse([
                'success' => true,
                'user_id' => $decoded->user_id,
                'role'    => $decoded->role,
                'email'   => $decoded->email,
                'exp'     => $decoded->exp,
            ]);
        } catch (\Throwable $e) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid token'], 401);
        }
    }

    // تحقق من التوكن ورجّع بياناته بدون عمل JSON response
    public function authenticateToken(): object
    {
        $token = $this->getTokenFromRequest();

        if (!$token) {
            throw new \Exception('Unauthorized', 401);
        }

        $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));

        if ($decoded->exp < time()) {
            throw new \Exception('Token expired', 401);
        }

        return $decoded;
    }

    // التحقق من صلاحيات الدور
    protected function requireRole(string ...$roles): object
    {
        $decoded = $this->authenticateToken();

        if (!in_array($decoded->role, $roles, true)) {
            $this->jsonResponse(['success' => false, 'message' => 'Forbidden'], 403);
        }

        return $decoded;
    }

    // إدارة الأخطاء وعرض رسالة عامة للمستخدم
    protected function handleError(\Throwable $e): void
    {
        error_log($e->getMessage());
        error_log($e->getTraceAsString());

        $this->jsonResponse([
            'success' => false,
            'message' => 'حدث خطأ داخلي',
        ], 500);
    }

    // تسجيل خروج المستخدم وإرجاع رسالة
    public function logout(): void
    {
        $this->logoutSilently();
        $this->jsonResponse(['success' => true, 'message' => 'تم تسجيل الخروج بنجاح']);
    }

    // تسجيل خروج  مسح الجلسة والكوكيز
    protected function logoutSilently(): void
    {
        foreach (['/', '/public', ''] as $path) {
            setcookie('token', '', time() - 3600, $path ?: '/', '', false, true);
        }

        if (session_status() !== PHP_SESSION_NONE) {
            $_SESSION = [];
            session_destroy();
        }
    }
}
