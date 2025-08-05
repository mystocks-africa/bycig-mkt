<head>
    <link rel="stylesheet" href="/static/css/navbar.css">
</head>
    <nav>
        <a href="/" class="logo">BYCIG</a>

        <button class="nav-toggle" aria-label="Toggle navigation">
            <span class="hamburger"></span>
        </button>

        <ul class="nav-links">
            <li><a href="/">Home</a></li>
            <li><a href="/proposals/submit">Create Proposal</a></li>
            <li><a href="/auth/signin">Sign in</a></li>
            <li><a href="/auth/signup">Sign up</a></li>
            <li><a href="/auth/signout">Sign out</a></li>
        </ul>
    </nav>

    <div class="nav-overlay"></div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get elements
            const navToggle = document.querySelector('.nav-toggle');
            const navLinks = document.querySelector('.nav-links');
            const navOverlay = document.querySelector('.nav-overlay');

            // Toggle nav menu on small screens
            navToggle.addEventListener('click', function() {
                navLinks.classList.toggle('active');
                navToggle.classList.toggle('active');
                navOverlay.classList.toggle('active');
            });

            // Close menu when clicking on overlay
            navOverlay.addEventListener('click', function() {
                navLinks.classList.remove('active');
                navToggle.classList.remove('active');
                navOverlay.classList.remove('active');
            });

            // Close menu when clicking on a nav link (useful for single-page sites)
            navLinks.addEventListener('click', function(e) {
                if (e.target.tagName === 'A') {
                    navLinks.classList.remove('active');
                    navToggle.classList.remove('active');
                    navOverlay.classList.remove('active');
                }
            });

            // Close menu when pressing Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && navLinks.classList.contains('active')) {
                    navLinks.classList.remove('active');
                    navToggle.classList.remove('active');
                    navOverlay.classList.remove('active');
                }
            });
        });
    </script>
</html>