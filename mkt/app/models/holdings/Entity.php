<?php

namespace App\Models\Entity;

class HoldingEntity 
{
    public string $investor;
    public string $stock_ticker;
    public string $stock_name;
    public string $bid_price;
    public int $shares;
    public bool $fulfilled;

    public function __construct(
        string $investor, 
        string $stock_ticker, 
        string $stock_name, 
        string $bid_price, 
        int $shares, 
        bool $fulfilled = false,
    )
    {
        $this->investor = $investor;
        $this->stock_ticker = $stock_ticker;
        $this->stock_name = $stock_name;
        $this->bid_price = $bid_price;
        $this->shares = $shares;
        $this->fulfilled = $fulfilled;
    }
}