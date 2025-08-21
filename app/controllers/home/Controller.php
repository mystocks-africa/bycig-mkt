<?php

namespace App\Controllers;

include_once __DIR__ . "/../../core/Controller.php";
include_once __DIR__ . "/../../models/user/Model.php";
include_once __DIR__ . "/../../models/holdings/Model.php";

use App\Core\Controller;
use App\Models\Holding;

class HomeController
{
    public function index()
    { 
        Controller::redirectIfNotAuth();
        $holdings = Holding::findAllHoldings();
        
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