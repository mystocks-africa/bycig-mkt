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
        $this->holdingRepository = new HoldingRepository($this->db->getPdo());
    }

    public function getProposalsByClusterLeader(string $email): array
    {
        return $this->proposalRepository->findByClusterLeader($email);
    }

    public function processProposalDecision(string $id, string $clusterLeaderEmail, string $status): void 
    {
        $this->db->getPdo()->beginTransaction();

        try {
            if ($status != 'accept' && $status != 'decline') {
                    throw new Exception('Status is not properly formatted');
            }

            $this->proposalRepository->updateStatus($id, $clusterLeaderEmail, $status);


            if ($status == 'accept') {
                $proposalWithUser = $this->proposalRepository->findById($id);
                $holdingEntity = new HoldingEntity(
                    $proposalWithUser['email'],
                    $proposalWithUser['stock_ticker'],
                    $proposalWithUser['stock_name'],
                    $proposalWithUser['bid_price'],
                    $proposalWithUser['shares'],
                    $proposalWithUser['proposal_file']
                );

                $this->holdingRepository->save($holdingEntity);
            }

            $this->db->getPdo()->commit();
        }
        catch (Exception $error) {
            $this->db->getPdo()->rollBack();
        }
    }

    public function deleteProposalById(int $id, string $email): void
    {
        $proposal = $this->proposalRepository->findById($id);
            
        if (empty($proposal)) {
            throw new Exception("Proposal is not found. You are deleting something that doesn't exist");
        }

        $this->proposalRepository->delete($id, $email);
    }
}