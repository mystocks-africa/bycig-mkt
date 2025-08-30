<?php

namespace App\Models\Repository;
include_once __DIR__ . "/Entity.php";

use PDO;
use App\Models\Entity\ProposalEntity;

class ProposalRepository 
{
    private PDO $pdo;

    private string $getProposalByIdQuery = "
        SELECT stock_ticker, stock_name, subject_line, thesis, bid_price, shares, status, proposal_file, full_name, email
        FROM proposals 
        INNER JOIN users 
            ON proposals.post_author = users.email
        WHERE post_id = ?;
    ";

    private string $insertProposalQuery = "
        INSERT INTO proposals (
        post_author, stock_ticker, stock_name,
        subject_line, thesis, bid_price, shares, proposal_file, status
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
            proposals.shares, 
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

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function save(ProposalEntity $proposal): void
    {
        $stmt = $this->pdo->prepare($this->insertProposalQuery);
        $stmt->execute([
            $proposal->post_author,
            $proposal->stock_ticker,
            $proposal->stock_name,
            $proposal->subject_line,
            $proposal->thesis,
            $proposal->bid_price,
            $proposal->shares,
            $proposal->proposal_file,
            $proposal->status
        ]);
    }

    public function findById(int $id)
    {
        $stmt = $this->pdo->prepare($this->getProposalByIdQuery);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findByClusterLeader(string $clusterLeaderEmail)
    {
        $stmt = $this->pdo->prepare($this->findProposalByClusterLeaderQuery);
        $stmt->execute([$clusterLeaderEmail]);
        return $stmt->fetchAll();
    }

    public function updateStatus(int $postId, string $clusterLeaderEmail, string $status): bool
    {
        $stmt = $this->pdo->prepare($this->updateProposalStatusQuery);
        return $stmt->execute([$status, $postId, $clusterLeaderEmail]);
    }

    public function delete(int $postId, string $clusterLeaderEmail): void
    {
        $stmt = $this->pdo->prepare($this->deleteProposalQuery);
        $stmt->execute([$postId, $clusterLeaderEmail]);
    }
}
