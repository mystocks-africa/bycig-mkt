<?php

namespace App\Controllers;
include_once __DIR__ . "/../../core/auth/Checker.php";
include_once __DIR__ . "/../../core/controller-helper/Controller.php";
include_once __DIR__ . "/../../../utils/env.php";

use App\Core\Auth\Checker;
use App\Core\ControllerHelper;
use App\Models\ProposalModel;
use Exception;

class ProposalController {

    private $env;
    private $authChecker;
    private $controllerHelper;

    public function __construct() 
    {
        global $env;
        $this->env = $env; 
        $this->authChecker = new Checker();
        $this->controllerHelper = new ControllerHelper();
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
        $this->authChecker->redirectIfNotAuth();
        $this->controllerHelper->render("proposal/submit");
    }

    public function submitPost() 
    {
        try {
            $session = $this->authChecker->redirectIfNotAuth(true);

            $stockTicker = filter_input(INPUT_POST, 'stock_ticker', FILTER_SANITIZE_SPECIAL_CHARS);
            $stockName = filter_input(INPUT_POST, 'stock_name', FILTER_SANITIZE_SPECIAL_CHARS);
            $bidPrice = filter_input(INPUT_POST, 'bid_price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $targetPrice = filter_input(INPUT_POST, 'target_price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $subjectLine = filter_input(INPUT_POST, 'subject_line', FILTER_SANITIZE_SPECIAL_CHARS);
            $thesis = filter_input(INPUT_POST, 'thesis', FILTER_SANITIZE_SPECIAL_CHARS);

            $file = $_FILES["proposal_file"];

            if (!isset($file) || $file["error"] !== UPLOAD_ERR_OK) {
                throw new Exception("File upload error.");
            }

            $filePath = $this->uploadToFTP($file);
            if (!$filePath) {
                throw new Exception("Failed to upload proposal file to server.");
            }

            $proposal = new ProposalModel(
                $session["email"],
                $stockTicker,
                $stockName,
                $bidPrice,
                $targetPrice,
                $filePath
            );

            $proposal->setSubjectLine($subjectLine);
            $proposal->setThesis($thesis);
            $proposal->setClusterLeaderEmail($session["cluster_leader"]);

            $proposal->createProposal();

            $this->controllerHelper->redirectToResult("Proposal has been submitted!", "success");
        } catch (Exception $error) {
            $this->controllerHelper->redirectToResult("Something went wrong: " . $error->getMessage(), "error");
        }
    }
}
