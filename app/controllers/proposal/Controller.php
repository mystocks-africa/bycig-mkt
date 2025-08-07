<?php

namespace App\Controllers;
include_once __DIR__ . "/../Controller.php";
include_once __DIR__ . "/../../models/proposals/Model.php";
include_once __DIR__ . "/../../../utils/env.php";

use App\Controller;
use App\Models\Proposal;

class ProposalController extends Controller {

    private $env;

    public function __construct() 
    {
        global $env;
        $this->env = $env; 
    }

    private function uploadToFTP($file) 
    {
        $ftp_conn = ftp_connect($this->env["FTP_SERVER"]) or die("FTP error");
        ftp_login($ftp_conn, $this->env["FTP_USER"], $this->env["FTP_PASS"]);

        $filename = "uploads/" . bin2hex(random_bytes(5)) . ".pdf";
        $result = ftp_put($ftp_conn, $filename, $file["tmp_name"], FTP_BINARY);
        ftp_close($ftp_conn);
        return $result ? $filename : false;
    }

    public function submit() 
    {
        parent::redirectIfNotAuth();

        parent::render("proposal/submit");
    }

    public function proposalDetails() 
    {    
        $postId = filter_input(INPUT_GET,"post_id", FILTER_SANITIZE_NUMBER_INT);
        $proposal = Proposal::findProposalById($postId);

        parent::render("proposal/details", [
            "proposal" => $proposal
        ]);
    }
    
    public function submitPost() 
    {
        $session = parent::redirectIfNotAuth(true);
        

        $stockTicker = filter_input(INPUT_POST, 'stock_ticker', FILTER_SANITIZE_SPECIAL_CHARS);
        $stockName = filter_input(INPUT_POST, 'stock_name', FILTER_SANITIZE_SPECIAL_CHARS);
        $subjectLine = filter_input(INPUT_POST, 'subject_line', FILTER_SANITIZE_SPECIAL_CHARS);
        $thesis = filter_input(INPUT_POST, 'thesis', FILTER_SANITIZE_SPECIAL_CHARS);
        $bidPrice = filter_input(INPUT_POST, 'bid_price', FILTER_VALIDATE_FLOAT);
        $targetPrice = filter_input(INPUT_POST, 'target_price', FILTER_VALIDATE_FLOAT);
        $proposalFile = $_FILES["proposal_file"] ?? null;

        $fileName = $this->uploadToFTP($proposalFile);

        if (!$stockTicker || !$stockName || !$subjectLine || !$thesis || !$bidPrice || !$targetPrice || !$fileName ) {
            parent::redirectToResult("Error in form input", "error");
            exit();
        }

        $proposal = new Proposal($session["email"], $stockTicker, $stockName, $subjectLine, $thesis, $bidPrice, $targetPrice, $fileName);
        $proposal->createProposal();

        if ($proposal && $proposal instanceof Exception) {
            parent::redirectToResult("Error in submitting proposal", "error");
            exit();
        }

        parent::redirectToResult("Success in submitting proposal", "success");
    }
}
