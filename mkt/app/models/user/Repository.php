<?php

namespace App\Models\Repository;
include_once __DIR__ . "/../../core/templates/DbTemplate.php";
include_once __DIR__ . "/Entity.php";


use App\DbTemplate;
use App\Models\Entity\UserEntity;

class UserRepository 
{
    private DbTemplate $db;

    private string $userInsertQuery = "
        INSERT INTO users (
            email, 
            pwd, 
            cluster_leader, 
            full_name
        )
        VALUES (?, ?, ?, ?)
    ";

    private string $userInsertNoLeaderQuery = "
        INSERT INTO users (
            email, 
            pwd, 
            full_name
        )
        VALUES (?, ?, ?)
    ";

    private string $findClusterLeaderQuery = "
        SELECT 
            email, 
            full_name 
        FROM users
        WHERE role = 'cluster_leader';
    ";

    private string $findUserQuery = "
        SELECT 
            full_name, 
            email, 
            pwd, 
            role, 
            cluster_leader, 
            balance
        FROM users 
        WHERE email = ?
        LIMIT 1;
    ";

    private string $updatePwdQuery = "
        UPDATE users
        SET pwd = ?
        WHERE email = ?
    ";

    public function __construct()
    {
        $this->db = new DbTemplate();
    }

    public function save(UserEntity $user): void
    {
        $pdo = $this->db->getConnection();

        if ($user->clusterLeader) {
            $stmt = $pdo->prepare($this->userInsertQuery);
            $stmt->execute([
                $user->email,
                $user->pwd,
                $user->clusterLeader,
                $user->fullName
            ]);
        } else {
            $stmt = $pdo->prepare($this->userInsertNoLeaderQuery);
            $stmt->execute([
                $user->email,
                $user->pwd,
                $user->fullName
            ]);
        }
    }

    public function findByEmail(string $email)
    {
        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare($this->findUserQuery);
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function findAllClusterLeaders()
    {
        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare($this->findClusterLeaderQuery);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function updatePwd(string $newPwd, string $email): void
    {
        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare($this->updatePwdQuery);
        $stmt->execute([$newPwd, $email]);
    }
}
