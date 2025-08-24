<?php

namespace App\Controllers;

include_once __DIR__ . "/../../core/Controller.php";
include_once __DIR__ . "/../../models/holdings/Repository.php";
include_once __DIR__ . "/../../models/user/Repository.php";
include_once __DIR__ . "/../../core/Files.php";

use App\Core\Controller;
use App\Core\Files;
use App\Models\Repository\HoldingRepository;
use App\Models\Repository\UserRepository;
use Exception;

class HoldingsController 
{
    private HoldingRepository $holdingRepository;
    private UserRepository $userRepository;

    public function __construct() {
        $this->holdingRepository = new HoldingRepository();
        $this->userRepository = new UserRepository();
    }

    public function sell()
    {
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

            Controller::redirectToResult("Successfully deleted holdings", "success");

        } catch(Exception $error) {
            Controller::redirectToResult("Error in selling holdings", "error");
        }
    }
}