<?php
include_once __DIR__ . "/../Controllers/HomeController.php";
include_once __DIR__ . "/../Controllers/FaviconController.php";
include_once __DIR__ . "/Router.php";

use App\Controllers\FaviconController;
use App\Controllers\HomeController;
use App\Router;

$router = new Router();

$router->get('/', HomeController::class, 'index');
$router->get('/favicon.ico', FaviconController::class, 'favicon');

$router->dispatch();