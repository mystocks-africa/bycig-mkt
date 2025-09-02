<?php
namespace App\Models\Proposals;

use App\Core\Templates\DbTemplate;
use mysqli;
use App\Models\Proposals\Entity as ProposalEntity;

class Repository 
{
    private mysqli $mysqli;

    private string $getProposalByIdQuery = "
        SELECT stock_ticker, stock_name, subject_line, thesis, shares, proposal_file, full_name, users.email
        FROM proposals 
        INNER JOIN users 
            ON proposals.post_author = users.email
        WHERE post_id = ?;
    ";

    private string $insertProposalQuery = "
        INSERT INTO proposals (
        post_author, stock_ticker, stock_name,
        subject_line, thesis, shares, proposal_file
        ) VALUES (?, ?, ?, ?, ?, ?, ?);
    ";

    private string $findProposalByClusterLeaderQuery = "
        SELECT 
            proposals.post_id,
            proposals.post_author, 
            proposals.stock_ticker, 
            proposals.stock_name, 
            proposals.subject_line, 
            proposals.thesis, 
            proposals.shares, 
            proposals.proposal_file, 
            authors.cluster_leader
        FROM proposals 
        INNER JOIN users AS authors 
            ON proposals.post_author = authors.email
        WHERE authors.cluster_leader = ?;
    ";

    private string $deleteProposalQuery = "
        DELETE proposals 
        FROM proposals
        INNER JOIN users ON proposals.post_author = users.email
        WHERE proposals.post_id = ?
        AND users.cluster_leader = ?;
    ";

    public function __construct() {
        $this->mysqli = new DbTemplate()->getMysqli();
    }

    public function save(ProposalEntity $proposal): void
    {
        $stmt = $this->mysqli->prepare($this->insertProposalQuery);
        $stmt->bind_param("sssssis",
            $proposal->post_author,
            $proposal->stock_ticker,
            $proposal->stock_name,
            $proposal->subject_line,
            $proposal->thesis,
            $proposal->shares,
            $proposal->proposal_file
        );
        $stmt->execute();
        $stmt->close();
        $this->mysqli->commit();
    }

    public function findById(int $id): mixed
    {
        $stmt = $this->mysqli->prepare($this->getProposalByIdQuery);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        
        $result->free();
        $stmt->close();
        
        return $data ?: false;
    }

    public function findByClusterLeader(string $clusterLeaderEmail): array
    {
        $stmt = $this->mysqli->prepare($this->findProposalByClusterLeaderQuery);
        $stmt->bind_param("s", $clusterLeaderEmail);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        
        $result->free();
        $stmt->close();
        
        return $data;
    }

    public function delete(int $postId, string $clusterLeaderEmail): void
    {
        $stmt = $this->mysqli->prepare($this->deleteProposalQuery);
        $stmt->bind_param("is", $postId, $clusterLeaderEmail);
        $stmt->execute();
        $stmt->close();
        $this->mysqli->commit();
    }
}