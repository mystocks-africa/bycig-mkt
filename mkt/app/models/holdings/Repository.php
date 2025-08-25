<?php

namespace App\Models\Repository;
include_once __DIR__ . "/../../core/templates/DbTemplate.php";
include_once __DIR__ . "/Entity.php";

use App\DbTemplate;
use App\Models\Entity\HoldingEntity;

class HoldingRepository
{
    private DbTemplate $db;

    private string $insertHoldingQuery = "
        INSERT INTO holdings 
        (investor, stock_ticker, stock_name, bid_price, target_price, proposal_file) 
        VALUES (?, ?, ?, ?, ?, ?)
    ";
    
    private string $findAllHoldingsQuery = "
        SELECT
            id,
            stock_ticker,
            stock_name,
            investor,
            proposal_file
        FROM holdings
    ";

    private string $findByEmailQuery = "
        SELECT 
            id,
            stock_ticker,
            stock_name,
            investor, 
            proposal_file
        FROM holdings
        WHERE investor = ?
    ";

    private string $deleteHoldingQuery = "
        DELETE 
        FROM holdings 
        WHERE id = ? 
        AND investor = ?
    ";

    private string $findByIdQuery = "
        SELECT 
            id,
            stock_ticker,
            stock_name,
            investor,
            proposal_file,
            bid_price
        FROM holdings
        WHERE id = ?
    ";

    private string $deleteAllHoldingsQuery = "
        DELETE FROM holdings 
        WHERE investor = ?;
    ";

    public function __construct() {
        $this->db = new DbTemplate();
    }
    public function save(HoldingEntity $holding): void
    {
        $pdo = $this->db->getConnection();

        $stmt = $pdo->prepare($this->insertHoldingQuery);
        $stmt->execute([
            $holding->investor,
            $holding->stock_ticker,
            $holding->stock_name,
            $holding->bid_price,
            $holding->target_price, 
            $holding->proposal_file
        ]);
    }

    public function findAll(): array
    {
        $pdo = $this->db->getConnection();

        $stmt = $pdo->prepare($this->findAllHoldingsQuery);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findByEmail($email): array 
    {
        $pdo = $this->db->getConnection();

        $stmt = $pdo->prepare($this->findByEmailQuery);
        $stmt->execute([
            $email
        ]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findById($id): mixed 
    {
        $pdo = $this->db->getConnection();

        $stmt = $pdo->prepare($this->findByIdQuery);
        $stmt->execute([
            $id
        ]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function delete(int $id, string $email): void
    {
        $pdo = $this->db->getConnection();

        $stmt = $pdo->prepare($this->deleteHoldingQuery);
        $stmt->execute([$id, $email]);
    }

    public function deleteAllHoldings(string $email)
    {
        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare($this->deleteAllHoldingsQuery);

        // The investor field is a foriegn key to user's email (primary key of user)
        $stmt->execute([
            $email
        ]);
    }
}
