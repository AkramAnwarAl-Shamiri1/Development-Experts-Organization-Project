<?php
declare(strict_types=1);

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$allowedOrigin = 'http://localhost:5173';

if ($origin === $allowedOrigin) {
    header("Access-Control-Allow-Origin: $allowedOrigin");
    header("Access-Control-Allow-Credentials: true");
}

header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}


error_reporting(E_ALL);
ini_set("display_errors", "1");


spl_autoload_register(function($class){
    $prefix = "App\\";
    $baseDir = __DIR__."/../app/";
    if(strncmp($prefix,$class,strlen($prefix))!==0) return;

    $relativeClass = substr($class,strlen($prefix));
    $file = $baseDir.str_replace("\\","/",$relativeClass).".php";
    if(file_exists($file)) require $file;
});


use App\Core\Router;

$router = new Router();
require __DIR__."/../routes/web.php"; 

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = str_replace('/blog-mvc/public', '', $path);
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';


$router->dispatch($method, $path);
