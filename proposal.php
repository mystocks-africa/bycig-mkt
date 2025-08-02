<?php 
    include "utils/redirection.php";
    include 'utils/database.php';

    $PROPOSAL_ID = filter_input(INPUT_GET,"proposal_id", FILTER_SANITIZE_SPECIAL_CHARS);

    if (empty($PROPOSAL_ID)) {
        $message = "Proposal ID query parameter is required";
        redirect_to_result($message, "error");    
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proposal Details</title>
    <script>
        window.serverData = {
            proposal_id: "<?php echo $PROPOSAL_ID ?>"
        }
    </script>

    <script src="static/javascript/proposal.js"></script>
    <link rel="stylesheet" href="static/css/index.css" >
</head>
<body>
    <div id="proposal-container"></div>
</body>
</html>