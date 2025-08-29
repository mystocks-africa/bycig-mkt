<?php
namespace App\Services;

include_once __DIR__ . "/../../core/templates/DbTemplate.php";
include_once __DIR__ . "/../../core/files/Files.php";

include_once __DIR__ . "/../../models/holdings/Repository.php";
include_once __DIR__ . "/../../models/user/Repository.php";

use App\DbTemplate;
use App\Core\Files;

use App\Models\Repository\HoldingRepository;
use App\Models\Repository\UserRepository;
use Exception;

class HoldingService 
{
    private DbTemplate $db;
    private HoldingRepository $holdingRepository;
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->db = new DbTemplate();
        $this->holdingRepository = new HoldingRepository($this->db->getPdo());
        $this->userRepository = new UserRepository($this->db->getPdo());
    }

    // We need a try-catch block here so that the ACID transaction rollbacks (reverts) gracefully
    public function processSellOrder(?int $id, string $email): void
    {
        $this->db->getPdo()->beginTransaction();
         
        try {
            $holding = $this->holdingRepository->findById($id);

            if (!$holding['fulfilled']) {
                throw new Exception("Order already has been fulfilled.");
            }

            $user = $this->userRepository->findByEmail($email);
            $newBalance = $user['balance'] - $user['bought_price'];

            $this->userRepository->updateBalance($newBalance, $email);
            $this->holdingRepository->delete($id, $email);
            Files::deleteFile($holding['proposal_file']);

            $this->db->getPdo()->commit();
        } catch(Exception $error) {
            $this->db->getPdo()->rollBack();
        }
    }
}