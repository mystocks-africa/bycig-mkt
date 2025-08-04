<?php
$BASE_DIR = __DIR__ . "/../../";

include $BASE_DIR . "utils/memcached.php";

$session_id_cookie = $_COOKIE["session_id"] ?? null;
$session = $memcached->get($session_id_cookie);

$request_method = $_SERVER["REQUEST_METHOD"];

if ($request_method == "GET" && isset($session)) {
    $memcached->delete($session_id_cookie);
    setcookie('session_id', "", 0,'/');
} else if ($request_method == "GET" && empty($session_id_cookie)) {
    echo json_encode([
        "error" => "No session found"
    ]);
    exit();
}