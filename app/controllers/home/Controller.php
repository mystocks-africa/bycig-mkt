<?php

namespace App\Controllers;

include_once __DIR__ . "/../../core/Controller.php";
include_once __DIR__ . "/../../models/user/Model.php";

use App\Core\Controller;
use App\Models\User;

class HomeController
{
    public function index()
    { 
        Controller::redirectIfNotAuth();
        $clusterLeaders = User::findAllClusterLeaders();
        
        Controller::render("index", [
            "clusterLeaders"=>$clusterLeaders
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