<?php

namespace App\Controllers;
include_once __DIR__ . "/../Controller.php";
include_once __DIR__ . "/../../models/proposals/User.php";

use App\Controller;
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        parent::redirectIfNotAuth();
        $clusterLeaders = User::findAllClusterLeaders();
        
        parent::render("index", [
            "clusterLeaders"=>$clusterLeaders
        ]);    
    }

    public function favicon()
    {
        parent::redirectIfNotAuth();
        parent::render('favicon');
    }

    public function redirect() {
        parent::render('redirect');
    }
}