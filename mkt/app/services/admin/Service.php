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
        try {
            $this->db->getPdo()->beginTransaction();

            if ($status != 'accept' && $status != 'decline') {
                    throw new Exception('Status is not properly formatted');
            }

            if ($status == 'accept') {
                $proposalWithUser = $this->proposalRepository->findById($id);
                $holdingEntity = new HoldingEntity(
                    $proposalWithUser['email'],
                    $proposalWithUser['stock_ticker'],
                    $proposalWithUser['stock_name'],
                    $proposalWithUser['shares'],
                );

                $this->holdingRepository->save($holdingEntity);
            }

            $this->proposalRepository->delete($id, $clusterLeaderEmail);

            if ($this->db->getPdo()->inTransaction()) {
                $this->db->getPdo()->commit();
            }
        }
        catch (Exception $error) {
            if ($this->db->getPdo()->inTransaction()) {
                $this->db->getPdo()->rollBack();
            }
            throw $error;
        }
    }
}