<?php

namespace App\Models;
include_once __DIR__ . "/../../core/Dbh.php";

use App\Dbh;
use Exception;

class User extends Dbh
{
    private string $email;
    private string $pwd;
    private string $cluster_leader; 
    private string $full_name; 

    private string $post_user_query = "
        INSERT INTO users (email, pwd, cluster_leader, full_name)
        VALUES (?, ?, ?, ?)
    ";

    private static string $findClusterLeaderQuery = "
        SELECT email FROM users
        WHERE role = 'cluster_leader';
    ";

    private static string $findUserQuery = "
        SELECT email, pwd, role
        FROM users 
        WHERE email = ?
        LIMIT 1;
    ";

    // Constructor with mysqli injection
    public function __construct(string $email, string $pwd, string $cluster_leader, string $full_name) 
    {
        $this->email = $email;
        $this->pwd = $pwd;
        $this->cluster_leader = $cluster_leader;
        $this->full_name = $full_name;
    }

    public function createUser() 
    {
        parent::connect();

        $stmt = parent::$mysqli->prepare($this->post_user_query);
        $stmt->bind_param(
            "ssss", 
            $this->email,
            $this->pwd,
            $this->cluster_leader,
            $this->full_name
        );
        $stmt->execute();
        $stmt->close();
    }

    public static function findByEmail(string $email) 
    {        
        try {
            parent::connect();
            $stmt = parent::$mysqli->prepare(self::$findUserQuery);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();
            return $user;
        } catch(Exception $error) {
            return $error->getMessage();
        }
    }

    public static function findAllClusterLeaders() 
    {
        try {
            parent::connect();
            $stmt = parent::$mysqli->prepare(self::$findClusterLeaderQuery);
            $stmt->execute();
            $result = $stmt->get_result();
            $clusterLeaders = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $clusterLeaders;
        } catch (Exception $error) {
            return ["error" => $error->getMessage()];
        }
    }
}
