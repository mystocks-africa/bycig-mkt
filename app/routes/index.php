<?php
include_once __DIR__ . "/../Controllers/HomeController.php";
include_once __DIR__ . "/Router.php";

use App\Controllers\HomeController;
use App\Router;

$router = new Router();

$router->get('/', HomeController::class, 'index');
$router->get('/favicon.ico', HomeController::class, 'favicon');

$router->dispatch();