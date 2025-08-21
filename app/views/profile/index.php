<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="/static/css/index.css" >
    <script src="/static/js/profile.js"></script>
</head>
<body>
    <div class="hero-section">
        <h1>User Profile</h1>
        <p>Update your profile or view your holdings</p>
        <br>
        <div>
            <span id="info-tab" class="tab active" onclick="handleToggleScreen('info')">Info</span>
            <span id="holdings-tab" class="tab" onclick="handleToggleScreen('holdings')">Holdings</span>
        </div>
    </div>



    <div id="user-info">
        Update User Info
    </div>

    <div id="user-holdings">
        Holding Section
    </div>
</body>
</html>