<?php

namespace App;
include_once __DIR__ . "/../../../utils/env.php";

class DbTemplate {
    
    protected static function getConnection(): \PDO
    {    
        global $env;

        $dsn = $env["SQL_DSN"];
        $user = $env["SQL_USER"];
        $pass = $env["SQL_PASS"];
        
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