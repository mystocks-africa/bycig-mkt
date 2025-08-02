<?php
  $session_id_cookie = $_COOKIE['session_id'] ?? null;

  if (isset($session_id_cookie)) {
    header("Location: signout.php");
    exit();
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sign In</title>
  <link rel="stylesheet" href="static/css/index.css">
</head>
<body>
  <h1>Sign In</h1>
  <form action="json-api/auth/signin.php" method="post">
    <label for="email">Email:</label>
    <input type="email" id="email" name="email"><br><br>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password"><br><br>

    <input type="submit" value="Sign Up">
  </form>

  <p>Sign up failed. Please try again.</p>

  <p>Already have an account? <a href="login.php">Login here</a></p>
</body>
</html>
