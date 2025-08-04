<?php
include_once "memcached.php";

// Used if need email or user role
function get_session() {
    global $memcached;

    $session_id = $_COOKIE['session_id'] ?? null;
    if (!$session_id) return null;

    $session_raw = $memcached->get($session_id);

    // Debug check: is it a string?
    if (!is_string($session_raw)) {
        error_log("Session data is not a string: " . print_r($session_raw, true));
        return null;
    }

    $parts = explode(',', $session_raw);
    if (count($parts) !== 2) {
        error_log("Session string doesn't split correctly: " . $session_raw);
        return null;
    }

    // Sanitize
    $email = filter_var(trim($parts[0]), FILTER_SANITIZE_EMAIL);
    $role = htmlspecialchars(trim($parts[1]), ENT_QUOTES, 'UTF-8');

    return [
        'email' => $email,
        'role' => $role
    ];
}

// Serverside check uses the actual memcached session to check
function serverside_check_auth() {
    header('Content-Type: application/json');
    global $memcached;

    $session_id = $_COOKIE["session_id"] ?? null;

    if (empty($session_id)) {
        echo json_encode([
                "error" => "Unauthenticated. Please log in and try again."
        ]);        
        exit();
    }

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
    header('Content-Type: application/json');

    $session_id_cookie = $_COOKIE["session_id"] ?? null;

    if (isset($session_id_cookie)) return true;
    else {
        header("Location: no_auth.php");
        exit();
    };
}