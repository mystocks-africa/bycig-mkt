<?php

namespace App\Controllers;

include_once __DIR__ . "/../../core/controller/Controller.php";
include_once __DIR__ . "/../../core/templates/DbTemplate.php";
include_once __DIR__ . "/../../core/files/Files.php";
include_once __DIR__ . "/../../models/holdings/Repository.php";
include_once __DIR__ . "/../../models/user/Repository.php";

use App\Core\Controller;
use App\Core\Files;
use App\DbTemplate;
use App\Models\Repository\HoldingRepository;
use App\Models\Repository\UserRepository;
use Exception;

class HoldingsController 
{
    private HoldingRepository $holdingRepository;
    private UserRepository $userRepository;
    private DbTemplate $db;

    public function __construct() {
        $this->db = new DbTemplate();
        $this->holdingRepository = new HoldingRepository($this->db->getPdo());
        $this->userRepository = new UserRepository($this->db->getPdo());
    }

    public function sell()
    {
        $this->db->getPdo()->beginTransaction();

        try {
            $session = Controller::redirectIfNotAuth(returnSession: true);
            $id = filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT);

            $holding = $this->holdingRepository->findById($id);

            if (!$holding['fulfilled']) {
                Controller::redirectToResult("Holding order needs to be fulfilled before selling", "error");
            }

            $user = $this->userRepository->findByEmail($session['email']);
            $newBalance = $user['balance'] - $user['bought_price'];

            $this->userRepository->updateBalance($newBalance, $session['email']);
            $this->holdingRepository->delete($id, $session['email']);
            Files::deleteFile($holding['proposal_file']);

            $this->db->getPdo()->commit();
            Controller::redirectToResult("Successfully deleted holdings", "success");

        } catch(Exception $error) {
            $this->db->getPdo()->rollBack();
            Controller::redirectToResult("Error in selling holdings", "error");
        }
    }
}