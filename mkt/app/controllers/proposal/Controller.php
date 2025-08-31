<?php

namespace App\Controllers;

include_once __DIR__ . "/../../core/controller/Controller.php";
include_once __DIR__ . "/../../core/auth/Session.php";
include_once __DIR__ . "/../../core/auth/Guard.php";
include_once __DIR__ . "/../../core/stocks/Stocks.php";

include_once __DIR__ . "/../../services/proposal/Service.php";

use App\Core\Controller;
use App\Core\Session;
use App\Core\Auth\AuthGuard;
use App\Core\Stocks;

use App\Services\ProposalService;
use Exception;

class ProposalController
{
    private Session $session;
    private AuthGuard $authGuard;
    private ProposalService $proposalService;

    public function __construct() 
    {
        $this->session = new Session();
        $this->authGuard = new AuthGuard($this->session);
        $this->proposalService = new ProposalService();
    }

    public function submit(): void 
    {
        $this->authGuard->redirectIfNotAuth();
        $supportedStocks = Stocks::getSupportedStocks();
        Controller::render("proposal/submit", [
            'supportedStocks' => $supportedStocks
        ]);
    }

    public function submitPost(): void 
    {
        $this->authGuard->redirectIfNotAuth();

        try {
            $stockTicker = filter_input(INPUT_POST, 'stock_ticker', FILTER_SANITIZE_SPECIAL_CHARS);
            $stockName = filter_input(INPUT_POST, 'stock_name', FILTER_SANITIZE_SPECIAL_CHARS);
            $subjectLine = filter_input(INPUT_POST, 'subject_line', FILTER_SANITIZE_SPECIAL_CHARS);
            $thesis = filter_input(INPUT_POST, 'thesis', FILTER_SANITIZE_SPECIAL_CHARS);
            $bidPrice = filter_input(INPUT_POST, 'bid_price', FILTER_VALIDATE_FLOAT);
            $shares = filter_input(INPUT_POST, "shares", FILTER_VALIDATE_INT);
            $proposalFile = $_FILES["proposal_file"] ?? null;
            $this->proposalService->createProposal(
                $this->session->getSession()['email'],
                $stockTicker,
                $stockName,
                $subjectLine,
                $thesis,
                $bidPrice,
                $shares,
                $proposalFile
            );            
            Controller::redirectToResult("Success in submitting proposal", "success");
        } catch (Exception $error) {
            $this->proposalService->deleteFileOnError($this->proposalService->getFileName());
            Controller::redirectToResult($error->getMessage(), "error");
        }
    }
}