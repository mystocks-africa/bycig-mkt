<?php
namespace App\Services;

include_once __DIR__ . "/../../core/templates/DbTemplate.php";

include_once __DIR__ . "/../../models/holdings/Repository.php";
include_once __DIR__ . "/../../models/user/Repository.php";

use App\DbTemplate;
use App\Models\Repository\HoldingRepository;
use App\Models\Repository\UserRepository;
use Exception;

class AccountService 
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

    public function getUserInfo(?string $email): array
    {
        if (!$email) {
            throw new Exception("Email is not given");
        }
        $holdings = $this->holdingRepository->findByEmail($email);
        $user = $this->userRepository->findByEmail($email);
        return [
            'holdings' => $holdings,
            'user' => $user
        ];
    }
}