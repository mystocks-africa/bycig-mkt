<?php
namespace App\Services;

use App\Core\Templates\DbTemplate;

use App\Models\Holdings\Repository as HoldingRepository;

class HomeService 
{
    private DbTemplate $db;
    private HoldingRepository $holdingRepository;

    public function __construct()
    {
        $this->db = new DbTemplate();
        $this->holdingRepository = new HoldingRepository($this->db->getPdo());
    }

    public function getAllHoldings(): array
    {
       return $this->holdingRepository->findAll();
    }
}