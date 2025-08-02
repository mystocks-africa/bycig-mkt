<?php
$BASE_DIR = __DIR__ . "/../../";
include $BASE_DIR . "utils/memcached.php";

$session_id_cookie = $_COOKIE["session_id"] ?? null;

$session = $memcached->get($session_id_cookie);

if (empty($session)) {
    json_encode(["error" => "No session found."]);
    http_response_code(401);
    exit();
}

$session->delete($session_id_cookie);
setcookie("session_id", "", time() - 3600);