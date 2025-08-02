<?php
  $session_id_cookie = $_COOKIE["session_id"] ?? null;
  
  if (empty($session_id_cookie)) {
    header("Location: signin.php");
    exit();
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Out</title>
</head>
<body>
    <form action="json-api/auth/signout.php" method="get">
      <button type="submit">Sign out</button>
    </form>
</body>
</html>