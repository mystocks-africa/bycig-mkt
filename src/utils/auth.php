<?php
include "./memcached.php";

function get_session() {
    global $memcached;

    $session_id = $_COOKIE['session_id'] ?? null;

    $session = $memcached->get($session_id);

    if (isset($session)) {
        return $session;
    } else {
        return null;
    }
}