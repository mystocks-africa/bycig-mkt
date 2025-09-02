<?php
namespace App\Models\Holdings;

use mysqli;
use App\Models\Holdings\Entity as HoldingEntity;
use App\Core\Templates\DbTemplate;

class Repository
{
    private mysqli $mysqli;

    private string $insertHoldingQuery = "
        INSERT INTO holdings 
        (investor, stock_ticker, stock_name, shares) 
        VALUES (?, ?, ?, ?);
    ";
    
    private string $findAllHoldingsQuery = "
        SELECT
            id,
            stock_ticker,
            stock_name,
            investor,
            shares,
            fulfilled
        FROM holdings;
    ";

    private string $findByEmailQuery = "
        SELECT 
            id,
            stock_ticker,
            stock_name,
            investor,
            fulfilled,
            shares
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
            fulfilled,
            shares
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

    public function __construct() 
    {
        $this->mysqli = new DbTemplate()->getMysqli();
    }

    public function save(HoldingEntity $holding): void
    {
        $stmt = $this->mysqli->prepare($this->insertHoldingQuery);
        $stmt->bind_param("sssi", 
            $holding->investor,
            $holding->stock_ticker,
            $holding->stock_name,
            $holding->shares
        );
        $stmt->execute();
        $stmt->close();
        $this->mysqli->commit();
    }

    public function findAll(): array
    {
        $result = $this->mysqli->query($this->findAllHoldingsQuery);
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $result->free();
        return $data;
    }

    public function findByEmail(string $email): array 
    {
        $stmt = $this->mysqli->prepare($this->findByEmailQuery);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        
        $result->free();
        $stmt->close();
        
        return $data;
    }

    public function findById(int $id): mixed 
    {
        $stmt = $this->mysqli->prepare($this->findByIdQuery);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        
        $result->free();
        $stmt->close();
        
        return $data ?: false;
    }

    public function delete(int $id): void
    {
        $stmt = $this->mysqli->prepare($this->deleteHoldingQuery);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        $this->mysqli->commit();
    }

    public function deleteAllHoldings(string $email): void
    {        
        $stmt = $this->mysqli->prepare($this->deleteAllHoldingsQuery);
        
        // The investor field is a foreign key to user's email (primary key of user)
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->close();
        $this->mysqli->commit();
    }

    public function fulfillOrder(int $id): void
    {
        $stmt = $this->mysqli->prepare($this->updateFulfillOrder);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        $this->mysqli->commit();
    }
}