<?php

namespace App\Controllers;
include_once __DIR__ . "/../../core/auth/Checker.php";
include_once __DIR__ . "/../../core/controller-helper/Controller.php";
include_once __DIR__ . "/../../models/holdings/Model.php";

use App\Core\Auth\Checker;
use App\Core\ControllerHelper;
use App\Models\HoldingModel;

class HoldingsController 
{
    private $authChecker;
    private $controllerHelper;

    public function __construct()
    {
        $this->authChecker = new Checker();
        $this->controllerHelper = new ControllerHelper();
    }

    public function index() 
    {
        $session = $this->authChecker->redirectIfNotAuth(returnSession: true);
        $clusterLeaderEmail = filter_input(INPUT_GET, "cluster_leader_email", FILTER_SANITIZE_EMAIL);

        $holdings = HoldingModel::findAllHoldings($clusterLeaderEmail);
        $this->controllerHelper->render("/holdings/index", [
            "holdings" => $holdings,
            "session" => $session
        ]);
    }

    public function delete()
    {
        $session = $this->authChecker->redirectIfNotAuth(returnSession: true);
        $id = filter_input(INPUT_GET, "id", FILTER_SANITIZE_NUMBER_INT);

        // Query for email as well so only the owner can delete
        HoldingModel::deleteHolding($id, $session['email']);
    }
}
