<?php
function set_rate_limit() {
    global $ip;
    $ttl = 120; // 2 minutes
    $expires_at = time() + $ttl;

    $new_payload = json_encode([
        'attempts' => 0,
        'expires_at' => $expires_at,
    ]);
    apcu_store("$ip:rate_limit", $new_payload, $ttl);
}

function update_rate_limit($payload) {
    global $ip;

    $ttl = $payload['expires_at'] - time();
    if ($ttl <= 0) return false;

    $new_value = $payload['attempts'] + 1;

    $new_encoded_payload = json_encode([
        'attempts' => $new_value,
        'expires_at' => $payload['expires_at']
    ]);

    apcu_store("$ip:rate_limit", $new_encoded_payload, $ttl);
    return true;
}

function get_rate_limit() {
    global $ip;
    $rate_limit = apcu_fetch("$ip:rate_limit");
    if ($rate_limit === false) return false;
    return json_decode($rate_limit, true);
}