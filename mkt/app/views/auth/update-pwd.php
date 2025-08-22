<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Password</title>
    <link rel="stylesheet" href="/static/css/index.css">
</head>
<body>
    <h2>Update Your Password</h2>
    <form action="/auth/update-pwd" method="POST">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required placeholder="Enter your email">
        <br><br>

        <label for="code">Verification Code:</label>
        <input type="text" id="code" name="code" required placeholder="Enter the code you received">
        <br><br>

        <label for="pwd">New Password:</label>
        <input type="password" id="pwd" name="pwd" required placeholder="Enter your new password">
        <br><br>

        <button type="submit">Update Password</button>
    </form>
</body>
</html>
