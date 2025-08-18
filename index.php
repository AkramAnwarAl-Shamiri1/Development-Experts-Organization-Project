<?php
declare(strict_types=1);


spl_autoload_register(function ($class) {
    $prefix = "App\\";
    $baseDir = __DIR__ . "/../app/";

    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $file = $baseDir . str_replace("\\", "/", $relativeClass) . ".php";

    if (file_exists($file)) {
        require $file;
    }
});


define('BASE_PATH', rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/'));


$rawPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = $rawPath;
if (BASE_PATH !== '' && str_starts_with($rawPath, BASE_PATH)) {
    $path = substr($rawPath, strlen(BASE_PATH));
    if ($path === '') $path = '/';
}

use App\Core\Router;
use App\Controllers\UserController;

$router = new Router();


$router->get('/', [UserController::class, 'showLogin']);
$router->get('/login', [UserController::class, 'showLogin']);
$router->post('/login', [UserController::class, 'login']);

$router->get('/register', [UserController::class, 'showRegister']);
$router->post('/register', [UserController::class, 'register']);

$router->get('/users', [UserController::class, 'index']);
$router->get('/logout', [UserController::class, 'logout']);

$router->dispatch($_SERVER['REQUEST_METHOD'], $path);
