<?php
namespace App\Services;

include_once __DIR__ . "/../../models/holdings/Repository.php";

include_once __DIR__ . "/../../core/templates/DbTemplate.php";

use App\DbTemplate;
use App\Models\Repository\HoldingRepository;

class HomeService 
{
    private DbTemplate $db;
    private HoldingRepository $holdingRepository;

    public function __construct()
    {
        $this->db = new DbTemplate();
        $this->holdingRepository = new HoldingRepository($this->db->getPdo());
    }

    public function getAllHoldings()
    {
       return $this->holdingRepository->findAll();
    }
}