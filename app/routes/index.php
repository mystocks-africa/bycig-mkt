<?php
include_once __DIR__ . "/../controllers/home/Controller.php";
include_once __DIR__ . "/../controllers/auth/Controller.php";
include_once __DIR__ . "/../controllers/proposal/Controller.php";
include_once __DIR__ . "/../controllers/admin/Controller.php";
include_once __DIR__ . "/Router.php";

use App\Controllers\AdminController;
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

// Authentication post methods
$router->post('/auth/signin', AuthController::class,'signInPost');
$router->post("/auth/signup", AuthController::class, 'signUpPost');
$router->post('/auth/signout', AuthController::class,'signOutPost');

// Proposal get methods
$router->get('/proposals/details', ProposalController::class, 'proposalDetails');
$router->get('/proposals/submit', ProposalController::class,'submit');

// Proposal post methods
$router->post('/proposals/submit', ProposalController::class,'submitPost');

// Admin get methods
$router->get('/admin', AdminController::class, 'index');

// Admin put methods 
$router->put('/admin/handle-proposal-status', AdminController::class,'handleProposalStatusPost');

// Admin delete methods
$router->delete('/admin/delete-proposal', AdminController::class, 'deleteProposal');

$router->dispatch();