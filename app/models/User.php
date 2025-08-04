<?php
namespace App\Models;

use Exception;
include_once "../../utils/database.php";

// SQL statements
$get_user_query = "
    SELECT email, pwd, role
    FROM users 
    WHERE email = ?
    LIMIT 1;
";

$post_user_query = "
    INSERT INTO users (email, pwd, cluster_leader, full_name)
    VALUES (?, ?, ?, ?)
";

class User
{
    private string $email;
    private string $pwd;
    private string $cluster_leader; 
    private string $full_name; 

    public function __construct(string $email, string $pwd, string $cluster_leader, string $full_name) {
        $this->email = $email;
        $this->pwd = $pwd;
        $this->cluster_leader = $cluster_leader;
        $this->full_name = $full_name;
    }

    public function createUser() {
        global $mysqli, $post_user_query;

        try {
            $stmt = $mysqli->prepare($post_user_query);
            $stmt->bind_param(
                "ssss", 
                $this->email,
                $this->pwd,
                $this->cluster_leader,
                $this->full_name
            );
            $stmt->execute();
        } catch(Exception $error)  {
            return $error->getMessage();
        }

    }

    public static function findByEmail(string $email) {
        global $mysqli, $get_user_query;

        try {
            $stmt = $mysqli->prepare($get_user_query);
            $stmt->bind_param(
                "s", 
                $email,
            );
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();

            return $user;
        } catch(Exception $error) {
            return $error->getMessage();
        }

    }
}