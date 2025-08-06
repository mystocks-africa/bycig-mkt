<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sign Up</title>
  <link rel="stylesheet" href="/static/css/index.css">
</head>
<body>
  <form action="/auth/signup" method="post">
    <label for="full_name">Full Name:</label>
    <input type="text" id="full_name" name="full_name"><br><br>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email"><br><br>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password"><br><br>

    <label for="confirm_password">Confirm Password:</label>
    <input type="password" id="confirm_password" name="confirm_password"><br><br>

    <label for="cluster_leader">Cluster Leader (optional):</label>
    <input type="text" id="cluster_leader" name="cluster_leader"><br><br>

    <button type="submit">Sign up</button>
  </form>

  <p>Sign up failed. Please try again.</p>

  <p>Already have an account? <a href="login.php">Login here</a></p>
</body>
</html>