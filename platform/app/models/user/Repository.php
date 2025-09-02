<?php
namespace App\Models\User;

use App\Core\Templates\DbTemplate;
use mysqli;
use App\Models\User\Entity as UserEntity;

class Repository 
{
    private mysqli $mysqli;

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

    public function __construct()
    {
        $this->mysqli = new DbTemplate()->getMysqli();
    }

    public function save(UserEntity $user): void
    {
        if ($user->clusterLeader) {
            $stmt = $this->mysqli->prepare($this->userInsertQuery);
            $stmt->bind_param("ssss",
                $user->email,
                $user->pwd,
                $user->clusterLeader,
                $user->fullName
            );
        } else {
            $stmt = $this->mysqli->prepare($this->userInsertNoLeaderQuery);
            $stmt->bind_param("sss",
                $user->email,
                $user->pwd,
                $user->fullName
            );
        }

        $stmt->execute();
        $stmt->close();
        $this->mysqli->commit();
    }

    public function findByEmail(string $email): array|false
    {        
        $stmt = $this->mysqli->prepare($this->findUserQuery);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        
        $result->free();
        $stmt->close();
        
        return $data ?: false;
    }

    public function findAllClusterLeaders(): array
    {        
        $result = $this->mysqli->query($this->findClusterLeaderQuery);
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $result->free();
        
        return $data;
    }

    public function updatePwd(string $newPwd, string $email): void
    {        
        $stmt = $this->mysqli->prepare($this->updatePwdQuery);
        $stmt->bind_param("ss", $newPwd, $email);
        $stmt->execute();
        $stmt->close();
        $this->mysqli->commit();
    }

    public function updateBalance(float $newBalance, string $email): void 
    {        
        $stmt = $this->mysqli->prepare($this->updateBalanceQuery);
        $stmt->bind_param("ds", $newBalance, $email);
        $stmt->execute();
        $stmt->close();
        $this->mysqli->commit();
    }

    public function delete(string $email): void
    {        
        $stmt = $this->mysqli->prepare($this->deleteUser);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->close();
        $this->mysqli->commit();
    }

    public function update(string $email, array $fields, array $params): void
    {
        // Build the SQL dynamically
        $sql = "UPDATE users SET " . implode(" = ?, ", $fields) . " = ? WHERE email = ?";
        
        // Prepare parameter values and types
        $values = [];
        $types = "";
        
        foreach ($fields as $field) {
            $values[] = $params[$field];
            // Determine type based on value
            if (is_int($params[$field])) {
                $types .= "i";
            } elseif (is_float($params[$field])) {
                $types .= "d";
            } else {
                $types .= "s";
            }
        }
        
        // Add email parameter
        $values[] = $email;
        $types .= "s";
        
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param($types, ...$values);
        $stmt->execute();
        $stmt->close();
        $this->mysqli->commit();
    }
}