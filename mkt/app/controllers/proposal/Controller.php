<?php

namespace App\Controllers;

include_once __DIR__ . "/../../core/controller/Controller.php";
include_once __DIR__ . "/../../core/auth/Session.php";
include_once __DIR__ . "/../../core/templates/DbTemplate.php";
include_once __DIR__ . "/../../core/files/Files.php";
include_once __DIR__ . "/../../core/auth/Guard.php";

include_once __DIR__ . "/../../models/proposals/Entity.php";
include_once __DIR__ . "/../../models/proposals/Repository.php";
include_once __DIR__ . "/../../models/user/Repository.php";
include_once __DIR__ . "/../../../utils/env.php";

use App\Core\Controller;
use App\Core\Session;
use App\DbTemplate;
use App\Core\Auth\AuthGuard;

use App\Models\Entity\ProposalEntity;
use App\Models\Repository\ProposalRepository;
use App\Models\Repository\UserRepository;
use App\Core\Files;
use Exception;

class ProposalController
{
    private $env;
    private ProposalRepository $proposalRepository;
    private UserRepository $userRepository;
    private DbTemplate $db;
    private Session $session;

    public function __construct() 
    {
        global $env;

        $this->env = $env; 
        $this->db = new DbTemplate();
        $this->proposalRepository = new ProposalRepository($this->db->getPdo());
        $this->userRepository = new UserRepository($this->db->getPdo());
        $this->session = new Session();
    }

    public function submit() 
    {
        AuthGuard::redirectIfNotAuth($this->session);
        Controller::render("proposal/submit");
    }

    public function submitPost() 
    {
        AuthGuard::redirectIfNotAuth($this->session);

        try {
            $stockTicker = filter_input(INPUT_POST, 'stock_ticker', FILTER_SANITIZE_SPECIAL_CHARS);
            $stockName = filter_input(INPUT_POST, 'stock_name', FILTER_SANITIZE_SPECIAL_CHARS);
            $subjectLine = filter_input(INPUT_POST, 'subject_line', FILTER_SANITIZE_SPECIAL_CHARS);
            $thesis = filter_input(INPUT_POST, 'thesis', FILTER_SANITIZE_SPECIAL_CHARS);
            $bidPrice = filter_input(INPUT_POST, 'bid_price', FILTER_VALIDATE_FLOAT);
            $shares = filter_input(INPUT_POST, "shares", FILTER_VALIDATE_INT);
            $proposalFile = $_FILES["proposal_file"] ?? null;

            $fileName = Files::uploadFile($proposalFile);

            if (!$stockTicker || !$stockName || !$subjectLine || !$thesis || !$bidPrice || !$shares || !$fileName ) {
                Controller::redirectToResult("Error in form input", "error");
                exit();
            }

            $user = $this->userRepository->findByEmail($this->session->getSession()["email"]);

            if (!$user["cluster_leader"]) {
                throw new Exception("You need to link with a cluster leader before completing this operation");
            }
            
            $proposalEntity = new ProposalEntity(
                $this->session->getSession()["email"], 
                $stockTicker, 
                $stockName, 
                $subjectLine, 
                $thesis, 
                $bidPrice, 
                $shares, 
                $fileName
            );

            $this->proposalRepository->save($proposalEntity);

            Controller::redirectToResult("Success in submitting proposal", "success");
        } catch (Exception $error) {
            // Delete the file if there was an error to avoid orphaned files
            // We need to get the file from the server before deletion because sometimes the error happens before creation
            $file = Files::getFile($fileName);
            if (isset($file)) {
                Files::deleteFile($fileName);
            }
            
            Controller::redirectToResult($error->getMessage(), "error");
        }
    }
}
