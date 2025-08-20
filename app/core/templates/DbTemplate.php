<?php

namespace App;

class DbTemplate {

    protected static function getConnection(): \PDO
    {
        $host = "mysql";
        $user = "app_user";
        $pass = "app_pass";
        $db   = "app_db";
        $port = 3306;

        $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";
        
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