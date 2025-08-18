<?php
namespace App\Controllers;
use App\Core\Session;
use App\Models\User;

class UserController
{
    public function showRegister(){
        require_once __DIR__ . '/../Views/register.php';
    }

    public function register(){
        Session::start();
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if(User::findByEmail($email)){
            $error = "البريد مستخدم مسبقًا!";
            require_once __DIR__ . '/../Views/register.php';
            return;
        }

        $password_hashed = password_hash($password, PASSWORD_DEFAULT);
        $role = 'user'; 

        $stmt = \App\Core\App::db()->prepare(
            "INSERT INTO users (name,email,password,role) VALUES (:name,:email,:password,:role)"
        );
        $stmt->execute([
            'name'=>$name,
            'email'=>$email,
            'password'=>$password_hashed,
            'role'=>$role
        ]);

        header("Location: " . BASE_PATH . "/login");
        exit;
    }

    public function showLogin(){
        require_once __DIR__ . '/../Views/login.php';
    }

    public function login(){
        Session::start();
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $user = User::findByEmail($email);
        if($user && password_verify($password, $user['password'])){
            Session::set('user_id', $user['id']);
            Session::set('user_role', $user['role']); 
            header("Location: " . BASE_PATH . "/users");
            exit;
        } else {
            $error = "البريد أو كلمة المرور غير صحيحة!";
            require_once __DIR__ . '/../Views/login.php';
        }
    }

    public function index(){
        Session::start();
        if(!Session::get('user_id')){
            header("Location: " . BASE_PATH . "/login"); exit;
        }
       
        if(Session::get('user_role') !== 'admin'){
    \App\Controllers\ErrorController::noAccess();
}


        $users = User::getAll();
        require_once __DIR__ . '/../Views/users.php';
    }

    public function logout(){
        Session::start();
        Session::destroy();
        header("Location: " . BASE_PATH . "/login"); exit;
    }
}
