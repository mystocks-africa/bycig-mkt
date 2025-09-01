<?php
namespace App\Controllers;

use App\Core\Controller\Controller;
use App\Core\Auth\AuthGuard;
use App\Core\Auth\Session;

use App\Services\HomeService;


class HomeController
{
    private Session $session;
    private AuthGuard $authGuard;
    private HomeService $homeService;

    public function __construct() {
        $this->session = new Session();
        $this->authGuard = new AuthGuard($this->session);
        $this->homeService = new HomeService();
    }

    public function index(): void
    { 
        $this->authGuard->redirectIfNotAuth();
        $holdings = $this->homeService->getAllHoldings();  
        Controller::render("index", [
            "holdings"=>$holdings
        ]);    
    }

    public function favicon(): void
    {
        $this->authGuard->redirectIfNotAuth();
        Controller::render('favicon');
    }

    public function redirect(): void {
        Controller::render('redirect');
    }
}