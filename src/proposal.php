<?php 
    include "utils/redirection.php";
    include 'utils/database.php';

    $PROPOSAL_ID = filter_input(INPUT_GET,"proposal_id", FILTER_SANITIZE_SPECIAL_CHARS);
    $GET_PROPOSAL = filter_input(INPUT_GET,"get_proposal", FILTER_SANITIZE_SPECIAL_CHARS);
    $request_method = $_SERVER["REQUEST_METHOD"];

    if (empty($PROPOSAL_ID)) {
        $message = "Proposal ID query parameter is required";
        redirect_to_result($message, "error");    
    }

    if (isset($GET_ALL_PROPOSALS) && isset($PROPOSAL_ID) && $request_method == "GET") {
        header('Content-Type: application/json');

        $get_proposal_query = "
            SELECT email, stock_ticker, stock_name, subject_line, thesis, bid_price, target_price, proposal_file, status
            FROM wp_2_proposals 
            WHERE post_id = ? 
            LIMIT 1;         
        ";

        $stmt = $mysqli->prepare($get_proposal_query);
        $stmt->bind_param("i", $PROPOSAL_ID);
        $stmt->execute();
        $result = $stmt->get_result();
        $get_proposal_info = $result->fetch_assoc();

        $get_proposal_info_json = json_encode($get_proposal_info);
        echo $get_proposal_info_json;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proposal Details</title>
    <script>
        window.serverData {
            proposal_id: "<?php echo $PROPOSAL_ID ?>"
        }
    </script>

    <script src="static/javascript/proposal.js"></script>
    <link rel="stylesheet" href="static/css/index.css" >
</head>
<body>
    
</body>
</html>