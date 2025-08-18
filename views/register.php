<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<title>تسجيل مستخدم جديد</title>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-jQfYfFwn4Hn1Q6G2Pcb7VxG1k3v9g2X9sBziZP8WvX/UQ8HMbA0h6Bv6f0gGp4x9V4uV5o2YyS4M5dK9L+Vlw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
body { font-family: 'Cairo', sans-serif; background: linear-gradient(135deg,#242856,#1b1f3e); display:flex;justify-content:center;align-items:center; height:100vh;margin:0;}
.register-container { background: linear-gradient(145deg,#ffffff,#f0f0f0); padding:30px; border-radius:15px; box-shadow:0 15px 35px rgba(0,0,0,0.2); width:350px; text-align:center; animation: fadeIn 0.8s ease; }
.register-container h1 { margin-bottom:20px; color:#242856; }
input { width:100%; padding:14px; margin:10px 0; border:1px solid #ccc; border-radius:8px; font-size:16px; transition: all 0.3s ease; }
input:focus { border-color:#facc15; box-shadow:0 0 8px rgba(250,204,21,0.5); outline:none; }
button { width:100%; padding:14px; background:#facc15; border:none; border-radius:8px; color:#242856; font-size:16px; font-weight:bold; cursor:pointer; transition: all 0.3s ease; }
button:hover { transform:scale(1.05); box-shadow:0 4px 15px rgba(0,0,0,0.2); background:#eab308; }
p{ margin-top:15px; font-size:14px; }
a{ color:#facc15; text-decoration:none; font-weight:bold; }
a:hover{ text-decoration:underline; }
.error{ color:red; margin-bottom:15px; }
@keyframes fadeIn{ from{opacity:0; transform:translateY(-20px);} to{opacity:1; transform:translateY(0);} }
@media (max-width:480px){ .register-container{ width:90%; padding:20px; } }
</style>
</head>
<body>
<div class="register-container">
<h1><i class="fa-solid fa-user-plus"></i> تسجيل مستخدم جديد</h1>
<?php if (!empty($error)) : ?>
<p class="error"><?= $error ?></p>
<?php endif; ?>
<form method="post" action="/blog-mvc/public/register">
<input type="text" name="name" placeholder="الاسم الكامل" required>
<input type="email" name="email" placeholder="البريد الإلكتروني" required>
<input type="password" name="password" placeholder="كلمة المرور" required>
<button type="submit"><i class="fa-solid fa-user-plus"></i> تسجيل</button>
</form>
<p>لديك حساب بالفعل؟ <a href="/blog-mvc/public/login"><i class="fa-solid fa-sign-in-alt"></i> تسجيل الدخول</a></p>
</div>
</body>
</html>
