<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot your password?</title>
</head>
<body>
    <h2>Forgot your password?</h2>
    <form action="/forgot-pwd" method="POST">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required placeholder="Enter your email">
        <br><br>
        <button type="submit">Send Reset Code</button>
    </form>
</body>
</html>
