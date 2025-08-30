<?php

namespace App\Models\Entity;

class HoldingEntity 
{
    public string $investor;
    public string $stock_ticker;
    public string $stock_name;
    public string $bid_price;
    public int $shares;
    public string $proposal_file;
    public bool $fulfilled;
    public int $bought_price;

    public function __construct(
        string $investor, 
        string $stock_ticker, 
        string $stock_name, 
        string $bid_price, 
        int $shares, 
        string $proposal_file,
        bool $fulfilled = false,
        int $bought_price = null
    )
    {
        $this->investor = $investor;
        $this->stock_ticker = $stock_ticker;
        $this->stock_name = $stock_name;
        $this->bid_price = $bid_price;
        $this->shares = $shares;
        $this->proposal_file = $proposal_file;
        $this->fulfilled = $fulfilled;
        $this->bought_price = $bought_price;
    }
}