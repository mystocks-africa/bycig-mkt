<?php

namespace App\Controllers;

include_once __DIR__ . "/../../core/controller/Controller.php";
include_once __DIR__ . "/../../core/templates/DbTemplate.php";
include_once __DIR__ . "/../../core/auth/Guard.php";
include_once __DIR__ . "/../../core/auth/Session.php";

include_once __DIR__ . "/../../models/holdings/Repository.php";

use App\Core\Controller;
use App\DbTemplate;
use App\Core\Auth\AuthGuard;
use App\Core\Session;

use App\Models\Repository\HoldingRepository;

class HomeController
{
    private HoldingRepository $holdingRepository;
    private DbTemplate $db;
    private Session $session;
    private AuthGuard $authGuard;

    public function __construct() {
        $this->db = new DbTemplate();
        $this->holdingRepository = new HoldingRepository($this->db->getPdo());
        $this->session = new Session();
        $this->authGuard = new AuthGuard($this->session);
    }

    public function index()
    { 
        $this->authGuard->redirectIfNotAuth();

        $holdings = $this->holdingRepository->findAll();
        
        Controller::render("index", [
            "holdings"=>$holdings
        ]);    
    }

    public function favicon()
    {
        $this->authGuard->redirectIfNotAuth();
        Controller::render('favicon');
    }

    public function redirect() {
        Controller::render('redirect');
    }
}