<?php
$BASE_DIR = __DIR__ ."../";

include $BASE_DIR . 'utils/auth.php';
include $BASE_DIR . 'utils/database.php';

serverside_check_auth();

$request_method = $_SERVER['REQUEST_METHOD'];

if ($request_method == "GET") {
    header('Content-Type: application/json');

    $get_proposal_query = "
        SELECT post_id, email, subject_line
        FROM wp_2_proposals 
    ";

    try {
        $stmt = $mysqli->prepare($get_proposal_query);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $result_json = json_encode($rows);
        echo $result_json;
        exit();
    } catch (Exception $error) {
        $error_message = json_encode([
            "error" => $error->getMessage()
        ]);

        echo $error_message;
        exit();
    }
}