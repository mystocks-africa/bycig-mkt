<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sign In</title>
  <link rel="stylesheet" href="/static/css/index.css">
</head>
<body>
  <div class="hero-section">
    <h1>Sign in</h1>
    <p>If you already have an account, use this form to authenticate yourself</p>
    <br>
  </div>
  <form action="/auth/signin" method="post">
    <label for="email">Email:</label>
    <input type="email" id="email" name="email"><br><br>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password"><br><br>

    <button type="submit">Sign in</button>
  </form>
</body>
</html>