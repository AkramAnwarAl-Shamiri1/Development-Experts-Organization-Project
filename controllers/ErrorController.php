<?php
namespace App\Controllers;
use App\Core\Session;

class ErrorController
{
    public static function noAccess()
    {
        Session::start();
        ?>
        <!DOCTYPE html>
        <html lang="ar" dir="rtl">
        <head>
            <meta charset="UTF-8">
            <title>خطأ صلاحيات</title>
            <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
            <style>
                body {
                    font-family: 'Cairo', sans-serif;
                    background: linear-gradient(135deg, #242856, #1b1f3e);
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                    margin: 0;
                    color: #fff;
                    text-align: center;
                }
                .error-container {
                    background: linear-gradient(145deg, #ffffff, #f0f0f0);
                    padding: 40px;
                    border-radius: 15px;
                    box-shadow: 0 15px 35px rgba(0,0,0,0.2);
                    max-width: 400px;
                    color: #242856;
                    animation: fadeIn 0.8s ease;
                }
                .error-container h1 {
                    font-size: 24px;
                    margin-bottom: 20px;
                    color: #242856;
                }
                .error-container p {
                    font-size: 16px;
                    margin-bottom: 25px;
                }
                a {
                    color: #242856;
                    text-decoration: none;
                    font-weight: bold;
                    background: #facc15;
                    padding: 10px 20px;
                    border-radius: 8px;
                    transition: all 0.3s ease;
                }
                a:hover {
                    background: #eab308;
                }
                @keyframes fadeIn { from{opacity:0; transform:translateY(-20px);} to{opacity:1; transform:translateY(0);} }
            </style>
        </head>
        <body>
            <div class="error-container">
                <h1><i class="fas fa-exclamation-circle"></i> وصول مرفوض!</h1>
                <p>ليس لديك صلاحية الدخول إلى هذه الصفحة.</p>
                <a href="<?= BASE_PATH ?>/login"><i class="fas fa-sign-in-alt"></i> العودة لتسجيل الدخول</a>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}
