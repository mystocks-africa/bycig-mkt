<?php

namespace App\Controllers;
include_once __DIR__ . "/Controller.php";
include_once __DIR__ . "/../../models/proposals/Model.php";

use App\Controller;
use App\Models\Proposal;

class AdminController extends Controller 
{
    public function index()
    {
        $session = parent::redirectIfNotClusterLeader();

        $proposals = Proposal::findProposalByClusterLeader($session['email']);
        parent::render("admin/index", [
            'proposals' => $proposals
        ]);
    }
}