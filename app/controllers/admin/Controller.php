<?php

namespace App\Controllers;
include_once __DIR__ . "/Controller.php";
include_once __DIR__ . "/../../models/proposals/Model.php";

use App\Controller;
use App\Models\Proposal;
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

    public function updateProposalStatusPost() 
    {
        try {
            $postId = filter_input(INPUT_POST, 'post_id', FILTER_SANITIZE_SPECIAL_CHARS);
            $clusterLeaderEmail = filter_input(INPUT_POST, 'cluster_leader_email', FILTER_SANITIZE_EMAIL);
            $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_SPECIAL_CHARS);

            if ($status != 'accept' && $status != 'decline') {
                $msg = 'Status is not properly formatted';
                throw new Exception($msg);
            }

            Proposal::updateProposalStatus($postId, $clusterLeaderEmail, $status); 

            $msg = 'Successfully updated status of proposal';
            parent::redirectToResult($msg, 'success');
        } catch (Exception $error) {
            parent::redirectToResult($error->getMessage(), 'error');
        }

    }
}