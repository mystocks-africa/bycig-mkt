<?php

namespace App\Controllers;

include_once __DIR__ . "/../../core/Controller.php";
include_once __DIR__ . "/../../models/user/Model.php";
include_once __DIR__ . "/../../models/holdings/Model.php";

use App\Core\Controller;
use App\Models\Holding;

class HomeController
{
    private $holding;

    public function __construct() {
        $this->holding = new Holding();
    }
    public function index()
    { 
        Controller::redirectIfNotAuth();
        $holdings = $this->holding->findAllHoldings();
        
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