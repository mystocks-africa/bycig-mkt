<?php
use App\Controllers\AccountController;
use App\Controllers\AdminController;
use App\Controllers\AuthController;
use App\Controllers\HomeController;
use App\Controllers\ProposalController;
use App\Controllers\HoldingsController;
use App\Controllers\ProfileController;
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
$router->get('/auth/update-pwd', AuthController::class,'updatePwd');

// Authentication post methods
$router->post('/auth/signin', AuthController::class,'processSignIn');
$router->post("/auth/signup", AuthController::class, 'processSignUp');
$router->post('/auth/signout', AuthController::class,'processSignOut');
$router->post('/auth/forgot-pwd', AuthController::class,'processForgotPwd');
$router->post('/auth/update-pwd', AuthController::class,'processUpdatePwd');

// Proposal get methods
$router->get('/proposals/submit', ProposalController::class,'submit');

// Proposal post methods
$router->post('/proposals/submit', ProposalController::class,'processSubmit');

// Admin get methods
$router->get('/admin', AdminController::class, 'index');

// Admin put methods 
$router->put('/admin/handle-proposal-status', AdminController::class,'processProposalStatusPost');

// Holdings post methods
$router->post('/holdings/buy', HoldingsController::class, 'buy');
$router->post('/holdings/sell', HoldingsController::class, 'sell');

// Profile get methods
$router->get("/profile", ProfileController::class, 'index');

// Profile delete methods
$router->delete("/profile/delete-user", ProfileController::class, "deleteUser");

// Profile put methods
$router->post("/profile/update-user", ProfileController::class, "updateUser");

// Account get method
$router->get('/account', AccountController::class, 'index');

$router->dispatch();