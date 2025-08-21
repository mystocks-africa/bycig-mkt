<?php

namespace App\Models;
include_once __DIR__ . "/../../core/templates/DbTemplate.php";

use App\DbTemplate;

class User extends DbTemplate
{
    private string $email;
    private string $pwd;
    private string $clusterLeader;
    private string $fullName;

    private string $userInsertQuery = "
        INSERT INTO users (email, pwd, cluster_leader, full_name)
        VALUES (?, ?, ?, ?)
    ";

    private string $userInsertNoLeaderQuery = "
        INSERT INTO users (email, pwd, full_name)
        VALUES (?, ?, ?)
    ";

    private static string $findClusterLeaderQuery = "
        SELECT email, full_name FROM users
        WHERE role = 'cluster_leader';
    ";

    private static string $findUserQuery = "
        SELECT email, pwd, role, cluster_leader
        FROM users 
        WHERE email = ?
        LIMIT 1;
    ";

    private static string $updatePwdQuery = "
        UPDATE users
        SET pwd = ?
        WHERE email = ?
    ";

    public function __construct(string $email, string $pwd, string $clusterLeader, string $fullName)
    {
        $this->email = $email;
        $this->pwd = $pwd;
        $this->clusterLeader = $clusterLeader;
        $this->fullName = $fullName;
    }

    public function createUser()
    {
        $pdo = parent::getConnection();

        if ($this->clusterLeader) {
            $stmt = $pdo->prepare($this->userInsertQuery);
            $stmt->execute([
                $this->email,
                $this->pwd,
                $this->clusterLeader,
                $this->fullName
            ]);
        } else {
            $stmt = $pdo->prepare($this->userInsertNoLeaderQuery);
            $stmt->execute([
                $this->email,
                $this->pwd,
                $this->fullName
            ]);
        }
    }

    public static function findByEmail(string $email)
    {
        $pdo = parent::getConnection();
        $stmt = $pdo->prepare(self::$findUserQuery);
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        return $user;
    }

    public static function findAllClusterLeaders()
    {
        $pdo = parent::getConnection();
        $stmt = $pdo->prepare(self::$findClusterLeaderQuery);
        $stmt->execute();
        $clusterLeaders = $stmt->fetchAll();
        
        return $clusterLeaders;
    }

    public static function updatePwd($newPwd, $email) 
    {
        $pdo = parent::getConnection();
        $stmt = $pdo->prepare(self::$updatePwdQuery);
        $stmt->execute([$newPwd, $email]);
    }
}