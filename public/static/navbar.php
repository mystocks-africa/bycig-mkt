<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Navbar</title>
    <style>
        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Navbar container */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background: #fff;
            color: #000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 1.5rem;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            min-height: 56px;
        }

        /* Logo text */
        .logo {
            font-weight: 700;
            font-size: 1.5rem;
            letter-spacing: 1px;
            color: #000;
        }

        /* Navigation links */
        .nav-links {
            list-style: none;
            display: flex;
            gap: 1.5rem;
            margin: 0;
            padding: 0;
        }

        /* Links styling */
        .nav-links a {
            color: #000;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
            padding: 0.5rem 0;
        }

        /* Hover effect */
        .nav-links a:hover {
            color: #555;
        }

        /* Hamburger button - hidden on desktop */
        .nav-toggle {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            padding: 0.5rem;
            z-index: 1001;
        }

        .hamburger,
        .hamburger::before,
        .hamburger::after {
            display: block;
            background-color: #000;
            height: 3px;
            width: 25px;
            border-radius: 3px;
            position: relative;
            transition: all 0.3s ease;
        }

        .hamburger::before,
        .hamburger::after {
            content: '';
            position: absolute;
            left: 0;
        }

        .hamburger::before {
            top: -8px;
        }

        .hamburger::after {
            top: 8px;
        }

        /* Hamburger animation when active */
        .nav-toggle.active .hamburger {
            background-color: transparent;
        }

        .nav-toggle.active .hamburger::before {
            transform: rotate(45deg);
            top: 0;
        }

        .nav-toggle.active .hamburger::after {
            transform: rotate(-45deg);
            top: 0;
        }

        /* Responsive styles */
        @media (max-width: 768px) {
            /* Show hamburger */
            .nav-toggle {
                display: block;
            }

            /* Hide nav links by default */
            .nav-links {
                position: fixed;
                top: 56px;
                right: 0;
                background: #fff;
                flex-direction: column;
                width: 250px;
                max-width: 80vw;
                height: calc(100vh - 56px);
                padding: 1rem 0;
                gap: 0;
                box-shadow: -2px 0 10px rgba(0, 0, 0, 0.1);
                transform: translateX(100%);
                transition: transform 0.3s ease;
                overflow-y: auto;
                z-index: 999;
            }

            /* When active, slide in */
            .nav-links.active {
                transform: translateX(0);
            }

            /* Nav links styling in mobile */
            .nav-links li {
                width: 100%;
            }

            .nav-links a {
                display: block;
                padding: 1rem 1.5rem;
                width: 100%;
                border-bottom: 1px solid #f0f0f0;
            }

            .nav-links a:hover {
                background-color: #f8f8f8;
            }
        }

        /* Body styles and demo content */
        body {
            padding-top: 56px;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
        }

        .content {
            padding: 2rem 1.5rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .demo-section {
            margin-bottom: 2rem;
            padding: 2rem;
            background: #f8f9fa;
            border-radius: 8px;
        }

        /* Overlay for mobile menu */
        .nav-overlay {
            display: none;
            position: fixed;
            top: 56px;
            left: 0;
            width: 100%;
            height: calc(100vh - 56px);
            background: rgba(0, 0, 0, 0.5);
            z-index: 998;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .nav-overlay.active {
            display: block;
            opacity: 1;
        }

        @media (max-width: 768px) {
            .nav-overlay {
                display: block;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo">MySite</div>

        <button class="nav-toggle" aria-label="Toggle navigation">
            <span class="hamburger"></span>
        </button>

        <ul class="nav-links">
            <li><a href="#home">Home</a></li>
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