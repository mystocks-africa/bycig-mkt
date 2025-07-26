<?php 
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
        header("Location: submit_proposal.php");
        exit;  
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Redirection</title>
    <link rel="stylesheet" href="static/css/index.css">
</head>
<body id="redirect-body">
    <h1 id="main-text"></h1>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <button type="submit" name="submit" value="Submitted">Go back home</button>
    </form>

    <script>
        window.serverData = {
            msg_type: "<?php echo filter_input(INPUT_GET, 'message_type', FILTER_SANITIZE_SPECIAL_CHARS) ?: 'success'; ?>",
            msg: "<?php echo filter_input(INPUT_GET, 'message', FILTER_SANITIZE_SPECIAL_CHARS) ?: 'Thank you :)'; ?>"
        };
    </script>

    <script src="static/javascript/redirect.js"></script>
</body>
</html>
