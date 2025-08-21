<?php

namespace App\Models;
include_once __DIR__ . "/../../core/templates/DbTemplate.php";

use App\DbTemplate;

class Holding extends DbTemplate 
{
    private string $investor;
    private string $stock_ticker;
    private string $stock_name;
    private string $bid_price;
    private string $target_price;
    private string $proposal_file;

    private string $insertHoldingQuery = "
        INSERT INTO holdings 
        (investor, stock_ticker, stock_name, bid_price, target_price, proposal_file) 
        VALUES (?, ?, ?, ?, ?, ?)
    ";
    
    private static string $findAllHoldingsQuery = "
        SELECT
            holdings.id,
            holdings.stock_ticker,
            holdings.stock_name,
            holdings.investor
        FROM holdings
        INNER JOIN users
            ON holdings.investor = users.email
        WHERE users.cluster_leader = ?
    ";

    private static string $deleteHoldingQuery = "
        DELETE 
        FROM holdings 
        WHERE id = ? 
        AND investor = ?
    ";

    public function __construct(string $investor, string $stock_ticker, string $stock_name, string $bid_price, string $target_price, string $proposal_file)
    {
        $this->investor = $investor;
        $this->stock_ticker = $stock_ticker;
        $this->stock_name = $stock_name;
        $this->bid_price = $bid_price;
        $this->target_price = $target_price;
        $this->proposal_file = $proposal_file;
    }

    public function createHolding()
    {
        $pdo = parent::getConnection();

        $stmt = $pdo->prepare($this->insertHoldingQuery);
        $stmt->execute([
            $this->investor,
            $this->stock_ticker,
            $this->stock_name,
            $this->bid_price,
            $this->target_price,
            $this->proposal_file
        ]);
    }

    // Each cluster leader has their own portfolio and investors associated to them can contribute to it
    public static function findAllHoldings()
    {
        $pdo = parent::getConnection();

        $stmt = $pdo->prepare(self::$findAllHoldingsQuery);
        $holdings = $stmt->fetchAll();

        return $holdings;
    }

    public static function deleteHolding($id, $email) 
    {
        $pdo = parent::getConnection();

        $stmt = $pdo->prepare(self::$deleteHoldingQuery);
        $stmt->execute([$id, $email]);
    }
}