<?php

namespace App\Controllers;

include_once __DIR__ . "/../../core/controller/Controller.php";
include_once __DIR__ . "/../../core/templates/DbTemplate.php";
include_once __DIR__ . "/../../core/auth/Guard.php";

include_once __DIR__ . "/../../models/proposals/Repository.php";
include_once __DIR__ . "/../../models/holdings/Repository.php";
include_once __DIR__ . "/../../models/holdings/Entity.php";

use App\Core\Controller;
use App\Core\Session;
use App\Core\Auth\AuthGuard;

use App\DbTemplate;
use App\Models\Repository\HoldingRepository;
use App\Models\Entity\HoldingEntity;
use App\Models\Repository\ProposalRepository;
use Exception;

class AdminController 
{
    private HoldingRepository $holdingRepository;
    private ProposalRepository $proposalRepository;
    private DbTemplate $db;
    private Session $session;
    private AuthGuard $authGuard;

    public function __construct() {
        $this->db = new DbTemplate();
        $this->holdingRepository = new HoldingRepository($this->db->getPdo());
        $this->proposalRepository = new ProposalRepository($this->db->getPdo());
        $this->session = new Session();
        $this->authGuard = new AuthGuard($this->session);
    }

    public function index()
    {
        $this->authGuard->redirectIfNotAuth();

        $proposals = $this->proposalRepository->findByClusterLeader($this->session->getSession()['email']);
        Controller::render("admin/index", [
            'proposals' => $proposals
        ]);
    }

    public function handleProposalStatusPost() 
    {
        $this->authGuard->redirectIfNotAuth();

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

            Controller::redirectToResult('Posted proposal successfully', 'success');
        } catch (Exception $error) {
            Controller::redirectToResult('Error in posting proposal', 'error');
        }
    }

    public function deleteProposal()
    {
        $this->authGuard->redirectIfNotClusterLeader();

        try {
            $postId = filter_input(INPUT_GET, 'post_id', FILTER_SANITIZE_NUMBER_INT);
            $proposal = $this->proposalRepository->findById($postId);
            
            if (empty($proposal)) {
                throw new Exception("Proposal is not found. You are deleting something that doesn't exist");
            }

            $this->proposalRepository->delete($postId, $this->session->getSession()['email']);
            Controller::redirectToResult('Deleted proposal successfully', 'success');
        } catch(Exception $error) {
            Controller::redirectToResult('Error in deleting proposal', 'error');       
        }
    }
}
