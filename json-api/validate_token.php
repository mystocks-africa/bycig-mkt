<?php
$BASE_DIR = __DIR__ . "../";
require $BASE_DIR . 'vendor/autoload.php';

include $BASE_DIR . 'utils/auth.php';
include $BASE_DIR . 'utils/env.php';
include $BASE_DIR . 'utils/database.php';
include $BASE_DIR . 'utils/redirection.php';

serverside_check_auth();

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$JWT_TOKEN = filter_input(INPUT_GET, "jwt", FILTER_SANITIZE_SPECIAL_CHARS);

header('Content-Type: application/json');

if (!$JWT_TOKEN) {
    http_response_code(400);
    echo json_encode(["error" => "Missing JWT token"]);
    exit();
}

$secret_key = $env["JWT_SECRET"];

try {
    $decoded = JWT::decode($JWT_TOKEN, new Key($secret_key, 'HS256'));
    $cluster_leader_id = $decoded->cluster_leader_id;
    $proposal_id = $decoded->proposal_id;

    $stmt = $mysqli->prepare("
        SELECT email, stock_ticker, stock_name, thesis, proposal_file, status 
        FROM wp_2_proposals
        WHERE post_id = ? AND cluster_leader_id = ?
        LIMIT 1
    ");
    $stmt->bind_param("ii", $proposal_id, $cluster_leader_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($row["status"] !== "pending") {
        redirect_to_result("Proposal has already been given a decision", "error");
        exit();
    }

    session_start();
    $_SESSION["cluster_leader_id"] = $cluster_leader_id;
    $_SESSION["proposal_id"] = $proposal_id;
    $_SESSION["auth_to_access"] = true;
    session_write_close();

    echo json_encode(["status" => "authenticated"]);
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(["error" => $e->getMessage()]);
}

exit();