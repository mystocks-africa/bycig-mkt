<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Navbar</title>
    <link rel="stylesheet" href="/static/css/navbar.css">
</head>
<body>
    <nav class="navbar">
        <div class="logo">BYCIG</div>

        <button class="nav-toggle" aria-label="Toggle navigation">
            <span class="hamburger"></span>
        </button>

        <ul class="nav-links">
            <li><a href="/">Home</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#services">Services</a></li>
            <li><a href="#contact">Contact</a></li>
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
</body>
</html>