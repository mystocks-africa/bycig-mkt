<?php
namespace App\Services;

use App\Core\Templates\DbTemplate;

use App\Models\Proposals\Repository as ProposalRepository;
use App\Models\Holdings\Repository as HoldingRepository;
use App\Models\Holdings\Entity as HoldingEntity;
use Exception;

class AdminService
{
    private DbTemplate $db;
    private ProposalRepository $proposalRepository;
    private HoldingRepository $holdingRepository;


    public function __construct()
    {
        $this->db = new DbTemplate();
        $this->proposalRepository = new ProposalRepository($this->db->getMysqli());
        $this->holdingRepository = new HoldingRepository($this->db->getMysqli());
    }

    public function getProposalsByClusterLeader(string $email): array
    {
        return $this->proposalRepository->findByClusterLeader($email);
    }

    public function processProposalDecision(string $id, string $clusterLeaderEmail, string $status): void 
    {
        try {
            // Start transaction using MySQLi
            $this->db->getMysqli()->begin_transaction();

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

            // Check transaction status and commit using MySQLi
            if ($this->db->getMysqli()->connect_errno === 0) {
                $this->db->getMysqli()->commit();
            }
        }
        catch (Exception $error) {
            // Rollback transaction using MySQLi
            $this->db->getMysqli()->rollback();
            throw $error;
        }
    }
}