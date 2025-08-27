<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="/static/css/navbar.css">
</head>
<body>
    <!-- MOBILE SCREEN -->
    <nav>
        <a href="/" class="logo">BYCIG MKT</a>
        <div class="nav-toggle" aria-label="Toggle navigation">
            <div class="bar"></div>
            <div class="bar"></div>
            <div class="bar"></div>
        </div>
        <ul class="nav-links">
            <li><a href="/auth/forgot-pwd">Forgot password?</a></li>
            <li><a href="/proposals/submit">Create Proposal</a></li>
            <li><a href="/profile">User Profile</a></li>
            <li>
                <form action="/auth/signout" method="POST">
                    <button id="signout-btn" type="submit">Sign out</button>
                </form>  
            </li>  
        </ul>
    </nav>

    <script src="/static/js/navbar.js"></script>
</body>
</html>