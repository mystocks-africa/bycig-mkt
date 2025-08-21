<?php

namespace App\Controllers;

include_once __DIR__ . "/../../core/Controller.php";
include_once __DIR__ . "/../../models/proposals/Model.php";
include_once __DIR__ . "/../../../utils/env.php";

use App\Core\Controller;
use App\Models\Proposal;
use Exception;

class ProposalController
{
    private $env;

    public function __construct() 
    {
        global $env;
        $this->env = $env; 
    }

    private function uploadFile($file) 
    {
        $uploadDir = __DIR__ . "/../../../public/uploads/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $filename = bin2hex(random_bytes(5)) . ".pdf";
        $fullPath = $uploadDir . $filename;
        
        return move_uploaded_file($file["tmp_name"], $fullPath) ? $filename : false;
    }

    // public because it is needed in admin controller
    public function deleteFile($fileName)
    {
        $fullPath = __DIR__ . "/../../../public/uploads/" . $fileName;
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }

    public function submit() 
    {
        Controller::redirectIfNotAuth();
        Controller::render("proposal/submit");
    }

    public function submitPost() 
    {
        try {
            $session = Controller::redirectIfNotAuth(true);
            
            $stockTicker = filter_input(INPUT_POST, 'stock_ticker', FILTER_SANITIZE_SPECIAL_CHARS);
            $stockName = filter_input(INPUT_POST, 'stock_name', FILTER_SANITIZE_SPECIAL_CHARS);
            $subjectLine = filter_input(INPUT_POST, 'subject_line', FILTER_SANITIZE_SPECIAL_CHARS);
            $thesis = filter_input(INPUT_POST, 'thesis', FILTER_SANITIZE_SPECIAL_CHARS);
            $bidPrice = filter_input(INPUT_POST, 'bid_price', FILTER_VALIDATE_FLOAT);
            $targetPrice = filter_input(INPUT_POST, 'target_price', FILTER_VALIDATE_FLOAT);
            $proposalFile = $_FILES["proposal_file"] ?? null;

            $fileName = $this->uploadFile($proposalFile);

            if (!$stockTicker || !$stockName || !$subjectLine || !$thesis || !$bidPrice || !$targetPrice || !$fileName ) {
                Controller::redirectToResult("Error in form input", "error");
                exit();
            }

            $proposal = new Proposal(
                $session["email"], 
                $stockTicker, 
                $stockName, 
                $subjectLine, 
                $thesis, 
                $bidPrice, 
                $targetPrice, 
                $fileName
            );
            $proposal->createProposal();

            if ($proposal && $proposal instanceof Exception) {
                Controller::redirectToResult("Error in submitting proposal", "error");
                exit();
            }

            Controller::redirectToResult("Success in submitting proposal", "success");
        } catch (Exception $error) {
            // Delete the file if there was an error to avoid orphaned files
            if (isset($fileName)) {
                $this->deleteFile($fileName);
            }
            
            Controller::redirectToResult($error->getMessage(), "error");
        }
    }
}
