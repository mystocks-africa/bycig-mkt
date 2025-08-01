<?php 
// Not SQLite database, it is the main MySQL database (conn logic)
include "../../utils/database.php";
$request_method = $_SERVER["REQUEST_METHOD"];

$session_db = new SQLite3(filename: "sessions.db");

function assign_session($role, $user_id) {
    global $session_db;

    $session_id = bin2hex(random_bytes(32)); // 64 character hex string
    
    $stmt = $session_db->prepare("
        INSERT INTO sessions (session_id, user_id, role, created_at)
        VALUES (?, ?, ?, CURRENT_TIMESTAMP)
    ");
    
    $stmt->bindValue(1, $session_id, SQLITE3_TEXT);
    $stmt->bindValue(1, $role, SQLITE3_TEXT);
    $stmt->bindValue(2, $user_id, SQLITE3_INTEGER);
    
    $stmt->execute();
    
    $session_db->close();
    
    return $session_id;
}

if ($request_method == "POST") {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    $get_user_query = "
        SELECT id, role
        FROM users 
        WHERE email = ? AND password = ?
        LIMIT 1;
    ";

    $stmt = $mysqli->prepare("");
    $stmt->bind_param("ss", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if (isset($user_id)) {
        $session_id = assign_session($user['role'], $user['id']);
        setcookie('session_id', $session_id, time() + (86400 *24*60*60),'/');
        echo json_encode([
            'status' => 'success',
            'message' => 'Login successful',
            'session_id' => $session_id
        ]);
    } else { 
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid email or password'
        ]);
    }
}