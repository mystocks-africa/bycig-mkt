<?php
include 'env.php';

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

$mysqli = new mysqli($host, $user, $pass, $dbname, $port);

if ($mysqli->connect_errno) {
    die("Failed to connect to MySQL: " . $mysqli->connect_error);
}

$mysqli->set_charset("utf8mb4");
