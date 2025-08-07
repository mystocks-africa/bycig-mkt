<?php

namespace App\Controllers;
include_once __DIR__ . "/../Controller.php";
include_once __DIR__ . "/../../models/holdings/Model.php";

use App\Controller;
use App\Models\Holding;

class HoldingsController extends Controller
{
    public function index() 
    {
        $clusterLeaderEmail = filter_input(INPUT_GET, "cluster_leader_email", FILTER_SANITIZE_EMAIL);

        $holdings = Holding::findAllHoldings($clusterLeaderEmail);
        parent::render("/holdings/index", [
            "holdings"=> $holdings
        ]);
    }

    public function delete()
    {
        $session = parent::redirectIfNotAuth(returnSession: true);
        $id = filter_input(INPUT_GET, "id", FILTER_SANITIZE_NUMBER_INT);

        // Query for email as well so only the owner can delete
        Holding::deleteHolding($id, $session['email']);
    }
}