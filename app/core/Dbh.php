<?php
namespace App;
include "../../utils/env.php";

use Exception;

class Dbh {
    protected static \mysqli $mysqli;

    protected static function connect() {
        global $env, $mysqli;

        $mysql_uri = $env["MYSQL_URI"] ?? null;

        if (!$mysql_uri) {
            die("MYSQL_URI not found in .env");
        }

        $parsed = parse_url($mysql_uri);

        if (!$parsed || !isset($parsed['host'], $parsed['user'], $parsed['pass'], $parsed['path'])) {
            die("Invalid MYSQL_URI format");
        }

        $host = $parsed['host'];
        $port = $parsed['port'] ?? 3306; 
        $user = $parsed['user'];
        $pass = $parsed['pass'];
        $dbname = ltrim($parsed['path'], '/'); 

        try {
            self::$mysqli = new \mysqli($host, $user, $pass, $dbname, $port);
            self::$mysqli->set_charset("utf8mb4");
        } catch (Exception $error) {
            echo $error->getMessage();
        }

    }
}