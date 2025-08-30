<?php

namespace App\Models\Repository;
include_once __DIR__ . "/../../core/templates/DbTemplate.php";
include_once __DIR__ . "/Entity.php";

use Exception;
use PDO;
use App\Models\Entity\UserEntity;

class UserRepository 
{
    private PDO $pdo;

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

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function save(UserEntity $user): void
    {
        if ($user->clusterLeader) {
            $stmt = $this->pdo->prepare($this->userInsertQuery);
            $stmt->execute([
                $user->email,
                $user->pwd,
                $user->clusterLeader,
                $user->fullName
            ]);
        } else {
            $stmt = $this->pdo->prepare($this->userInsertNoLeaderQuery);
            $stmt->execute([
                $user->email,
                $user->pwd,
                $user->fullName
            ]);
        }
    }

    public function findByEmail(string $email): array|false
    {        
        $stmt = $this->pdo->prepare($this->findUserQuery);
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function findAllClusterLeaders(): array
    {        
        $stmt = $this->pdo->prepare($this->findClusterLeaderQuery);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function updatePwd(string $newPwd, string $email): void
    {        
        $stmt = $this->pdo->prepare($this->updatePwdQuery);
        $stmt->execute([
            $newPwd, 
            $email
        ]);
    }

    public function updateBalance(float $newBalance, string $email): void 
    {        
        $stmt = $this->pdo->prepare($this->updateBalanceQuery);
        $stmt->execute([
            $newBalance, 
            $email
        ]);
    }

    public function delete(string $email): void
    {        
        $stmt = $this->pdo->prepare($this->deleteUser);
        $stmt->execute([
            $email
        ]);
    }

    public function update(string $email, array $fields, array $params): void
    {
        $sql = "UPDATE users SET " . implode(", ", $fields) . " WHERE email = :email";
        $params['email'] = $email;
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
    }
}