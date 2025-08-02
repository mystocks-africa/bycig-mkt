<?php

$BASE_DIR = __DIR__ . "../";

include $BASE_DIR . 'utils/auth.php';
include $BASE_DIR . 'utils/env.php';
include $BASE_DIR . 'utils/database.php';
include $BASE_DIR . 'utils/session_details.php';

serverside_check_auth();

header('Content-Type: application/json');

$DECLINE_OR_ACCEPT_PROPOSAL = filter_input(INPUT_POST, "decline_or_accept", FILTER_SANITIZE_SPECIAL_CHARS);

// Last task in submit proposal workflow, so we clear session variables (true param)
$session = get_session_variables(true);

if (!in_array($DECLINE_OR_ACCEPT_PROPOSAL, ["accept", "decline"])) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid action"]);
    exit();
}

if (!$session["auth_to_access"]) {
    http_response_code(403);
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$proposal_id = $session["proposal_id"];
$cluster_leader_id = $session["cluster_leader_id"];

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