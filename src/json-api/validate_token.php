<?php
require '../vendor/autoload.php';
include '../utils/env.php';
include '../utils/database.php';
include '../utils/redirection.php';

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
    $proposal_id = $decoded->proposal_id;

    $stmt = $mysqli->prepare("
        SELECT stock_ticker, stock_name, thesis, proposal_file, status 
        FROM proposals
        WHERE post_id = ? 
        LIMIT 1
    ");
    $stmt->bind_param("i", $proposal_id);
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