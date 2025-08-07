<?php

namespace App\Models;
include_once __DIR__ . "/../../core/Dbh.php";

use App\Dbh;

class Proposal extends Dbh {
    private string $post_author;
    private string $stock_ticker;
    private string $stock_name;
    private string $subject_line;
    private string $thesis;
    private string $bid_price;
    private string $target_price;
    private string $proposal_file;
    private string $status = "pending";

    private static string $getAllProposalQuery = "
        SELECT post_id, subject_line, email, full_name 
        FROM proposals 
        INNER JOIN users 
        ON proposals.post_author = users.email;
    ";

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
        WHERE proposals.post_id = ?;
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

    public function insert(): void {
        $stmt = $this->connect()->prepare(self::$insertProposalQuery);
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

    public static function getAll(): array {
        $stmt = (new self('', '', '', '', '', '', '', ''))->connect()->prepare(self::$getAllProposalQuery);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function getById(string $id): array {
        $stmt = (new self('', '', '', '', '', '', '', ''))->connect()->prepare(self::$getProposalByIdQuery);
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public static function getByClusterLeader(string $email): array {
        $stmt = (new self('', '', '', '', '', '', '', ''))->connect()->prepare(self::$findProposalByClusterLeaderQuery);
        $stmt->execute([$email]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function updateStatus(string $status, string $post_id): void {
        $stmt = (new self('', '', '', '', '', '', '', ''))->connect()->prepare(self::$updateProposalStatusQuery);
        $stmt->execute([$status, $post_id]);
    }
}
