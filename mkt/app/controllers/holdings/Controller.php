<?php

namespace App\Controllers;

include_once __DIR__ . "/../../core/controller/Controller.php";
include_once __DIR__ . "/../../core/templates/DbTemplate.php";
include_once __DIR__ . "/../../core/files/Files.php";
include_once __DIR__ . "/../../core/auth/Guard.php";
include_once __DIR__ . "/../../core/auth/Session.php";

include_once __DIR__ . "/../../models/holdings/Repository.php";
include_once __DIR__ . "/../../models/user/Repository.php";

use App\Core\Controller;
use App\Core\Files;
use App\DbTemplate;
use App\Core\Session;
use App\Core\Auth\AuthGuard;

use App\Models\Repository\HoldingRepository;
use App\Models\Repository\UserRepository;
use Exception;

class HoldingsController 
{
    private HoldingRepository $holdingRepository;
    private UserRepository $userRepository;
    private DbTemplate $db;
    private Session $session;
    private AuthGuard $authGuard;

    public function __construct() {
        $this->db = new DbTemplate();
        $this->holdingRepository = new HoldingRepository($this->db->getPdo());
        $this->userRepository = new UserRepository($this->db->getPdo());
        $this->session = new Session();
        $this->authGuard = new AuthGuard($this->session);
    }

    public function sell()
    {
        $this->authGuard->redirectIfNotAuth();
        $this->db->getPdo()->beginTransaction();

        try {
            $id = filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT);

            $holding = $this->holdingRepository->findById($id);

            if (!$holding['fulfilled']) {
                Controller::redirectToResult("Holding order needs to be fulfilled before selling", "error");
            }

            $user = $this->userRepository->findByEmail($this->session->getSession()['email']);
            $newBalance = $user['balance'] - $user['bought_price'];

            $this->userRepository->updateBalance($newBalance, $this->session->getSession()['email']);
            $this->holdingRepository->delete($id, $this->session->getSession()['email']);
            Files::deleteFile($holding['proposal_file']);

            $this->db->getPdo()->commit();
            Controller::redirectToResult("Successfully deleted holdings", "success");

        } catch(Exception $error) {
            $this->db->getPdo()->rollBack();
            Controller::redirectToResult("Error in selling holdings", "error");
        }
    }
}