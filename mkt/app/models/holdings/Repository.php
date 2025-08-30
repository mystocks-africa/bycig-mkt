<?php

namespace App\Models\Repository;
include_once __DIR__ . "/Entity.php";

use PDO;
use App\Models\Entity\HoldingEntity;

class HoldingRepository
{
    private PDO $pdo;

    private string $insertHoldingQuery = "
        INSERT INTO holdings 
        (investor, stock_ticker, stock_name, bid_price, shares) 
        VALUES (?, ?, ?, ?, ?);
    ";
    
    private string $findAllHoldingsQuery = "
        SELECT
            id,
            stock_ticker,
            stock_name,
            investor
        FROM holdings;
    ";

    private string $findByEmailQuery = "
        SELECT 
            id,
            stock_ticker,
            stock_name,
            investor
        FROM holdings
        WHERE investor = ?;
    ";

    private string $deleteHoldingQuery = "
        DELETE 
        FROM holdings 
        WHERE id = ?;
    ";

    private string $findByIdQuery = "
        SELECT 
            id,
            stock_ticker,
            stock_name,
            investor,
            bid_price,
            fulfilled
        FROM holdings
        WHERE id = ?;
    ";

    private string $deleteAllHoldingsQuery = "
        DELETE 
        FROM holdings 
        WHERE investor = ?;
    ";

    private string $updateFulfillOrder = "
        UPDATE holdings 
        SET fulfilled = true
        WHERE id = ?;
    ";

    public function __construct(PDO $pdo) 
    {
        $this->pdo = $pdo;
    }

    public function save(HoldingEntity $holding): void
    {
        $stmt = $this->pdo->prepare($this->insertHoldingQuery);
        $stmt->execute([
            $holding->investor,
            $holding->stock_ticker,
            $holding->stock_name,
            $holding->bid_price,
            $holding->shares, 
        ]);
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->prepare($this->findAllHoldingsQuery);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByEmail(string $email): array 
    {
        $stmt = $this->pdo->prepare($this->findByEmailQuery);
        $stmt->execute([
            $email
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $id): mixed 
    {
        $stmt = $this->pdo->prepare($this->findByIdQuery);
        $stmt->execute([
            $id
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare($this->deleteHoldingQuery);
        $stmt->execute([
            $id,
        ]);
    }

    public function deleteAllHoldings(string $email): void
    {        
        $stmt = $this->pdo->prepare($this->deleteAllHoldingsQuery);

        // The investor field is a foriegn key to user's email (primary key of user)
        $stmt->execute([
            $email
        ]);
    }

    public function fulfillOrder(int $id): void
    {
        $stmt = $this->pdo->prepare($this->updateFulfillOrder);
        $stmt->execute([
            $id
        ]);
    }
}