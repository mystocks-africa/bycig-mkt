<?php
include "./memcached.php";

// Used if need email or user role
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

// Serverside check uses the actual memcached session to check
function serverside_check_auth() {
    global $memcached;

    $session_id = $_COOKIE["session_id"] ?? null;
    $session = $memcached->get($session_id);
    if (empty($session)) {
        echo json_encode([
            "error" => "Unauthenticated. Please log in and try again."
        ]);
        exit();
    };
}

// Clientside check only uses cookies
function clientside_check_auth() {
    $session_id_cookie = $_COOKIE["session_id"] ?? null;

    if (isset($session_id_cookie)) return true;
    else {
        header("Location: no_auth.php");
        exit();
    };
}