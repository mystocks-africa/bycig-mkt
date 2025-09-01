<?php
namespace App\Controllers;

use App\Core\Controller\Controller;
use App\Core\Auth\Session;
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