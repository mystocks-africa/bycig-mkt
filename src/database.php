<?php
// Load environment variables
$env = parse_ini_file('.env');
$mysql_uri = $env["MYSQL_URI"] ?? null;

if (!$mysql_uri) {
    die("MYSQL_URI not found in .env");
}

// Parse URI (e.g. mysql://user:password@host:port/dbname)
$parsed = parse_url($mysql_uri);

if (!$parsed || !isset($parsed['host'], $parsed['user'], $parsed['pass'], $parsed['path'])) {
    die("Invalid MYSQL_URI format");
}

$host = $parsed['host'];
$port = $parsed['port'] ?? 3306; // default MySQL port
$user = $parsed['user'];
$pass = $parsed['pass'];
$dbname = ltrim($parsed['path'], '/'); // remove leading slash from path

// Create mysqli connection
$mysqli = new mysqli($host, $user, $pass, $dbname, $port);

if ($mysqli->connect_errno) {
    die("Failed to connect to MySQL: " . $mysqli->connect_error);
}

// Optional: set charset
$mysqli->set_charset("utf8mb4");
