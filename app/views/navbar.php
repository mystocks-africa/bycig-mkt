<?php
ob_start();
$loggedIn = isset($_COOKIE["session_id"]);
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="/static/css/navbar.css">
</head>
<body>
    <nav>
        <a href="/" class="logo">BYCIG MKT</a>
        <ul class="nav-links">
            <?php if ($loggedIn): ?>
                <li><a href="/proposals/submit">Create Proposal</a></li>
                <li>
                    <form action="/auth/signout" method="POST">
                        <button id="signout-btn" type="submit">Sign out</button>
                    </form>  
                </li>  
            <?php else: ?>
                <li><a href="/auth/signin">Sign in</a></li>
                <li><a href="/auth/signup">Sign up</a></li>
                <li><a href="/auth/forgot-pwd">Forgot password?</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</body>
</html>
