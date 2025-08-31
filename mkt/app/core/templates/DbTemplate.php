<?php
namespace App\Core\Templates;

include_once __DIR__ . "/../../../utils/env.php";

use PDO;
use PDOException;

class DbTemplate {

    private array $env;
    private PDO $pdo;

    public function __construct()
    {
        global $env;
        $this->env = $env;

        $dsn = $this->env["SQL_DSN"] ?? 'mysql:host=mysql;port=3306;dbname=app_db';
        $user = $this->env["SQL_USER"] ?? "app_user";
        $pass = $this->env["SQL_PASS"] ?? "app_pass";
        
        try {
            $this->pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_AUTOCOMMIT => false
            ]);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    
    public function getPdo(): PDO 
    {
        return $this->pdo;
    }
}