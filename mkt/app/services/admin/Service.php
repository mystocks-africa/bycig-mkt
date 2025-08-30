<?php
namespace App\Services;

include_once __DIR__ . "/../../core/templates/DbTemplate.php";

include_once __DIR__ . "/../../models/proposals/Repository.php";
include_once __DIR__ . "/../../models/holdings/Repository.php";
include_once __DIR__ . "/../../models/holdings/Entity.php";

use App\DbTemplate;
use App\Models\Repository\HoldingRepository;
use App\Models\Entity\HoldingEntity;
use App\Models\Repository\ProposalRepository;
use Exception;

class AdminService
{
    private DbTemplate $db;
    private ProposalRepository $proposalRepository;
    private HoldingRepository $holdingRepository;


    public function __construct()
    {
        $this->db = new DbTemplate();
        $this->proposalRepository = new ProposalRepository($this->db->getPdo());
    }

    public function getProposalsByClusterLeader(string $email): array
    {
        return $this->proposalRepository->findByClusterLeader($email);
    }

    public function processProposalDecision(string $postId, string $clusterLeaderEmail, string $status): void 
    {
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
                $proposal['shares'],
                $proposal['proposal_file']
            );

            $this->holdingRepository->save($holdingEntity);
        }
    }

    public function deleteProposalById(int $postId, string $email): void
    {
        $proposal = $this->proposalRepository->findById($postId);
            
        if (empty($proposal)) {
            throw new Exception("Proposal is not found. You are deleting something that doesn't exist");
        }

        $this->proposalRepository->delete($postId, $email);
    }
}