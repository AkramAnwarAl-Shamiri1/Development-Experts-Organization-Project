<?php

use App\Controllers\UserController;
use App\Core\Router;

$router = new Router();

$router->get('/blog-mvc/public/register', [UserController::class, 'showRegister']);
$router->post('/blog-mvc/public/register', [UserController::class, 'register']);
$router->get('/blog-mvc/public/login', [UserController::class, 'showLogin']);
$router->post('/blog-mvc/public/login', [UserController::class, 'login']);
$router->get('/blog-mvc/public/users', [UserController::class, 'index']);
$router->get('/blog-mvc/public/logout', [UserController::class, 'logout']);
