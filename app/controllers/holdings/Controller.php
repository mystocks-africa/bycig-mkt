<?php

namespace App\Controllers;

include_once __DIR__ . "/../../core/Controller.php";
include_once __DIR__ . "/../../models/holdings/Repository.php";

use App\Core\Controller;
use App\Models\Repository\HoldingRepository;

use Exception;

class HoldingsController 
{
    private HoldingRepository $holdingRepository;

    public function __construct() {
        $this->holdingRepository = new HoldingRepository();
    }

    public function delete()
    {
        try {
            $session = Controller::redirectIfNotAuth(returnSession: true);
            $id = filter_input(INPUT_GET, "id", FILTER_SANITIZE_NUMBER_INT);

            // Query for email as well so only the owner can delete
            $this->holdingRepository->delete($id, $session['email']);
            
            echo json_encode([
                'status'=> 'success'
            ]);
        } catch(Exception $error) {
            echo json_encode([
                'status'=> 'error',
                'error'=> $error->getMessage(),
            ]);        
        }
    }
}