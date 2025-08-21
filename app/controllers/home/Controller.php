<?php

namespace App\Controllers;

include_once __DIR__ . "/../../core/Controller.php";
include_once __DIR__ . "/../../models/user/Model.php";
include_once __DIR__ . "/../../models/holdings/Repository.php";

use App\Core\Controller;
use App\Models\Repository\HoldingRepository;

class HomeController
{
    private HoldingRepository $holdingRepository;

    public function __construct() {
        $this->holdingRepository = new HoldingRepository();
    }

    public function index()
    { 
        Controller::redirectIfNotAuth();
        
        $holdings = $this->holdingRepository->findAll();
        
        Controller::render("index", [
            "holdings"=>$holdings
        ]);    
    }

    public function favicon()
    {
        Controller::redirectIfNotAuth();
        Controller::render('favicon');
    }

    public function redirect() {
        Controller::render('redirect');
    }
}