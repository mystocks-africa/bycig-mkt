<?php
include '../utils/env.php';
include '../utils/database.php';

$DECLINE_OR_ACCEPT_PROPOSAL = filter_input(INPUT_POST, "decline_or_accept", FILTER_SANITIZE_SPECIAL_CHARS);

if (!in_array($DECLINE_OR_ACCEPT_PROPOSAL, ["accept", "decline"])) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid action"]);
    exit();
}

session_start();
if (!$_SESSION["auth_to_access"]) {
    http_response_code(403);
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$proposal_id = $_SESSION["proposal_id"];
$cluster_leader_id = $_SESSION["cluster_leader_id"];
session_write_close();

$stmt = $mysqli->prepare("
    UPDATE wp_2_proposals
    SET status = ?
    WHERE post_id = ? AND cluster_leader_id = ?
    LIMIT 1
");
$stmt->bind_param("sii", $DECLINE_OR_ACCEPT_PROPOSAL, $proposal_id, $cluster_leader_id);
$stmt->execute();
$stmt->close();

echo json_encode(["status" => "success"]);
exit();