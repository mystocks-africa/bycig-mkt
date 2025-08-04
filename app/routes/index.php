<?php
include_once __DIR__ . "/../Controllers/HomeController.php";
include_once __DIR__ . "/../Controllers/AuthController.php";
include_once __DIR__ . "/Router.php";

use App\Controllers\AuthController;
use App\Controllers\HomeController;
use App\Router;

$router = new Router();

// Home get methods
$router->get('/', HomeController::class, 'index');
$router->get('/favicon.ico', HomeController::class, 'favicon');
$router->get('/redirect', HomeController::class, 'redirect');

// Authentication get methods 
$router->get('/signup', AuthController::class,'signUp');
$router->get('/signin', AuthController::class, 'signIn');
$router->get('/signout', AuthController::class,'signOut');

// Authentication post methods
$router->post('/signin', AuthController::class,'signInPost');
$router->post("/signup", AuthController::class, 'signUpPost');
$router->post('/signout', AuthController::class,'signOutPost');

$router->dispatch();