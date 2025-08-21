<?php

namespace App\Models\Repository;
include_once __DIR__ . "/../../core/templates/DbTemplate.php";
include_once __DIR__ . "/Entity.php";

use App\DbTemplate;
use App\Models\Entity\Proposal;

class ProposalRepository 
{
    private DbTemplate $db;

    private string $getProposalByIdQuery = "
        SELECT stock_ticker, stock_name, subject_line, thesis, bid_price, target_price, status, proposal_file, full_name, email
        FROM proposals 
        INNER JOIN users 
            ON proposals.post_author = users.email
        WHERE post_id = ?;
    ";

    private string $insertProposalQuery = "
        INSERT INTO proposals (
        post_author, stock_ticker, stock_name,
        subject_line, thesis, bid_price, target_price, proposal_file, status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?);
    ";

    private string $findProposalByClusterLeaderQuery = "
        SELECT 
            proposals.post_id,
            proposals.post_author, 
            proposals.stock_ticker, 
            proposals.stock_name, 
            proposals.subject_line, 
            proposals.thesis, 
            proposals.bid_price, 
            proposals.target_price, 
            proposals.proposal_file, 
            proposals.status,
            authors.cluster_leader
        FROM proposals 
        INNER JOIN users AS authors 
            ON proposals.post_author = authors.email
        WHERE authors.cluster_leader = ?;
    ";

    private string $updateProposalStatusQuery = "
        UPDATE proposals
        INNER JOIN users ON proposals.post_author = users.email
        SET proposals.status = ?
        WHERE proposals.post_id = ? 
        AND users.cluster_leader = ?;
    ";

    private string $deleteProposalQuery = "
        DELETE proposals 
        FROM proposals
        INNER JOIN users ON proposals.post_author = users.email
        WHERE proposals.post_id = ?
        AND users.cluster_leader = ?;
    ";

    public function __construct() {
        $this->db = new DbTemplate();
    }

    public function save(Proposal $proposal): void
    {
        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare($this->insertProposalQuery);
        $stmt->execute([
            $proposal->post_author,
            $proposal->stock_ticker,
            $proposal->stock_name,
            $proposal->subject_line,
            $proposal->thesis,
            $proposal->bid_price,
            $proposal->target_price,
            $proposal->proposal_file,
            $proposal->status
        ]);
    }

    public function findById(int $id)
    {
        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare($this->getProposalByIdQuery);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findByClusterLeader(string $clusterLeaderEmail)
    {
        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare($this->findProposalByClusterLeaderQuery);
        $stmt->execute([$clusterLeaderEmail]);
        return $stmt->fetchAll();
    }

    public function updateStatus(int $postId, string $clusterLeaderEmail, string $status): bool
    {
        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare($this->updateProposalStatusQuery);
        return $stmt->execute([$status, $postId, $clusterLeaderEmail]);
    }

    public function delete(int $postId, string $clusterLeaderEmail): void
    {
        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare($this->deleteProposalQuery);
        $stmt->execute([$postId, $clusterLeaderEmail]);
    }
}
