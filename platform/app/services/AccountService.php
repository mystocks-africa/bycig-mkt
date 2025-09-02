<?php
namespace App\Services;

use App\Core\Templates\DbTemplate;

use App\Models\Holdings\Repository as HoldingRepository;
use App\Models\User\Repository as UserRepository;
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
        $user = $this->userRepository->findByEmail($email);
        $holdings = $this->holdingRepository->findByEmail($email);
        return [
            'user' => $user,
            'holdings' => $holdings
        ];
    }
}