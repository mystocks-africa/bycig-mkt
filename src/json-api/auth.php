<?php 

// Not SQLite database, it is the main MySQL database
include "database.php";

$request_method = $_SERVER["REQUEST_METHOD"];
$session_db = new SQLite3(filename: "sessions.db");

function assign_session($user_id) {
    global $session_db;

    $session_id = bin2hex(random_bytes(32)); // 64 character hex string
    
    // Prepare the SQL statement with proper INSERT syntax
    $stmt = $session_db->prepare("
        INSERT INTO sessions (session_id, user_id, created_at)
        VALUES (?, ?, CURRENT_TIMESTAMP)
    ");
    
    $stmt->bindValue(1, $session_id, SQLITE3_TEXT);
    $stmt->bindValue(2, $user_id, SQLITE3_INTEGER);
    
    $stmt->execute();
    
    $session_db->close();
    
    return $session_id;
}

if ($request_method == "POST") {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    $get_user_query = "
        SELECT id 
        FROM users 
        WHERE email = ? AND password = ?
    ";

    $stmt = $mysqli->prepare("");
    $stmt->bind_param("ss", $email);
    $stmt->execute();
    $user_id = $stmt->get_result();
    $stmt->close();

    if (isset($user_id)) {
        $session_id = assign_session($user_id);
        $_COOKIE['session_id'] = $session_id;
    }
}