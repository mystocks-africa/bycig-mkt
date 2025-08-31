<?php

namespace App\Models\Entity;

class ProposalEntity
{
    public string $post_author;
    public string $stock_ticker;
    public string $stock_name;
    public string $subject_line;
    public string $thesis;
    public string $shares;
    public string $proposal_file;

    public function __construct(
        string $post_author,
        string $stock_ticker,
        string $stock_name,
        string $subject_line,
        string $thesis,
        string $shares,
        string $proposal_file,
    ) {
        $this->post_author = $post_author;
        $this->stock_ticker = $stock_ticker;
        $this->stock_name = $stock_name;
        $this->subject_line = $subject_line;
        $this->thesis = $thesis;
        $this->shares = $shares;
        $this->proposal_file = $proposal_file;
    }
}
