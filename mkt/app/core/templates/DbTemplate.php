<?php

namespace App;
include_once __DIR__ . "/../../../utils/env.php";

class DbTemplate {

    private array $env;
    private \PDO $pdo;

    public function __construct()
    {
        global $env;
        $this->env = $env;

        $dsn = $this->env["SQL_DSN"] ?? 'mysql:host=mysql;port=3306;dbname=app_db';
        $user = $this->env["SQL_USER"] ?? "app_user";
        $pass = $this->env["SQL_PASS"] ?? "app_pass";
        
        try {
            $this->pdo = new \PDO($dsn, $user, $pass, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false
            ]);
            
        } catch (\PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    
    public function getPdo(): \PDO 
    {
        return $this->pdo;
    }
}