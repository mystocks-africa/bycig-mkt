<?php 
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
        header("Location: index.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank you!</title>
</head>
<body>
    <p id="main-text" />

    <form method="post" action="<?php echo $_SERVER["PHP_SELF"] ?>">
        <button type="submit" name="submit" value="Submitted">Go back home</button>
    </form>

    <script>
        const msg_type = "<?php echo filter_input(INPUT_GET, "message_type", FILTER_SANITIZE_SPECIAL_CHARS); ?>";
        const msg = "<?php echo filter_input(INPUT_GET, "message", FILTER_SANITIZE_SPECIAL_CHARS); ?>";

        const textElement = document.getElementById("main-text");
        textElement.innerHTML = msg;
        textElement.style.color = msg_type == "success" ? "green" : msg_type == "error" ? "red" : "grey";
    </script>
</body>
</html>