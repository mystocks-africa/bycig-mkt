<?php

namespace App\Models\Entity;

class Proposal 
{
    public string $post_author;
    public string $stock_ticker;
    public string $stock_name;
    public string $subject_line;
    public string $thesis;
    public string $bid_price;
    public string $target_price;
    public string $proposal_file;
    public string $status;

    public function __construct(
        string $post_author,
        string $stock_ticker,
        string $stock_name,
        string $subject_line,
        string $thesis,
        string $bid_price,
        string $target_price,
        string $proposal_file,
        string $status = "pending"
    ) {
        $this->post_author = $post_author;
        $this->stock_ticker = $stock_ticker;
        $this->stock_name = $stock_name;
        $this->subject_line = $subject_line;
        $this->thesis = $thesis;
        $this->bid_price = $bid_price;
        $this->target_price = $target_price;
        $this->proposal_file = $proposal_file;
        $this->status = $status;
    }
}
