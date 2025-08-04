<?php 
$BASE_DIR = __DIR__ . "/../../";

include $BASE_DIR . "utils/database.php";
include $BASE_DIR . "utils/memcached.php";

$request_method = $_SERVER["REQUEST_METHOD"];
$EXPIRATION_DAYS = 60*60*24*30; // 30 days

function assign_session($role, $email) {
    global $memcached;
    global $EXPIRATION_DAYS;
    
    $session_id = bin2hex(random_bytes(32)); // 64 character hex string
    $memcached->set($session_id, "$email, $role", $EXPIRATION_DAYS);
    
    return $session_id;
}

if ($request_method == "POST") {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);

    $get_user_query = "
        SELECT email, pwd, role
        FROM users 
        WHERE email = ?
        LIMIT 1;
    ";

    $stmt = $mysqli->prepare($get_user_query);
    $stmt->bind_param(
        "s", 
        $email,
    );
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if (isset($user) && password_verify($password, $user['pwd'])) {
        $session_id = assign_session($user['role'], $user['email']);
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