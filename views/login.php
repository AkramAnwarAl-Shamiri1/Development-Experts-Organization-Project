<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<title>تسجيل الدخول</title>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-papEjrSP1zpM+O/2K5Ki0++l6j5WxYq/E8p95KFA8e4kH1gFR0P0qJhv+kC1G3W+wzKTF3S4b8iFh+fC5uM0Jg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
body { font-family: 'Cairo', sans-serif; background: linear-gradient(135deg, #242856, #1b1f3e); display:flex; justify-content:center; align-items:center; height:100vh; margin:0; }
.login-container { background: linear-gradient(145deg, #ffffff, #f0f0f0); padding:30px; border-radius:15px; box-shadow:0 15px 35px rgba(0,0,0,0.2); width:350px; text-align:center; animation: fadeIn 0.8s ease; }
.login-container h1 { margin-bottom:20px; color:#242856; }
input { width:100%; padding:14px; margin:10px 0; border:1px solid #ccc; border-radius:8px; font-size:16px; transition: all 0.3s ease; }
input:focus { border-color:#facc15; box-shadow:0 0 8px rgba(250,204,21,0.5); outline:none; }
button { width:100%; padding:14px; background:#facc15; border:none; border-radius:8px; color:#242856; font-size:16px; font-weight:bold; cursor:pointer; transition: all 0.3s ease; }
button:hover { transform: scale(1.05); box-shadow:0 4px 15px rgba(0,0,0,0.2); background:#eab308; }
p { margin-top:15px; font-size:14px; }
a { color:#facc15; text-decoration:none; font-weight:bold; }
a:hover { text-decoration:underline; }
.error { color:red; margin-bottom:15px; }
@keyframes fadeIn { from{opacity:0; transform:translateY(-20px);} to{opacity:1; transform:translateY(0);} }
@media(max-width:480px){ .login-container{ width:90%; padding:20px; } }
</style>
</head>
<body>
<div class="login-container">
<h1><i class="fas fa-sign-in-alt"></i> تسجيل الدخول</h1>
<?php if (!empty($error)) : ?>
<p class="error"><?= $error ?></p>
<?php endif; ?>
<form method="post" action="/blog-mvc/public/login">
<input type="email" name="email" placeholder="البريد الإلكتروني" required>
<input type="password" name="password" placeholder="كلمة المرور" required>
<button type="submit"><i class="fas fa-arrow-right"></i> دخول</button>
</form>
<p>ليس لديك حساب؟ <a href="/blog-mvc/public/register"><i class="fas fa-user-plus"></i> إنشاء حساب جديد</a></p>
</div>
</body>
</html>
