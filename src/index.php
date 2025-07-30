<?php
include 'utils/database.php';

$GET_ALL_PROPOSALS = filter_input(INPUT_GET,'get_all_proposals', FILTER_SANITIZE_SPECIAL_CHARS);
$request_method = $_SERVER['REQUEST_METHOD'];

if (isset($GET_PROPOSAL_INFO) && $request_method == "GET") {
    $get_proposal_query = "
        SELECT email, stock_ticker, stock_name, subject_line, thesis, bid_price, target_price, proposal_file
        FROM wp_2_proposals 
    ";

    $stmt = $mysqli->prepare($get_proposal_query);
    $stmt->bind_param("s", $request_method);
    $stmt->execute();
    $stmt->close();
    $result = $stmt->get_result();

    $result_json = json_encode($result);
    echo $result_json;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proposal Submissions</title>
    <script src="static/javascript/index.js"></script>
</head>
<body>
    
</body>
</html>