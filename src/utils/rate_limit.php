<?php
function get_rate_limit_key() {
    $ip = $_SERVER["REMOTE_ADDR"];
    return "$ip:rate_limit";
}

function check_rate_limit_and_update() {
    $key = get_rate_limit_key();
    $limit = apcu_fetch($key);
    if (!$limit) {
        apcu_store($key, json_encode(['attempts' => 1, 'expires_at' => time() + 120]), 120);
    } else {
        $payload = json_decode($limit, true);
        if ($payload['attempts'] >= 2) {
            http_response_code(429);
            echo json_encode(["error" => "Rate limit exceeded"]);
            exit();
        }
        $payload['attempts']++;
        apcu_store($key, json_encode($payload), $payload['expires_at'] - time());
    }
}