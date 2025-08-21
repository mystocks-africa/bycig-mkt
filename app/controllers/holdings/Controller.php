<?php

namespace App\Controllers;

include_once __DIR__ . "/../../core/Controller.php";
include_once __DIR__ . "/../../models/holdings/Model.php";

use App\Core\Controller;
use App\Models\Holding;
use Exception;

class HoldingsController 
{
    public function delete()
    {
        try {
            $session = Controller::redirectIfNotAuth(returnSession: true);
            $id = filter_input(INPUT_GET, "id", FILTER_SANITIZE_NUMBER_INT);

            // Query for email as well so only the owner can delete
            Holding::deleteHolding($id, $session['email']);
            
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