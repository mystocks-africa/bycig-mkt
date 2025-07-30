<?php
include '../utils/database.php';

$request_method = $_SERVER["REQUEST_METHOD"];
$PROPOSAL_ID = filter_input(INPUT_GET,  "proposal_id", FILTER_SANITIZE_SPECIAL_CHARS);  
$ADMIN_PURPOSE = filter_input(INPUT_GET, "admin_purpose", FILTER_SANITIZE_SPECIAL_CHARS);

function get_session_variables ($delete_vars = false) {
    session_start();
    $cluster_leader_id = $_SESSION["cluster_leader_id"];
    $proposal_id = $_SESSION["proposal_id"];
    $auth_to_access = $_SESSION["auth_to_access"];

    if ($delete_vars) {
        $_SESSION = [];
        session_destroy();
        session_abort();
    } else {
        session_write_close();
    }

    return [
        "cluster_leader_id"=> $cluster_leader_id,
        "proposal_id"=> $proposal_id,
        "auth_to_access"=> $auth_to_access
    ];

}

if (isset($ADMIN_PURPOSE) && $request_method == "GET") {
    $session = get_session_variables(true);

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

else if (isset($PROPOSAL_ID) && $request_method == "GET") {
    header('Content-Type: application/json');

    $get_proposal_query = "
        SELECT email, stock_ticker, stock_name, subject_line, thesis, bid_price, target_price, proposal_file, status
        FROM wp_2_proposals 
        WHERE post_id = ? 
        LIMIT 1;         
    ";

    try {
        $stmt = $mysqli->prepare($get_proposal_query);
        $stmt->bind_param("i", $PROPOSAL_ID);
        $stmt->execute();
        $result = $stmt->get_result();
        $get_proposal_info = $result->fetch_assoc();
        
        $get_proposal_info_json = json_encode($get_proposal_info);
        echo $get_proposal_info_json;
        exit();
    } catch (Exception $error) {
        $error_message = json_encode([
            "error"=> $error->getMessage()
        ]);

        echo $error_message;
        exit();
    }
} 
