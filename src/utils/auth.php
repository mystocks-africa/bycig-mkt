<?php
$session_db = new SQLite3(filename: "sessions.db");
$EXPIRATION_DAYS = 30;

function get_session() {
    global $session_db;
    global $EXPIRATION_DAYS;

    $session_id = $_COOKIE['session_id'] ?? null;

    $stmt = $session_db->prepare("
        SELECT user_id, role, created_at
        FROM sessions 
        WHERE session_id = ?
        LIMIT 1;
    ");
    $stmt->bindValue(1, $session_id, SQLITE3_TEXT);
    $result = $stmt->execute();
    $session = $result->fetchArray(SQLITE3_ASSOC);

    // Expiration logic: delete session if passed time 
    if ($session['created_at'] < date('Y-m-d H:i:s', strtotime("-$EXPIRATION_DAYS days"))) {
        $delete_stmt = $session_db->prepare("
            DELETE FROM sessions WHERE session_id = ?
        ");
        $delete_stmt->bindValue(1, $session_id, SQLITE3_TEXT);
        $delete_stmt->execute();

        return null; 
    }

    return $session;
}