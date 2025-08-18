<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<title>المستخدمون</title>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-jQfYfFwn4Hn1Q6G2Pcb7VxG1k3v9g2X9sBziZP8WvX/UQ8HMbA0h6Bv6f0gGp4x9V4uV5o2YyS4M5dK9L+Vlw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
body { 
    font-family: 'Cairo', sans-serif; 
    background: linear-gradient(135deg, #242856, #1b1f3e); 
    margin:0; padding:0; 
}
.container { 
    width: 90%; 
    max-width: 800px; 
    margin: 50px auto; 
    background:#ffffff; 
    padding:20px; 
    border-radius:15px; 
    box-shadow:0 10px 25px rgba(0,0,0,0.2); 
}
h1 { 
    text-align:center; 
    color: #242856;
    margin-bottom:25px; 
}
table { 
    width:100%; 
    border-collapse: collapse; 
}
th, td { 
    padding:12px 15px; 
    border-bottom:1px solid #ddd; 
    text-align:center; 
}
th { 
    background:#242856; 
    color:#fff; 
}
tr:hover { 
    background:#facc15; 
    color:#242856; 
    transition:0.3s; 
}
.role-admin { 
    color: #f87171; 
    font-weight:bold; 
}
.role-user { 
    color: #60a5fa; 
    font-weight:bold; 
}
a.logout { 
    display:inline-block; 
    margin-bottom:15px; 
    color:#facc15; 
    text-decoration:none; 
    font-weight:bold; 
}
a.logout:hover { text-decoration:underline; }

@media(max-width:600px){ 
    th, td{ font-size:14px; padding:10px; } 
}
</style>
</head>
<body>
<div class="container">
<h1><i class="fas fa-users"></i> قائمة المستخدمين</h1>

<p><a href="<?= BASE_PATH ?>/logout" class="logout"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a></p>

<table>
    <thead>
        <tr>
            <th>رقم</th>
            <th>الاسم</th>
            <th>البريد الإلكتروني</th>
            <th>الدور</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($users as $index => $user): ?>
        <tr>
            <td><?= $index + 1 ?></td>
            <td><?= htmlspecialchars($user['name']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td class="<?= $user['role'] === 'admin' ? 'role-admin' : 'role-user' ?>">
                <?= htmlspecialchars($user['role']) ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>
</body>
</html>
