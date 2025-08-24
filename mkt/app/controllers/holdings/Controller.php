<?php

namespace App\Controllers;

include_once __DIR__ . "/../../core/Controller.php";
include_once __DIR__ . "/../../models/holdings/Repository.php";
include_once __DIR__ . "/../../core/Files.php";

use App\Core\Controller;
use App\Core\Files;
use App\Models\Repository\HoldingRepository;

use Exception;

class HoldingsController 
{
    private HoldingRepository $holdingRepository;

    public function __construct() {
        $this->holdingRepository = new HoldingRepository();
    }

    public function sellAll()
    {
        try {
            $session = Controller::redirectIfNotAuth(returnSession: true);
            $id = filter_input(INPUT_GET, "id", FILTER_SANITIZE_NUMBER_INT);

            $holding = $this->holdingRepository->findById($id);
            
            $this->holdingRepository->delete($id, $session['email']);
            
            Files::deleteFile($holding['proposal_file']);

            Controller::redirectToResult("Successfully deleted holdings", "success");

        } catch(Exception $error) {
            Controller::redirectToResult("Error in deleting holdings", "error");
        }
    }
}