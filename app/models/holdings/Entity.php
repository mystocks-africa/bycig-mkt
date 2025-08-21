<?php

namespace App\Models\Entity;

class HoldingEntity 
{
    public string $investor;
    public string $stock_ticker;
    public string $stock_name;
    public string $bid_price;
    public string $target_price;
    public string $proposal_file;

    public function __construct(string $investor, string $stock_ticker, string $stock_name, string $bid_price, string $target_price, string $proposal_file)
    {
        $this->investor = $investor;
        $this->stock_ticker = $stock_ticker;
        $this->stock_name = $stock_name;
        $this->bid_price = $bid_price;
        $this->target_price = $target_price;
        $this->proposal_file = $proposal_file;
    }
}