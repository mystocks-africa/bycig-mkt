<?php

namespace App\Models;
include_once __DIR__ . "/../core/Dbh.php";

use App\Dbh;
use Exception;

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
        SELECT stock_ticker, stock_name, subject_line, thesis, bid_price, target_price, proposal_file, full_name 
        FROM proposals 
        WHERE post_id = ?
        INNER JOIN users 
        ON proposals.post_author = users.email;
    ";

    private static string $insertProposalQuery = "
        INSERT INTO proposals (
        post_author, stock_ticker, stock_name,
        subject_line, thesis, bid_price, target_price, proposal_file, status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?);
    ";

    private static string $updateProposalStatusQuery = "
        UPDATE wp_2_proposals
        SET status = ?
        WHERE post_id = ? AND cluster_leader_id = ?
        LIMIT 1;
    ";


    public function __construct (
        string $post_author, 
        string $stock_ticker, 
        string $stock_name, 
        string $subject_line,
        string $thesis, 
        string $bid_price, 
        string $target_price, 
        string $proposal_file
    ) 
    {
        $this->post_author = $post_author;
        $this->stock_ticker = $stock_ticker;
        $this->stock_name = $stock_name;
        $this->subject_line = $subject_line;
        $this->thesis = $thesis;
        $this->bid_price = $bid_price;
        $this->target_price = $target_price;
        $this->proposal_file = $proposal_file;
    }

    public function createProposal() 
    {
        try {
            parent::connect();
            $stmt = parent::$mysqli->prepare(self::$insertProposalQuery);
            $stmt->bind_param(
                'sssssssss',
                $this->post_author,
                $this->stock_ticker,
                $this->stock_name,
                $this->subject_line,
                $this->thesis,
                $this->bid_price,
                $this->target_price,
                $this->proposal_file,
                $this->status
            );
            
            $result = $stmt->execute();
            $stmt->close();

            return $result;
        } catch (Exception $error) {
            return $error->getMessage();
        }

    }

    public static function findAllProposals() 
    {
        try {
            parent::connect();
            $stmt = parent::$mysqli->prepare(self::$getAllProposalQuery);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();

            $rows = [];
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
            
            return $rows;
        } catch (Exception $error) {
            return $error->getMessage();
        }
    }

    public static function findProposalById(int $id) 
    {
        try {
            parent::connect();
            $stmt = parent::$mysqli->prepare(self::$insertProposalQuery);
            $stmt->bind_param(
                "i",
                $id
            );
            $stmt->execute();
            $result = $stmt->get_result();
            $getProposalInfo = $result->fetch_assoc();
            $stmt->close();
            return $getProposalInfo;
        } catch(Exception $error) {
            return $error->getMessage();
        }
    }

    public static function updateProposalStatus() 
    {
        try {
            $stmt = self::$mysqli->prepare(self::$updateProposalStatusQuery);
            $stmt->bind_param("sii", $status, $proposal_id, $cluster_leader_id);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        } catch(Exception $error) {
            return $error->getMessage();
        }

    }
}