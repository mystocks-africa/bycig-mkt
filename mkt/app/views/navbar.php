<?php
ob_start();
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="/static/css/index.css">
</head>
<body>
    <nav>
        <a href="/" class="logo">BYCIG MKT</a>
        <div class="nav-toggle" aria-label="Toggle navigation">
            <div class="hamburger">
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
            </div>
            <span class="close"></span>
        </div>
        <ul class="nav-links">
            <!-- JS will populate this container -->        
        </ul>
    </nav>

    <script>
        window.serverData = {
            sessionCookie: <?= isset($_COOKIE['session_id']) ? "true" : "false" ?>
        }
    </script>
    <script src="/static/js/navbar.js"></script>
</body>
</html>