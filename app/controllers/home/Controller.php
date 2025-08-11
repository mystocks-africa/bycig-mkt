<?php

namespace App\Controllers;
include_once __DIR__ . "/../../core/auth/Checker.php";
include_once __DIR__ . "/../../core/controller-helper/Controller.php";
include_once __DIR__ . "/../../models/user/Model.php";

use App\Core\Auth\Checker;
use App\ControllerHelper;
use App\Models\UserModel;

class HomeController extends Controller
{
    private $authChecker;
    private $controllerHelper;

    public function __construct()
    {
        $this->authChecker = new Checker();
        $this->controllerHelper = new ControllerHelper;
    }

    public function index()
    { 
        $this->authChecker->redirectIfNotAuth();
        $clusterLeaders = UserModel::findAllClusterLeaders();
        
        $this->controllerHelper->render("index", [
            "clusterLeaders" => $clusterLeaders
        ]);    
    }

    public function favicon()
    {
        $this->controllerHelper->render('favicon');
    }

    public function redirect() {
        $this->controllerHelper->render('redirect');
    }
}
