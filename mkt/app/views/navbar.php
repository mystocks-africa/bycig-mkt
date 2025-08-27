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
        <button class="nav-toggle" aria-label="Toggle navigation">
            <span class="hamburger"></span>
        </button>
        <ul class="nav-links">
            <li><a href="/auth/forgot-pwd">Forgot password?</a></li>
            <?php if ($loggedIn): ?>
                <li><a href="/proposals/submit">Create Proposal</a></li>
                <li><a href="/profile">User Profile</a></li>
                <li>
                    <form action="/auth/signout" method="POST">
                        <button id="signout-btn" type="submit">Sign out</button>
                    </form>  
                </li>  
            <?php else: ?>
                <li><a href="/auth/signin">Sign in</a></li>
                <li><a href="/auth/signup">Sign up</a></li>
            <?php endif; ?>
        </ul>
    </nav>
    <div class="nav-overlay"></div>
    
    <script>
        const navToggle = document.querySelector('.nav-toggle');
        const navLinks = document.querySelector('.nav-links');
        const navOverlay = document.querySelector('.nav-overlay');

        navToggle.addEventListener('click', () => {
            navToggle.classList.toggle('active');
            navLinks.classList.toggle('active');
            navOverlay.classList.toggle('active');
        });

        navOverlay.addEventListener('click', () => {
            navToggle.classList.remove('active');
            navLinks.classList.remove('active');
            navOverlay.classList.remove('active');
        });

        // Close menu when clicking on a link
        navLinks.addEventListener('click', (e) => {
            if (e.target.tagName === 'A') {
                navToggle.classList.remove('active');
                navLinks.classList.remove('active');
                navOverlay.classList.remove('active');
            }
        });
    </script>
</body>
</html>