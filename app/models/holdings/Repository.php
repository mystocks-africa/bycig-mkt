<?php

namespace App\Model\Repo;
include_once __DIR__ . "/../../core/templates/DbTemplate.php";
include_once __DIR__ . "/../../models/holdings/Entity.php";

use App\DbTemplate;
use App\Model\Entity\Holding;

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

    private string $deleteHoldingQuery = "
        DELETE 
        FROM holdings 
        WHERE id = ? 
        AND investor = ?
    ";

    public function __construct() {
        $this->db = new DbTemplate();
    }
    public function createHolding(Holding $holding): void
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

    public function findAllHoldings(): array
    {
        $pdo = $this->db->getConnection();

        $stmt = $pdo->prepare($this->findAllHoldingsQuery);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function deleteHolding(int $id, string $email): void
    {
        $pdo = $this->db->getConnection();

        $stmt = $pdo->prepare($this->deleteHoldingQuery);
        $stmt->execute([$id, $email]);
    }
}
