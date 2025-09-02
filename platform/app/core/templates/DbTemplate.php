<?php
namespace App\Core\Templates;

use mysqli;
use mysqli_sql_exception;
use Dotenv\Dotenv;

class DbTemplate {

    private mysqli $mysqli;

    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . "/../../../");
        $dotenv->load();

        $host = $_ENV["SQL_HOST"] ?? "mysql";
        $port = (int)($_ENV["SQL_PORT"] ?? 3306);
        $database = $_ENV["SQL_DATABASE"] ?? "app_db";
        $user = $_ENV["SQL_USER"] ?? "app_user";
        $pass = $_ENV["SQL_PASS"] ?? "app_pass";
        
        try {
            // Enable exception mode for MySQLi
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            
            $this->mysqli = new mysqli($host, $user, $pass, $database, $port);
            
            // Set charset to utf8mb4
            $this->mysqli->set_charset("utf8mb4");
            
            // Disable autocommit (equivalent to PDO::ATTR_AUTOCOMMIT => false)
            $this->mysqli->autocommit(false);
            
        } catch (mysqli_sql_exception $error) {
            exit("Database connection failed: " . $error->getMessage());
        }
    }
    
    public function getMysqli(): mysqli 
    {
        return $this->mysqli;
    }

    public function __destruct()
    {
        if (isset($this->mysqli)) {
            $this->mysqli->close();
        }
    }
}
