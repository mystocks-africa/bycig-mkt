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

    private function uploadToFTP($file) 
    {
        $ftp_conn = ftp_connect($this->env["FTP_SERVER"]) or throw new Exception("Failed to connect");
        ftp_login($ftp_conn, $this->env["FTP_USER"], $this->env["FTP_PASS"]);

        $filename = "uploads/" . bin2hex(random_bytes(5)) . ".pdf";
        $result = ftp_put($ftp_conn, $filename, $file["tmp_name"], FTP_BINARY);
        ftp_close($ftp_conn);
        return $result ? $filename : false;
    }

    // public because it is needed in admin controller
    public function deleteFromFTP($fileName)
    {
        $ftp_conn = ftp_connect($this->env["FTP_SERVER"]) or throw new Exception("Failed to connect");
        
        if (!ftp_login($ftp_conn, $this->env["FTP_USER"], $this->env["FTP_PASS"])) {
            ftp_close($ftp_conn);
            throw new Exception("FTP login failed");
        }

        if (!ftp_delete($ftp_conn, $fileName)) {
            ftp_close($ftp_conn);
            throw new Exception("Failed to delete file: $fileName");
        }

        ftp_close($ftp_conn);
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

            $fileName = $this->uploadToFTP($proposalFile);

            if (!$stockTicker || !$stockName || !$subjectLine || !$thesis || !$bidPrice || !$targetPrice || !$fileName ) {
                Controller::redirectToResult("Error in form input", "error");
                exit();
            }

            $proposal = new Proposal($session["email"], $stockTicker, $stockName, $subjectLine, $thesis, $bidPrice, $targetPrice, $fileName);
            $proposal->createProposal();

            if ($proposal && $proposal instanceof Exception) {
                Controller::redirectToResult("Error in submitting proposal", "error");
                exit();
            }

            Controller::redirectToResult("Success in submitting proposal", "success");
        } catch (Exception $error) {
            // Delete the file if there was an error to avoid orphaned files
            if (isset($fileName)) {
                $this->deleteFromFTP($fileName);
            }
            
            Controller::redirectToResult($error->getMessage(), "error");
        }
    }
}