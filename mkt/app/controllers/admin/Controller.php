<?php

namespace App\Controllers;

include_once __DIR__ . "/../../core/Controller.php";
include_once __DIR__ . "/../../models/proposals/Repository.php";
include_once __DIR__ . "/../../models/holdings/Repository.php";
include_once __DIR__ . "/../../models/holdings/Entity.php";

use App\Core\Controller;
use App\Models\Repository\HoldingRepository;
use App\Models\Entity\HoldingEntity;
use App\Models\Repository\ProposalRepository;
use Exception;

class AdminController 
{
    private HoldingRepository $holdingRepository;
    private ProposalRepository $proposalRepository;

    public function __construct() {
        $this->holdingRepository = new HoldingRepository();
        $this->proposalRepository = new ProposalRepository();
    }

    public function index()
    {
        $session = Controller::redirectIfNotClusterLeader();

        $proposals = $this->proposalRepository->findByClusterLeader($session['email']);
        Controller::render("admin/index", [
            'proposals' => $proposals
        ]);
    }

    public function handleProposalStatusPost() 
    {
        try {
            $postId = filter_input(INPUT_GET, 'post_id', FILTER_SANITIZE_NUMBER_INT);
            $clusterLeaderEmail = filter_input(INPUT_GET, 'cluster_leader_email', FILTER_SANITIZE_SPECIAL_CHARS);
            $status = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_SPECIAL_CHARS);

            if ($status != 'accept' && $status != 'decline') {
                throw new Exception('Status is not properly formatted');
            }

            $this->proposalRepository->updateStatus($postId, $clusterLeaderEmail, $status);


            if ($status == 'accept') {
                $proposal = $this->proposalRepository->findById($postId);
                $holdingEntity = new HoldingEntity(
                    $proposal['email'],
                    $proposal['stock_ticker'],
                    $proposal['stock_name'],
                    $proposal['bid_price'],
                    $proposal['target_price'],
                    $proposal['proposal_file']
                );

                $this->holdingRepository->save($holdingEntity);
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
            $proposal = $this->proposalRepository->findById($postId);
            
            if (empty($proposal)) {
                throw new Exception("Proposal is not found. You are deleting something that doesn't exist");
            }

            $this->proposalRepository->delete($postId, $session['email']);
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