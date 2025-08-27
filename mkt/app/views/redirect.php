<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Redirection</title>
    <link rel="stylesheet" href="/static/css/index.css">
</head>
<body class="redirect-body">
    <h1 id="main-text"></h1>
    <button onclick="goBackHome()">Go back home</button>
        <script>
        // Pass PHP data to js file
        window.serverData = {
            msg_type: "<?php echo filter_input(INPUT_GET, 'message_type', FILTER_SANITIZE_SPECIAL_CHARS) ?: 'success'; ?>",
            msg: "<?php echo filter_input(INPUT_GET, 'message', FILTER_SANITIZE_SPECIAL_CHARS) ?: 'Thank you :)'; ?>"
        };
    </script>
    <script src="static/js/redirect.js"></script>
</body>
</html>