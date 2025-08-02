<?php
$BASE_DIR = __DIR__ . "../";

include $BASE_DIR . 'utils/auth.php';
include $BASE_DIR . 'utils/database.php';
include $BASE_DIR . 'utils/session_details.php';

serverside_check_auth();

$request_method = $_SERVER["REQUEST_METHOD"];
$PROPOSAL_ID = filter_input(INPUT_GET,  "proposal_id", FILTER_SANITIZE_SPECIAL_CHARS);  
$ADMIN_PURPOSE = filter_input(INPUT_GET, "admin_purpose", FILTER_SANITIZE_SPECIAL_CHARS);

if (isset($ADMIN_PURPOSE) && $request_method == "GET") {
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
