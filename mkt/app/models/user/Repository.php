<?php

namespace App\Models\Repository;
include_once __DIR__ . "/../../core/templates/DbTemplate.php";
include_once __DIR__ . "/Entity.php";


use App\DbTemplate;
use App\Models\Entity\UserEntity;

class UserRepository 
{

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

    private string $updateBalanceQuery = "
        UPDATE users
        SET balance = ?
        WHERE email = ?
    ";

    private string $deleteUser = "
        DELETE FROM users
        WHERE email = ?;
    ";

    public function save(UserEntity $user, DbTemplate $db): void
    {
        $pdo = $db->getConnection();

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

    public function findByEmail(string $email, DbTemplate $db)
    {
        $pdo = $db->getConnection();
        $stmt = $pdo->prepare($this->findUserQuery);
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function findAllClusterLeaders(DbTemplate $db)
    {
        $pdo = $db->getConnection();
        $stmt = $pdo->prepare($this->findClusterLeaderQuery);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function updatePwd(string $newPwd, string $email, DbTemplate $db): void
    {
        $pdo = $db->getConnection();
        $stmt = $pdo->prepare($this->updatePwdQuery);
        $stmt->execute([$newPwd, $email]);
    }

    public function updateBalance(int $newBalance, string $email, DbTemplate $db): void 
    {
        $pdo = $db->getConnection();
        $stmt = $pdo->prepare($this->updateBalanceQuery);
        $stmt->execute([$newBalance, $email]);
    }

    public function delete(string $email, DbTemplate $db)
    {
        $pdo = $db->getConnection();
        $stmt = $pdo->prepare($this->deleteUser);
        $stmt->execute([
            $email
        ]);
    }
}
