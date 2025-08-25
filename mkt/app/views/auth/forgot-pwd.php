<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot your password?</title>
    <link rel="stylesheet" href="/static/css/index.css">
</head>
<body>
    <div class="hero-section">
        <h1>Forgot your password?</h1>
        <p>Simply enter your email for further instructions</p>
        <br>
    </div>

    <form action="/auth/forgot-pwd" method="POST">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required placeholder="Enter your email">
        <br><br>
        <button type="submit">Send Reset Code</button>
    </form>
</body>
</html>
