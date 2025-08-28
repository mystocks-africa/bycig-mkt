<?php

namespace App\Controllers;

include_once __DIR__ . "/../../core/controller/Controller.php";
include_once __DIR__ . "/../../core/templates/DbTemplate.php";
include_once __DIR__ . "/../../models/holdings/Repository.php";

use App\Core\Controller;
use App\DbTemplate;
use App\Models\Repository\HoldingRepository;

class HomeController
{
    private HoldingRepository $holdingRepository;
    private DbTemplate $db;

    public function __construct() {
        $this->db = new DbTemplate();
        $this->holdingRepository = new HoldingRepository($this->db->getPdo());
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