<?php 

$BASE_DIR = __DIR__ . "../";

include $BASE_DIR . 'utils/auth.php';
include $BASE_DIR . 'utils/database.php'; 

serverside_check_auth();

$request_method = $_SERVER["REQUEST_METHOD"];

if ($request_method === "GET") {
    header('Content-Type: application/json');

    $query = "
        SELECT ID, user_login
        FROM `wp_usermeta` 
        INNER JOIN `wp_users` ON `wp_usermeta`.`user_id` = `wp_users`.`ID` 
        WHERE `meta_key` = 'wp_capabilities' 
        AND `meta_value` = 'a:1:{s:14:\"cluster-leader\";b:1;}';
    ";


    if ($result = $mysqli->query($query)) {
        $leaders = [];
        while ($row = $result->fetch_assoc()) {
            array_push($leaders, [
                'user_login' => $row['user_login'],
                'id' => $row['ID']
            ]);        
        }
        echo json_encode($leaders);
        $result->free();
    } else {
        http_response_code(500);
        echo json_encode(["error" => $mysqli->error]);
    }

    $mysqli->close();
    exit();
}

exit();