<?php
require __DIR__ . '/../vendor/autoload.php';

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($requestUri !== '/redirect') {
    include $_SERVER['DOCUMENT_ROOT'] . '/../app/views/navbar.php';
}

$router = require $_SERVER['DOCUMENT_ROOT'] . '/../app/core/routes/index.php';
