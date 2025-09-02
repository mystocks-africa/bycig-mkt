<?php
namespace App\Models\Holdings;

class Entity 
{
    public string $investor;
    public string $stock_ticker;
    public string $stock_name;
    public int $shares;
    public bool $fulfilled;

    public function __construct(
        string $investor, 
        string $stock_ticker, 
        string $stock_name, 
        int $shares, 
        bool $fulfilled = false,
    )
    {
        $this->investor = $investor;
        $this->stock_ticker = $stock_ticker;
        $this->stock_name = $stock_name;
        $this->shares = $shares;
        $this->fulfilled = $fulfilled;
    }
}