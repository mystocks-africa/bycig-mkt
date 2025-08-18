<?php
include_once __DIR__ . "/../controllers/home/Controller.php";
include_once __DIR__ . "/../controllers/auth/Controller.php";
include_once __DIR__ . "/../controllers/proposal/Controller.php";
include_once __DIR__ . "/../controllers/admin/Controller.php";
include_once __DIR__ . "/../controllers/holdings/Controller.php";
include_once __DIR__ . "/Router.php";

use App\Controllers\AdminController;
use App\Controllers\AuthController;
use App\Controllers\HomeController;
use App\Controllers\ProposalController;
use App\Controllers\HoldingsController;
use App\Router;

$router = new Router();

// Home get methods
$router->get('/', HomeController::class, 'index');
$router->get('/favicon.ico', HomeController::class, 'favicon');
$router->get('/redirect', HomeController::class, 'redirect');

// Authentication get methods 
$router->get('/auth/signup', AuthController::class,'signUp');
$router->get('/auth/signin', AuthController::class, 'signIn');
$router->get('/auth/forgot-pwd', AuthController::class,'forgotPwd');

// Authentication post methods
$router->post('/auth/signin', AuthController::class,'processSignIn');
$router->post("/auth/signup", AuthController::class, 'processSignUp');
$router->post('/auth/signout', AuthController::class,'processSignOut');
$router->post('/auth/forgot-pwd', AuthController::class,'processForgotPwd');

// Proposal get methods
$router->get('/proposals/submit', ProposalController::class,'submit');

// Proposal post methods
$router->post('/proposals/submit', ProposalController::class,'submitPost');

// Admin get methods
$router->get('/admin', AdminController::class, 'index');

// Admin put methods 
$router->put('/admin/handle-proposal-status', AdminController::class,'handleProposalStatusPost');

// Admin delete methods
$router->delete('/admin/delete-proposal', AdminController::class, 'deleteProposal');

// Holdings get methods
$router->get('/holdings', HoldingsController::class, 'index');

// Holdings delete methods
$router->delete('/holdings/delete', HoldingsController::class, 'delete');

$router->dispatch();