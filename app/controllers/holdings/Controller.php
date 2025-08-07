<?php

namespace App\Controllers;
include_once __DIR__ . "/../Controller.php";
include_once __DIR__ . "/../../models/holdings/Model.php";

use App\Controller;
use App\Models\Holding;

class HoldingsController extends Controller
{
    public function details() 
    {
        $clusterLeaderEmail = filter_input(INPUT_GET, "cluster_leader_email", FILTER_SANITIZE_EMAIL);

        $holdings = Holding::findAllHoldings($clusterLeaderEmail);
        parent::render("/holdings/details", [
            "holdings"=> $holdings
        ]);
    }
}