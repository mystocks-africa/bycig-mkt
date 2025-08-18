<?php

namespace App\Controllers;

include_once __DIR__ . "/../../core/Controller.php";
include_once __DIR__ . "/../../models/proposals/Model.php";
include_once __DIR__ . "/../../models/holdings/Model.php";

use App\Core\Controller;
use App\Models\Proposal;
use App\Models\Holding;
use Exception;

class AdminController 
{
    public function index()
    {
        $session = Controller::redirectIfNotClusterLeader();

        $proposals = Proposal::findProposalByClusterLeader($session['email']);
        Controller::render("admin/index", [
            'proposals' => $proposals
        ]);
    }

    // Sending JSON in these 2 routes because fetch api in js is dealing w/ them
    public function handleProposalStatusPost() 
    {
        try {
            $postId = filter_input(INPUT_GET, 'post_id', FILTER_SANITIZE_NUMBER_INT);
            $clusterLeaderEmail = filter_input(INPUT_GET, 'cluster_leader_email', FILTER_SANITIZE_SPECIAL_CHARS);
            $status = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_SPECIAL_CHARS);

            if ($status != 'accept' && $status != 'decline') {
                throw new Exception('Status is not properly formatted');
            }

            Proposal::updateProposalStatus($postId, $clusterLeaderEmail, $status);

            // Keep proposal even when a new holding w/ proposal info is created
            // Cluster leader has to delete it themselves in admin portal 

            if ($status == 'accept') {
                $proposal = Proposal::findProposalById($postId);
                $holding = new Holding(
                    $proposal['email'],
                    $proposal['stock_ticker'],
                    $proposal['stock_name'],
                    $proposal['bid_price'],
                    $proposal['target_price'],
                    $proposal['proposal_file'],
                );
                $holding->createHolding();
            }

            echo json_encode([
                'status'=> 'success',
            ]);
        } catch (Exception $error) {
            echo json_encode([
                'status'=> 'error',
                'error'=> $error->getMessage(),
            ]);
        }
    }

    public function deleteProposal()
    {
        $session = Controller::redirectIfNotClusterLeader();
        try {
            $postId = filter_input(INPUT_GET, 'post_id', FILTER_SANITIZE_NUMBER_INT);
            $proposal = Proposal::findProposalById($postId);
            
            if (empty($proposal)) {
                throw new Exception("Proposal is not found. You are deleting something that doesn't exist");
            }

            Proposal::deleteProposal($postId, $session['email']);
            Controller::redirectToResult('Deleted proposal successfully', 'success');

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