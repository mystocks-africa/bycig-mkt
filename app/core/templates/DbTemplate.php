<?php

namespace App;
include_once __DIR__ . "/../../../utils/env.php";

class DbTemplate {

    private array $env;

    public function __construct()
    {
        global $env;
        $this->env = $env;
    }
    
    public function getConnection(): \PDO
    {    
        $dsn = $this->env["SQL_DSN"];
        $user = $this->env["SQL_USER"];
        $pass = $this->env["SQL_PASS"];
        
        try {
            $pdo = new \PDO($dsn, $user, $pass, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false
            ]);
            
            return $pdo;
            
        } catch (\PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
}
