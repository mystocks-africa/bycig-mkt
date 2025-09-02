<?php
// Use $_SERVER['DOCUMENT_ROOT'] for absolute paths
require __DIR__ . '/../vendor/autoload.php';

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($requestUri !== '/redirect') {
    include $_SERVER['DOCUMENT_ROOT'] . '/app/views/navbar.php';
}

$router = require __DIR__ . "/../app/routes/index.php";
