<?php

namespace App\Controllers;
include_once __DIR__ . "/Controller.php";
include_once __DIR__ . "/../../models/proposals/Model.php";
include_once __DIR__ . "/../../models/holdings/Model.php";
use App\Controller;
use App\Models\Proposal;
use App\Models\Holding;
use Exception;

class AdminController extends Controller 
{
    public function index()
    {
        $session = parent::redirectIfNotClusterLeader();

        $proposals = Proposal::findProposalByClusterLeader($session['email']);
        parent::render("admin/index", [
            'proposals' => $proposals
        ]);
    }

    public function handleProposalStatusPost() 
    {
        try {
            $postId = filter_input(INPUT_POST, 'post_id', FILTER_SANITIZE_NUMBER_INT);
            $clusterLeaderEmail = filter_input(INPUT_POST, 'cluster_leader_email', FILTER_SANITIZE_SPECIAL_CHARS);
            $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_SPECIAL_CHARS);

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

            parent::redirectToResult('Successfully updated status of proposal', 'success');
        } catch (Exception $error) {
            parent::redirectToResult($error->getMessage(), 'error');
        }
    }

    public function deleteProposal()
    {
        $session = parent::redirectIfNotClusterLeader();

        try {
            $postId = filter_input(INPUT_GET, 'post_id', FILTER_SANITIZE_NUMBER_INT);
            Proposal::deleteProposal($postId, $session['email']);
            parent::redirectToResult('Deleted proposal successfully', 'success');

            echo json_encode([
                'status'=> 'success'
            ]);
        } catch(Exception $error) {
            parent::redirectToResult($error->getMessage(), 'error');
        }
    }
}
