<?php
// Use $_SERVER['DOCUMENT_ROOT'] for absolute paths
require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';


if (!file_exists($autoloadPath)) {
    exit("Autoload file not found at: " . $autoloadPath);
}

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($requestUri !== '/redirect') {
    include $_SERVER['DOCUMENT_ROOT'] . '/app/views/navbar.php';
}

$router = require $_SERVER['DOCUMENT_ROOT'] . '/app/core/routes/index.php';
