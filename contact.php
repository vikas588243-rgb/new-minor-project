<?php
// Start session at the beginning
session_start();
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - The Daily Dose Podcast</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Custom styles for the Inter font and a subtle background */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc; /* A very light, subtle grey-blue from homepage */
            color: #334155; /* Dark slate grey for text from homepage */
            transition: background-color 0.5s ease, color 0.5s ease;
        }

        /* Dark mode styles */
        body.dark {
            background-color: #1a202c; /* bg-gray-900 */
            color: #e2e8f0; /* text-gray-200 */
        }
        
        body.dark .bg-white\/30 {
            background-color: rgba(30, 41, 59, 0.3); /* dark:bg-slate-800/30 */
        }
        
        body.dark .shadow-md {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2), 0 2px 4px -1px rgba(0, 0, 0, 0.12); /* dark:shadow-lg */
        }
        
        body.dark .text-gray-800 {
            color: #e2e8f0; /* dark:text-gray-200 */
        }
        
        body.dark .text-gray-600 {
            color: #a0aec0; /* dark:text-gray-400 */
        }

        body.dark .hover\:text-gray-900:hover {
            color: #fff; /* dark:hover:text-white */
        }

        body.dark .bg-gray-200 {
            background-color: #4a5568; /* dark:bg-gray-700 */
            color: #e2e8f0; /* dark:text-gray-200 */
        }
        
        body.dark .hover\:bg-gray-300:hover {
            background-color: #616e81; /* dark:hover:bg-gray-600 */
        }
        
        body.dark .bg-indigo-600 {
            background-color: #667eea; /* dark:bg-indigo-500 */
        }
        
        body.dark .hover\:bg-indigo-700:hover {
            background-color: #5a67d8; /* dark:hover:bg-indigo-600 */
        }
        
        body.dark .bg-white {
            background-color: #2d3748;
        }
        
        body.dark .hover\:bg-indigo-100:hover {
            background-color: #5a67d8;
        }

        body.dark .bg-gray-50 {
            background-color: #2d3748;
        }

        body.dark .border-gray-200 {
            border-color: #4a5568;
        }

        body.dark .bg-gray-100 {
            background-color: #2d3748;
        }
        
        body.dark .shadow-inner {
            box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.3);
        }

        body.dark .text-gray-900 {
            color: #e2e8f0;
        }

        body.dark .text-gray-700 {
            color: #e2e8f0;
        }
        
        body.dark .text-gray-500 {
            color: #a0aec0;
        }
        
        body.dark .border-gray-300 {
            border-color: #4a5568;
        }

        body.dark footer .text-gray-400 {
            color: #a0aec0; /* Lighter footer links */
        }
        
        body.dark footer .text-gray-400:hover {
            color: #fff; /* White hover color for better visibility */
        }

        body.dark input, body.dark textarea {
            background-color: #4a5568;
            border-color: #616e81;
            color: #fff;
        }

        body.dark input::placeholder, body.dark textarea::placeholder {
            color: #a0aec0;
        }

        /* Style for the play button icon (though not used for overlay anymore, keeping for general use if needed) */
        .play-icon {
            width: 24px;
            height: 24px;
            fill: currentColor;
        }
        /* Animation for link click feedback */
        @keyframes pulse-once {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.05); opacity: 0.7; }
            100% { transform: scale(1); opacity: 1; }
        }
        .animate-pulse-once {
            animation: pulse-once 0.3s ease-in-out;
        }
        /* Dark mode toggle icon animation */
        .dark-mode-icon {
            transition: transform 0.3s ease-in-out;
        }
        .dark-mode-icon.rotate {
            transform: rotate(180deg);
        }
        
        /* Fix for navbar styling */
        .nav-link {
            position: relative;
            display: inline-block;
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -4px;
            left: 50%;
            background-color: #4f46e5;
            transition: width 0.3s ease, left 0.3s ease;
        }
        
        .nav-link:hover::after {
            width: 100%;
            left: 0;
        }
    </style>
</head>
<body class="antialiased">

    <!-- Fixed container for the navbar, copied from the homepage -->
    <div class="fixed top-0 left-0 right-0 z-50">
        <!-- The actual navbar, now centered within the fixed container -->
        <header class="bg-white/30 backdrop-blur-md shadow-md rounded-full mx-auto max-w-7xl mt-4 px-8 py-3 flex items-center justify-between relative">
            <!-- Logo/Site Title -->
            <a href="index.php" class="flex items-center space-x-2 mr-auto">
                <div class="w-8 h-8 md:w-10 md:h-10 bg-indigo-600 rounded-full"></div>
                <span class="text-lg md:text-xl font-semibold text-gray-800">The Daily Dose</span>
            </a>

            <!-- Mobile Menu Toggle -->
            <button id="mobile-menu-button" class="md:hidden p-2 rounded-md text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>

            <!-- Navigation Links (Hidden on smaller screens) -->
            <nav id="main-nav" class="hidden md:flex flex-grow justify-center items-center space-x-6 md:space-x-8">
                <a href="index.php" class="nav-link text-gray-600 hover:text-gray-900 font-medium transition duration-300">Home</a>
                <a href="index.php#episodes-section" class="nav-link text-gray-600 hover:text-gray-900 font-medium transition duration-300">Episodes</a>
                <a href="trending.php" class="nav-link text-gray-600 hover:text-gray-900 font-medium transition duration-300">Trending</a>
                <a href="videos.php" class="nav-link text-gray-600 hover:text-gray-900 font-medium transition duration-300">My Videos</a>
                <a href="index.php#about-section" class="nav-link text-gray-600 hover:text-gray-900 font-medium transition duration-300">About Us</a>
                <a href="#contact-section" class="nav-link text-gray-600 hover:text-gray-900 font-medium transition duration-300">Contact</a>
            </nav>

            <!-- Action Buttons (Hidden on smaller screens, shown next to nav links on larger screens) -->
            <div class="hidden md:flex space-x-4 ml-auto items-center">
                <button id="theme-toggle" class="p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors duration-300 ease-in-out">
                    <svg id="sun-icon" class="dark-mode-icon w-6 h-6 text-gray-700 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <svg id="moon-icon" class="dark-mode-icon w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                    </svg>
                </button>
                <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
                    <span class="text-gray-700">Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?></span>
                    <a href="logout.php" class="bg-indigo-600 text-white px-4 py-2 rounded-full hover:bg-indigo-700">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="nav-link bg-gray-200 text-gray-800 font-semibold px-4 py-2 rounded-full hover:bg-gray-300 transition duration-300">Log In</a>
                    <a href="register.php" class="nav-link bg-indigo-600 text-white font-semibold px-4 py-2 rounded-full hover:bg-indigo-700 transition duration-300">Sign Up</a>
                <?php endif; ?>
            </div>
        </header>
    </div>

    <!-- Mobile Menu, positioned outside the header for mobile view -->
    <div id="mobile-menu" class="hidden md:hidden fixed top-24 left-4 right-4 bg-white/90 backdrop-blur-lg rounded-xl shadow-lg p-6 flex flex-col space-y-4 z-40">
        <nav class="flex flex-col space-y-2">
            <a href="index.php" class="nav-link text-gray-600 hover:text-indigo-700 font-medium transition duration-300">Home</a>
            <a href="index.php#episodes-section" class="nav-link text-gray-600 hover:text-indigo-700 font-medium transition duration-300">Episodes</a>
            <a href="trending.php" class="nav-link text-gray-600 hover:text-indigo-700 font-medium transition duration-300">Trending</a>
            <a href="videos.php" class="nav-link text-gray-600 hover:text-indigo-700 font-medium transition duration-300">My Videos</a>
            <a href="index.php#about-section" class="nav-link text-gray-600 hover:text-indigo-700 font-medium transition duration-300">About Us</a>
            <a href="#contact-section" class="nav-link text-gray-600 hover:text-indigo-700 font-medium transition duration-300">Contact</a>
        </nav>
        <div class="flex flex-col space-y-3 mt-4 pt-4 border-t border-gray-200">
            <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
                <span class="text-gray-700 text-center">Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?></span>
                <a href="logout.php" class="nav-link w-full text-center bg-indigo-600 text-white font-semibold px-4 py-2 rounded-full hover:bg-indigo-700 transition duration-300">Logout</a>
            <?php else: ?>
                <a href="login.php" class="nav-link w-full text-center bg-gray-200 text-gray-800 font-semibold px-4 py-2 rounded-full hover:bg-gray-300 transition duration-300">Log In</a>
                <a href="register.php" class="nav-link w-full text-center bg-indigo-600 text-white font-semibold px-4 py-2 rounded-full hover:bg-indigo-700 transition duration-300">Sign Up</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Main Content Area: Contact Info and Form -->
    <div id="contact-section" class="min-h-screen py-36 px-4 sm:px-6 lg:px-8 flex flex-col items-center">
        <!-- Hero/Header Section -->
        <div class="max-w-3xl w-full bg-white shadow-lg rounded-xl p-8 sm:p-10 lg:p-12 text-center mb-12">
            <h1 class="text-5xl font-extrabold text-gray-900 mb-4 tracking-tight">Reach Out to Our Podcast</h1>
            <p class="text-xl text-gray-600 leading-relaxed">
                We value your thoughts and questions! Whether it's a topic suggestion, guest inquiry, or just a friendly hello,
                we're eager to hear from you.
            </p>
        </div>

        <!-- Main Content Area: Contact Info and Form -->
        <div class="max-w-5xl w-full bg-white shadow-lg rounded-xl p-8 sm:p-10 lg:p-12 grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16">
            <!-- Contact Information Side -->
            <div class="lg:pr-8">
                <h2 class="text-3xl font-bold text-gray-900 mb-6 rounded-md">How to Connect</h2>
                <div class="space-y-6">
                    <div class="flex items-start space-x-4">
                        <svg class="h-8 w-8 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        <div>
                            <p class="text-lg font-semibold text-gray-800">Email Us</p>
                            <a href="mailto:contact@yourpodcast.com" class="text-emerald-600 hover:text-emerald-800 text-base rounded-md">contact@yourpodcast.com</a>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mt-10 mb-4 rounded-md">Follow Us on Social Media</h3>
                    <div class="flex space-x-6 justify-center lg:justify-start">
                        <!-- Social Media Icons (replace with actual links) -->
                        <a href="#" class="text-gray-500 hover:text-emerald-600 transition duration-300 rounded-full">
                            <svg class="h-10 w-10" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.007-.532A8.318 8.318 0 0022 5.092a8.192 8.192 0 01-2.357.646 4.11 4.11 0 001.804-2.27 8.22 8.22 0 01-2.606.996 4.106 4.106 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.073 4.073 0 01.4 10.70c-.095.003-.19.006-.285.006a4.105 4.105 0 003.292 4.025 4.105 4.105 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.204 8.204 0 01.92 18.067a11.602 11.602 0 006.315 1.84z"></path></svg>
                            <span class="sr-only">Twitter</span>
                        </a>
                        <a href="#" class="text-gray-500 hover:text-emerald-600 transition duration-300 rounded-full">
                            <svg class="h-10 w-10" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.776-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd"></path></svg>
                            <span class="sr-only">Facebook</span>
                        </a>
                        <a href="#" class="text-gray-500 hover:text-emerald-600 transition duration-300 rounded-full">
                            <svg class="h-10 w-10" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M12 2C6.477 2 2 6.477 2 12c0 4.286 2.76 7.91 6.602 9.206.48.089.654-.208.654-.462 0-.227-.008-.827-.013-1.62-.038-.344-.16-.677-.323-.974-.282-.511-.643-.97-.932-1.355-.25-.333-.505-.69-.747-1.07-.267-.406-.523-.836-.782-1.284-.234-.41-.453-.846-.667-1.295-.195-.41-.377-.847-.54-1.299-.148-.396-.282-.803-.404-1.217-.11-.37-.206-.75-.29-1.134-.075-.33-.135-.667-.184-1.006-.044-.296-.07-.597-.08-.9-.008-.23-.006-.46-.003-.692a1.53 1.53 0 011.53-1.53c.69 0 1.25.56 1.25 1.25s-.56 1.25-1.25 1.25H9.5c0-.128.006-.256.018-.384.015-.168.04-.334.075-.498.037-.17.086-.337.147-.5.06-.16.13-.317.21-.47.087-.158.183-.31.29-.45.11-.14.23-.275.36-.405.13-.13.27-.25.42-.36.15-.11.31-.21.48-.3.17-.09.35-.17.53-.24.19-.07.39-.13.6-.18.21-.05.43-.08.65-.09.22-.01.44-.01.66 0 .22.01.44.04.66.08.21.04.41.1.6.17.18.07.35.15.51.24.16.09.3.2.43.32.12.12.23.25.33.39.1.14.19.29.27.44.08.15.15.31.21.47.06.16.1.33.13.5.03.17.05.34.06.51.01.18.01.36.01.54 0 .22-.01.44-.03.66-.02.22-.05.44-.09.66-.04.22-.09.44-.15.66-.06.22-.13.44-.21.66-.08.22-.17.44-.27.66-.1.22-.21.44-.33.66-.12.22-.25.44-.39.66-.14.22-.29.44-.45.66-.16.22-.33.44-.52.66-.22.22-.45.44-.7.66-.25.22-.51.44-.78.66-.27.22-.55.44-.84.66-.29.22-.59.44-.9.66-.31.22-.63.44-.96.66-.33.22-.67.44-1.02.66-.35.22-.7.44-1.07.66-.37.22-.75.44-1.14.66-.39.22-.79.44-1.2.66-.41.22-.84.44-1.28.66-.44.22-.9.44-1.37.66C6.703 21.056 9.208 22 12 22z" clip-rule="evenodd"></path></svg>
                            <span class="sr-only">Instagram</span>
                        </a>
                        <a href="#" class="text-gray-500 hover:text-emerald-600 transition duration-300 rounded-full">
                            <svg class="h-10 w-10" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 10.17V8.825l5.352 2.222-5.352 2.222z"></path></svg>
                            <span class="sr-only">YouTube</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Contact Form Side -->
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-6 rounded-md">Send Us a Message</h2>
                <form action="contact_process.php" method="POST" class="space-y-6">
                    <?php
                    if (isset($_GET['success'])) {
                        echo '<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded">
                            <p>Thank you for your message! We\'ll get back to you soon.</p>
                        </div>';
                    }
                    if (isset($_GET['error'])) {
                        echo '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded">
                            <p>' . htmlspecialchars($_GET['error']) . '</p>
                        </div>';
                    }
                    ?>
                    <div>
                        <label for="name" class="block text-base font-medium text-gray-700">Your Name</label>
                        <input type="text" name="name" id="name" autocomplete="name" required
                            class="mt-1 block w-full px-5 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 text-base">
                    </div>
                    <div>
                        <label for="email" class="block text-base font-medium text-gray-700">Email Address</label>
                        <input type="email" name="email" id="email" autocomplete="email" required
                            class="mt-1 block w-full px-5 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 text-base">
                    </div>
                    <div>
                        <label for="message" class="block text-base font-medium text-gray-700">Your Message</label>
                        <textarea id="message" name="message" rows="5" required
                            class="mt-1 block w-full px-5 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 text-base"></textarea>
                    </div>
                    <div>
                        <button type="submit" class="w-full flex justify-center py-3 px-8 border border-transparent rounded-lg shadow-md text-xl font-semibold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-300">
                            Send Message
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer for additional info - now outside the main content wrapper -->
    <footer class="w-full bg-gray-800 text-white py-8 mt-16">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p>&copy; 2025 The Daily Dose Podcast. All rights reserved.</p>
            <p class="mt-2 text-sm">Made with passion for podcasting.</p>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mainNav = document.getElementById('main-nav');
            const navLinks = document.querySelectorAll('.nav-link, #main-nav a');
            const mobileMenu = document.getElementById('mobile-menu');

            // Theme toggle elements
            const themeToggleBtn = document.getElementById('theme-toggle');
            const sunIcon = document.getElementById('sun-icon');
            const moonIcon = document.getElementById('moon-icon');

            // Function to set the theme from local storage
            const setThemeFromLocalStorage = () => {
                const savedTheme = localStorage.getItem('theme');
                const isDark = savedTheme === 'dark';
                if (isDark) {
                    document.body.classList.add('dark');
                    sunIcon.classList.remove('hidden');
                    moonIcon.classList.add('hidden');
                } else {
                    document.body.classList.remove('dark');
                    sunIcon.classList.add('hidden');
                    moonIcon.classList.remove('hidden');
                }
            };
            
            // On page load, set the theme
            setThemeFromLocalStorage();

            // Toggle mobile menu visibility
            mobileMenuButton.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
            
            // Toggle theme on button click
            themeToggleBtn.addEventListener('click', () => {
                const isDark = document.body.classList.toggle('dark');
                if (isDark) {
                    localStorage.setItem('theme', 'dark');
                    sunIcon.classList.remove('hidden');
                    moonIcon.classList.add('hidden');
                } else {
                    localStorage.setItem('theme', 'light');
                    sunIcon.classList.add('hidden');
                    moonIcon.classList.remove('hidden');
                }
                // Add a simple animation to the icon
                themeToggleBtn.classList.add('rotate');
                setTimeout(() => themeToggleBtn.classList.remove('rotate'), 300);
            });
            
            // Handle navigation link clicks for smooth scrolling and animation
            navLinks.forEach(link => {
                link.addEventListener('click', (event) => {
                    const href = link.getAttribute('href');
                    
                    // Add animation class on click
                    link.classList.add('animate-pulse-once');
                    
                    // Remove animation class after it completes to allow it to be re-triggered
                    setTimeout(() => {
                        link.classList.remove('animate-pulse-once');
                    }, 300);

                    // Check if the link is an internal anchor link on the same page
                    if (href.startsWith('#')) {
                        event.preventDefault(); // Prevent default link behavior for anchor links
                        const targetElement = document.querySelector(href);
                        if (targetElement) {
                            targetElement.scrollIntoView({ behavior: 'smooth' });
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>