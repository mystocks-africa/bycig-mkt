<?php
include_once __DIR__ . "/../controllers/home/Controller.php";
include_once __DIR__ . "/../controllers/auth/Controller.php";
include_once __DIR__ . "/../controllers/proposal/Controller.php";
include_once __DIR__ . "/Router.php";

use App\Controllers\AuthController;
use App\Controllers\HomeController;
use App\Controllers\ProposalController;
use App\Router;

$router = new Router();

// Home get methods
$router->get('/', HomeController::class, 'index');
$router->get('/favicon.ico', HomeController::class, 'favicon');
$router->get('/redirect', HomeController::class, 'redirect');

// Authentication get methods 
$router->get('/auth/signup', AuthController::class,'signUp');
$router->get('/auth/signin', AuthController::class, 'signIn');
$router->get('/auth/signout', AuthController::class,'signOut');

// Authentication post methods
$router->post('/auth/signin', AuthController::class,'signInPost');
$router->post("/auth/signup", AuthController::class, 'signUpPost');
$router->post('/auth/signout', AuthController::class,'signOutPost');

// Proposal get methods
$router->get('/proposals', ProposalController::class, 'index');
$router->get('/proposals/details', ProposalController::class, 'proposalDetails');

// Proposal post method

$router->dispatch();