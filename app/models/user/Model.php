<?php

namespace App\Models;
include_once __DIR__ . "/../../core/Dbh.php";

use App\Dbh;

class UserModel extends Dbh
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
        SELECT email, pwd, role
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
        parent::connect();

        if ($this->clusterLeader) {
            $stmt = parent::$mysqli->prepare($this->userInsertQuery);
            $stmt->bind_param(
                "ssss",
                $this->email,
                $this->pwd,
                $this->clusterLeader,
                $this->fullName
            );
        } else {
            $stmt = parent::$mysqli->prepare($this->userInsertNoLeaderQuery);
            $stmt->bind_param(
                "sss",
                $this->email,
                $this->pwd,
                $this->fullName
            );
        }

        $stmt->execute();
        $stmt->close();
    }

    public static function findByEmail(string $email)
    {
        parent::connect();
        $stmt = parent::$mysqli->prepare(self::$findUserQuery);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    }

    public static function findAllClusterLeaders()
    {
        parent::connect();
        $stmt = parent::$mysqli->prepare(self::$findClusterLeaderQuery);
        $stmt->execute();
        $result = $stmt->get_result();
        $clusterLeaders = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $clusterLeaders;
    }

    public static function updatePwd($email, $newPwd)
    {
        parent::connect();
        $stmt = parent::$mysqli->prepare(self::$updatePwdQuery);
        $stmt->bind_params(
            "ss",
            $newPwd,
            $email
        );
        $stmt->execute();
        $stmt->close();
    }
}
