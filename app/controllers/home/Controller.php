<?php

namespace App\Controllers;
include_once __DIR__ . "/../Controller.php";
include_once __DIR__ . "/../../models/proposals/Model.php";

use App\Controller;
use App\Models\Proposal;

class HomeController extends Controller
{
    public function index()
    {
        parent::redirectIfNotAuth();
        $proposals = Proposal::findAllProposals();
        
        parent::render("index", [
            "proposals"=>$proposals
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