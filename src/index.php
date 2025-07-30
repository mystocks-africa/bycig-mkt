<?php
include 'utils/database.php';

$GET_ALL_PROPOSALS = filter_input(INPUT_GET,'get_all_proposals', FILTER_SANITIZE_SPECIAL_CHARS);
$request_method = $_SERVER['REQUEST_METHOD'];

if (isset($GET_ALL_PROPOSALS) && $request_method == "GET") {
    header('Content-Type: application/json');

    $get_proposal_query = "
        SELECT email, stock_ticker, stock_name, subject_line, thesis, bid_price, target_price, proposal_file
        FROM wp_2_proposals 
    ";

    try {
        $stmt = $mysqli->prepare($get_proposal_query);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $result_json = json_encode($rows);
        echo $result_json;
        exit();
    } catch (Exception $error) {
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
    <title>Proposal Submissions</title>
    <script src="static/javascript/index.js"></script>
</head>
<body>
    
</body>
</html>