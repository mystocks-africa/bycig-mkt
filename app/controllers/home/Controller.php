<?php

namespace App\Controllers;
include_once __DIR__ . "/../Controller.php";
include_once __DIR__ . "/../../models/user/Model.php";

use App\Controller;
use App\Models\UserModel;

class HomeController extends Controller
{
    public function index()
    { 
        parent::redirectIfNotAuth();
        $clusterLeaders = UserModel::findAllClusterLeaders();
        
        parent::render("index", [
            "clusterLeaders" => $clusterLeaders
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
