<?php

namespace App\Controllers;

include_once __DIR__ . "/../../core/controller/Controller.php";
include_once __DIR__ . "/../../core/auth/Guard.php";
include_once __DIR__ . "/../../core/auth/Session.php";

include_once __DIR__ . "/../../services/holdings/Service.php";

use App\Core\Controller;
use App\Core\Session;
use App\Core\Auth\AuthGuard;

use App\Services\HoldingService;
use Exception;

class HoldingsController 
{

    private Session $session;
    private AuthGuard $authGuard;
    private HoldingService $holdingService;

    public function __construct() {
        $this->session = new Session();
        $this->authGuard = new AuthGuard($this->session);
        $this->holdingService = new HoldingService();
    }

    public function buy(): void
    {
        $this->authGuard->redirectIfNotAuth();

        try {
            $id = filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT);
            $this->holdingService->processBuyOrder($id, $this->session->getSession()['email']);
            Controller::redirectToResult("Successfully bought holdings", "success");
        } catch(Exception $error) {
            Controller::redirectToResult($error->getMessage(), "error");
        }
    }

    public function sell(): void 
    {
        $this->authGuard->redirectIfNotAuth();

        try {
            $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
            $this->holdingService->processSellOrder($id, $this->session->getSession()['email']);
            Controller::redirectToResult("Successfully sold your holding", "success");
        } catch (Exception $error) {
            Controller::redirectToResult($error->getMessage(), "error");
        }
    }
}