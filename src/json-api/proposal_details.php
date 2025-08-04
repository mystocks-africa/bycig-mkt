<?php
$BASE_DIR = "../";

include $BASE_DIR . 'utils/auth.php';
include $BASE_DIR . 'utils/database.php';
include $BASE_DIR . 'utils/session_details.php';

serverside_check_auth();

$request_method = $_SERVER["REQUEST_METHOD"];
$PROPOSAL_ID = filter_input(INPUT_GET,  "proposal_id", FILTER_SANITIZE_SPECIAL_CHARS);  
$ADMIN_PURPOSE = filter_input(INPUT_GET, "admin_purpose", FILTER_SANITIZE_SPECIAL_CHARS);

if (isset($ADMIN_PURPOSE) && $request_method == "GET") {
    header("Content-Type: application/json");
    $session = get_session_variables();

    $get_proposal_info_query = "
        SELECT stock_ticker, stock_name, subject_line, thesis, bid_price, target_price, proposal_file, full_name 
        FROM proposals 
        WHERE post_id = ?
        INNER JOIN users 
        ON proposals.post_author = users.email;
    ";
    try {
        $stmt = $mysqli->prepare($get_proposal_info_query);
        $stmt->bind_param(
            "i",
            $session["proposal_id"],
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
        SELECT stock_ticker, stock_name, subject_line, thesis, bid_price, target_price, proposal_file, full_name, email
        FROM proposals 
        INNER JOIN users 
        ON proposals.post_author = users.email 
        WHERE post_id = ?;
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
