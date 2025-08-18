<?php

namespace App\Models;
include_once __DIR__ . "/../../core/DbTemplate.php";

use App\DbTemplate;

class Proposal extends DbTemplate 
{
    private string $post_author;
    private string $stock_ticker;
    private string $stock_name;
    private string $subject_line;
    private string $thesis;
    private string $bid_price;
    private string $target_price;
    private string $proposal_file;
    private string $status = "pending";
    
    private static string $getProposalByIdQuery = "
        SELECT stock_ticker, stock_name, subject_line, thesis, bid_price, target_price, status, proposal_file, full_name, email
        FROM proposals 
        INNER JOIN users 
            ON proposals.post_author = users.email
        WHERE post_id = ?;
    ";

    private static string $insertProposalQuery = "
        INSERT INTO proposals (
        post_author, stock_ticker, stock_name,
        subject_line, thesis, bid_price, target_price, proposal_file, status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?);
    ";

    private static string $findProposalByClusterLeaderQuery = "
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

    private static string $updateProposalStatusQuery = "
        UPDATE proposals
        INNER JOIN users ON proposals.post_author = users.email
        SET proposals.status = ?
        WHERE proposals.post_id = ? 
        AND users.cluster_leader = ?;
    ";

    private static string $deleteProposalQuery = "
        DELETE proposals 
        FROM proposals
        INNER JOIN users ON proposals.post_author = users.email
        WHERE proposals.post_id = ?
        AND users.cluster_leader = ?;
    ";

    
    public function __construct(
        string $post_author,
        string $stock_ticker,
        string $stock_name,
        string $subject_line,
        string $thesis,
        string $bid_price,
        string $target_price,
        string $proposal_file
    ) {
        $this->post_author = $post_author;
        $this->stock_ticker = $stock_ticker;
        $this->stock_name = $stock_name;
        $this->subject_line = $subject_line;
        $this->thesis = $thesis;
        $this->bid_price = $bid_price;
        $this->target_price = $target_price;
        $this->proposal_file = $proposal_file;
    }

    public function createProposal(): void {
        parent::connect();
        $stmt = parent::$mysqli->prepare(self::$insertProposalQuery);
        $stmt->execute([
            $this->post_author,
            $this->stock_ticker,
            $this->stock_name,
            $this->subject_line,
            $this->thesis,
            $this->bid_price,
            $this->target_price,
            $this->proposal_file,
            $this->status
        ]);
    }

    public static function findProposalById(int $id) 
    {
        parent::connect();
        $stmt = parent::$mysqli->prepare(self::$getProposalByIdQuery);
        $stmt->bind_param(
            "i",
            $id
        );
        $stmt->execute();
        $result = $stmt->get_result();
        $getProposalInfo = $result->fetch_assoc();
        $stmt->close();
        return $getProposalInfo;
    }

    public static function findProposalByClusterLeader($clusterLeaderEmail) 
    {
        parent::connect();

        $stmt = parent::$mysqli->prepare(self::$findProposalByClusterLeaderQuery);
        $stmt->bind_param("s", $clusterLeaderEmail);
        $stmt->execute();
        $result = $stmt->get_result();
        $proposals = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $proposals;
    }

    public static function deleteProposal( $postId, $clusterLeaderEmail )
    {
        parent::connect();

        $stmt = parent::$mysqli->prepare(self::$deleteProposalQuery);
        $stmt->bind_param("is", $postId, $clusterLeaderEmail);
        $stmt->execute();
        $stmt->close();
    }

    public static function updateProposalStatus($postId, $clusterLeaderEmail, $status) 
    {
        parent::connect();
        $stmt = parent::$mysqli->prepare(self::$updateProposalStatusQuery);
        $stmt->bind_param("sis", $status, $postId, $clusterLeaderEmail);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
}