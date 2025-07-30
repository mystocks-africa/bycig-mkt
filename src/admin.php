<?php
require 'vendor/autoload.php';

include 'utils/env.php';
include 'utils/database.php';
include 'utils/redirection.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$JWT_TOKEN = filter_input(INPUT_GET, "jwt", FILTER_SANITIZE_SPECIAL_CHARS);
$DECLINE_OR_ACCEPT_PROPOSAL = filter_input(INPUT_POST, "decline_or_accept", FILTER_SANITIZE_SPECIAL_CHARS);
$GET_PROPOSAL_INFO = filter_input(INPUT_GET, "get_proposal_info", FILTER_SANITIZE_SPECIAL_CHARS);

$request_method = $_SERVER["REQUEST_METHOD"];

function get_session_variables () {
    session_start();
    $cluster_leader_id = $_SESSION["cluster_leader_id"];
    $proposal_id = $_SESSION["proposal_id"];
    $auth_to_access = $_SESSION["auth_to_access"];
    session_abort();

    return [
        "cluster_leader_id"=> $cluster_leader_id,
        "proposal_id"=> $proposal_id,
        "auth_to_access"=> $auth_to_access
    ];
}

if (isset($JWT_TOKEN) && $request_method === "GET") {
    $secret_key = $env["JWT_SECRET"];

    try {
        $decoded = JWT::decode($JWT_TOKEN, new Key($secret_key, 'HS256'));
        $cluster_leader_id = $decoded->cluster_leader_id;
        $proposal_id = $decoded->proposal_id;

        $find_proposal_query = "
            SELECT email, stock_ticker, stock_name, thesis, proposal_file, status 
            FROM wp_2_proposals
            WHERE post_id = ? AND cluster_leader_id =  ?
            LIMIT 1;
        ";

        $stmt = $mysqli->prepare($find_proposal_query);
        $stmt->bind_param("ii", $proposal_id, $cluster_leader_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc(); 

        if ($row["status"] !== "pending") {
            $message = "Proposal has already been given a decison";
            $stmt->close();
            redirect_to_result($message, "error");
            exit();
        }

        session_start();
        $_SESSION["cluster_leader_id"] = $cluster_leader_id;
        $_SESSION["proposal_id"] = $proposal_id;
        $_SESSION["auth_to_access"] = true;
        session_write_close();

        $stmt->close();
    } catch(Exception $error) {
        $error_msg = $error->getMessage();
        echo $error_msg;
    } 
} else if (isset($DECLINE_OR_ACCEPT_PROPOSAL) && $request_method == "POST") {

    if ($DECLINE_OR_ACCEPT_PROPOSAL !== "accept" && $DECLINE_OR_ACCEPT_PROPOSAL !== "decline") {
        $error = json_encode([
            "error" => "Query paramater must be either accept or decline"
        ]);

        echo $error;
        exit();
    }

    $session = get_session_variables();

    if ($session["auth_to_access"]) {
        $update_proposal_query = "
            UPDATE wp_2_proposals
            SET status = ?
            WHERE post_id = ? AND cluster_leader_id = ?
            LIMIT 1;
        ";
        
        $stmt = $mysqli->prepare($update_proposal_query);
        $stmt->bind_param(
            "sii", 
            $DECLINE_OR_ACCEPT_PROPOSAL, 
            $session["proposal_id"], 
            $session["cluster_leader_id"]
        );
        $stmt->execute();  
    }
} else if (isset($GET_PROPOSAL_INFO) && $request_method == "GET") {
    $session = get_session_variables();

    $get_proposal_info_query = "
        SELECT email, stock_ticker, stock_name, subject_line, thesis, bid_price, target_price, proposal_file
        FROM wp_2_proposals 
        WHERE post_id = ? AND cluster_leader_id = ?
        LIMIT 1;  
    ";
    try {
        $stmt = $mysqli->prepare($get_proposal_info_query);
        $stmt->bind_param(
            "ii",
            $session["proposal_id"],
            $session["cluster_leader_id"]
        );
        $stmt->execute();
        $result = $stmt->get_result();
        $get_proposal_info = $result->fetch_assoc();
        $stmt->close();  
        $get_proposal_info_json = json_encode($get_proposal_info);
        echo $get_proposal_info_json;  
        exit();    
    } catch(Exception $error) {
        $error_message = json_encode([
            "error" => $error->getMessage()
        ]);
        echo $error_message;
        exit();
    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <script src="static/javascript/admin.js"></script>
</head>
<body>
    <div id="content"></div>
    <button onclick="handleSubmit('accept')">Accept proposal</button>
    <button onclick="handleSubmit('decline')">Decline proposal</button>
</body>
</html>