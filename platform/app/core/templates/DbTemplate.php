<?php
namespace App\Core\Templates;

use PDO;
use PDOException;
use Dotenv\Dotenv;

class DbTemplate {

    private PDO $pdo;

    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . "/../../../");
        $dotenv->load();

        $dsn = $_ENV["SQL_DSN"] ?? 'mysql:host=mysql;port=3306;dbname=app_db';
        $user = $_ENV["SQL_USER"] ?? "app_user";
        $pass = $_ENV["SQL_PASS"] ?? "app_pass";
        
        try {
            $this->pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_AUTOCOMMIT => false
            ]);
        } catch (PDOException $error) {
            exit("Database connection failed: " . $error->getMessage());
        }
    }
    
    public function getPdo(): PDO 
    {
        return $this->pdo;
    }
}