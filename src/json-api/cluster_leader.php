<?php 

$BASE_DIR = __DIR__ . "../";

include $BASE_DIR . 'utils/auth.php';
include $BASE_DIR . 'utils/database.php'; 

serverside_check_auth();

$request_method = $_SERVER["REQUEST_METHOD"];

if ($request_method === "GET") {
    header('Content-Type: application/json');

    $query = "
        SELECT email
        FROM users
        WHERE role = 'cluster_leader';
    ";

    if ($result = $mysqli->query($query)) {
        $email = [];
        while ($row = $result->fetch_assoc()) {
            array_push($email, $row["email"]);       
        }
        echo json_encode($email);
    } else {
        http_response_code(500);
        echo json_encode(["error" => $mysqli->error]);
    }

    $mysqli->close();
    exit();
}
