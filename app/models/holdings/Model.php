<?php

namespace App\Models;
include_once __DIR__ . "/../../core/Dbh.php";

use App\Dbh;

class Holding extends Dbh 
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
            holdings.stock_ticker,
            holdings.stock_name,
            holdings.bid_price,
            holdings.target_price,
            holdings.proposal_file
        FROM holdings
        INNER JOIN users
            ON holdings.investor = users.email
        WHERE users.cluster_leader = ?
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
        parent::connect();

        $stmt = parent::$mysqli->prepare($this->insertHoldingQuery);
        $stmt->bind_param(
            "sssiis",
            $this->investor,
            $this->stock_ticker,
            $this->stock_name,
            $this->bid_price,
            $this->target_price,
            $this->proposal_file
        );
        $stmt->execute();
        $stmt->close();
    }

    // Each cluster leader has their own portfolio and investors associated to them can contribute to it
    public static function findAllHoldings($clusterLeader)
    {
        parent::connect();

        $stmt = parent::$mysqli->prepare(self::$findAllHoldingsQuery);
    }
}