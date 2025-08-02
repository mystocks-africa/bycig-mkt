<?php
  $session_coookie = $_COOKIE["session_id"] ?? null;

  if (empty($session_cookie)) {
    header("Location: signin.php");
    exit();
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <button>Sign out</button>
</body>
</html>