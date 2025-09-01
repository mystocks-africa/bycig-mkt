<?php
require __DIR__ . '/../vendor/autoload.php';

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Only include navbar if not on /redirect
if ($requestUri !== '/redirect') {
    include __DIR__ . '/../app/views/navbar.php';
}

$router = require __DIR__ . '/../app/core/routes/index.php';
