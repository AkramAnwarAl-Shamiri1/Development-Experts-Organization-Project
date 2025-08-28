<?php
// هنااستدعاء الراوتر والكلاسات الأساسية 
use App\Core\Router;
use App\Controllers\UserController;
use App\Controllers\ProgramController;
use App\Controllers\ActivityController;
use App\Controllers\CompanieController;
use App\Controllers\PRCampaignController;

//  تسجيل الدخول انشاء حستاب جديد والخروج

$router->post('/api/login', [UserController::class, 'login']);       
$router->post('/api/register', [UserController::class, 'register']);  
$router->post('/api/logout', [UserController::class, 'logout']);      
$router->get('/api/authenticate', [UserController::class, 'authenticate']); 
$router->get('/api/csrf-token', [UserController::class, 'csrfToken']); 

//   admen  إدارة المستخدمين خاص بالمسؤول بي api
$router->get('/api/users', [UserController::class, 'allUsers'], 'auth'); 
$router->get('/api/users/{id}', [UserController::class, 'find'], 'auth'); 
$router->post('/api/users', [UserController::class, 'store'], 'auth'); 
$router->put('/api/users/{id}', [UserController::class, 'update'], 'auth'); 
$router->delete('/api/users/{id}', [UserController::class, 'delete'], 'auth');

//  البرامجapi
$router->get('/api/programs', [ProgramController::class, 'all'], 'auth'); 
$router->get('/api/programs/{id}', [ProgramController::class, 'find'], 'auth'); 
$router->post('/api/programs', [ProgramController::class, 'store'], 'auth'); 
$router->put('/api/programs/{id}', [ProgramController::class, 'update'], 'auth'); 
$router->delete('/api/programs/{id}', [ProgramController::class, 'delete'], 'auth'); 

//  الأنشطة api
$router->get('/api/activities', [ActivityController::class, 'all'], 'auth'); 
$router->get('/api/activities/{id}', [ActivityController::class, 'find'], 'auth'); 
$router->post('/api/activities', [ActivityController::class, 'store'], 'auth'); 
$router->put('/api/activities/{id}', [ActivityController::class, 'update'], 'auth'); 
$router->delete('/api/activities/{id}', [ActivityController::class, 'delete'], 'auth'); 

//  الشراكاتapi
$router->get('/api/companies', [CompanieController::class, 'all'], 'auth'); 
$router->get('/api/companies/{id}', [CompanieController::class, 'find'], 'auth'); 
$router->post('/api/companies', [CompanieController::class, 'store'], 'auth'); 
$router->put('/api/companies/{id}', [CompanieController::class, 'update'], 'auth'); 
$router->delete('/api/companies/{id}', [CompanieController::class, 'delete'], 'auth'); 

//  الحملات التبرعapi
$router->get('/api/prcampaigns', [PRCampaignController::class, 'all'], 'auth'); 
$router->get('/api/prcampaigns/{id}', [PRCampaignController::class, 'find'], 'auth'); 
$router->post('/api/prcampaigns', [PRCampaignController::class, 'store'], 'auth');
$router->put('/api/prcampaigns/{id}', [PRCampaignController::class, 'update'], 'auth'); 
$router->delete('/api/prcampaigns/{id}', [PRCampaignController::class, 'delete'], 'auth'); 
