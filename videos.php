<?php

// Start session at the beginning
session_start();

// Include authentication functions
require_once 'auth.php';
require_once 'config.php';

// Check if user is logged in
$isLoggedIn = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;

// If user is not logged in and tries to access this page, store the current URL for redirect after login
if (!$isLoggedIn && !isset($_GET['error'])) {
    $_SESSION['redirect_url'] = 'videos.php';
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Videos - The Daily Dose Podcast</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Remove default margins and control layout */
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow-x: hidden;
        }

        /* Use flexbox to control vertical space */
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Make main content fill available space */
        main {
            flex: 1;
            width: 100%;
            display: flex;
            flex-direction: column;
        }

        /* Ensure content sections fill space */
        .content-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        /* Keep footer at bottom */
        footer {
            flex-shrink: 0;
            width: 100%;
            margin-top: auto;
        }

        /* Prevent gaps between sections */
        section {
            margin: 0;
            padding: 0;
        }

        /* Ensure containers don't create gaps */
        .container, .max-w-7xl {
            width: 100%;
            max-width: 100%;
            padding-left: 1rem;
            padding-right: 1rem;
        }

        @media (min-width: 640px) {
            .container, .max-w-7xl {
                padding-left: 1.5rem;
                padding-right: 1.5rem;
            }
        }

        @media (min-width: 1024px) {
            .container, .max-w-7xl {
                padding-left: 2rem;
                padding-right: 2rem;
            }
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #334155;
            transition: background-color 0.5s ease, color 0.5s ease;
        }

        /* Dark mode styles */
        body.dark {
            background-color: #1a202c;
            color: #e2e8f0;
        }
        
        body.dark .bg-white\/30 {
            background-color: rgba(30, 41, 59, 0.3);
        }
        
        body.dark .shadow-md {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2), 0 2px 4px -1px rgba(0, 0, 0, 0.12);
        }
        
        body.dark .text-gray-800 {
            color: #e2e8f0;
        }
        
        body.dark .text-gray-600 {
            color: #a0aec0;
        }

        body.dark .hover\:text-gray-900:hover {
            color: #fff;
        }

        body.dark .bg-gray-200 {
            background-color: #4a5568;
            color: #e2e8f0;
        }
        
        body.dark .hover\:bg-gray-300:hover {
            background-color: #616e81;
        }
        
        body.dark .bg-indigo-600 {
            background-color: #667eea;
        }
        
        body.dark .hover\:bg-indigo-700:hover {
            background-color: #5a67d8;
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
            color: #a0aec0;
        }
        
        body.dark footer .text-gray-400:hover {
            color: #fff;
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
        .video-card {
            transition: transform 0.3s ease;
        }
        .video-card:hover {
            transform: translateY(-5px);
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

        /* Fix for mobile menu overflow */
        #mobile-menu {
            max-height: 80vh;
            overflow-y: auto;
        }

        /* Fix for video container */
        #videos-container {
            width: 100%;
            overflow: hidden;
        }

        /* Ensure no gaps in content */
        .no-gap {
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<body class="antialiased">
    <div class="fixed top-0 left-0 right-0 z-50">
        <header class="bg-white/30 backdrop-blur-md shadow-md rounded-full mx-auto max-w-7xl mt-4 px-8 py-3 flex items-center justify-between relative">
            <a href="index.php" class="flex items-center space-x-2 mr-auto">
                <div class="w-8 h-8 md:w-10 md:h-10 bg-indigo-600 rounded-full"></div>
                <span class="text-lg md:text-xl font-semibold text-gray-800">The Daily Dose</span>
            </a>

            <button id="mobile-menu-button" class="md:hidden p-2 rounded-md text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>

            <nav id="main-nav" class="hidden md:flex flex-grow justify-center items-center space-x-6 md:space-x-8">
                <a href="index.php" class="nav-link text-gray-600 hover:text-gray-900 font-medium transition duration-300">Home</a>
                <a href="index.php#episodes-section" class="nav-link text-gray-600 hover:text-gray-900 font-medium transition duration-300">Episodes</a>
                <a href="trending.php" class="nav-link text-gray-600 hover:text-gray-900 font-medium transition duration-300">Trending</a>
                <a href="videos.php" class="nav-link text-gray-600 hover:text-gray-900 font-medium transition duration-300">My Videos</a>
                <a href="index.php#about-section" class="nav-link text-gray-600 hover:text-gray-900 font-medium transition duration-300">About Us</a>
                <a href="contact.php" class="nav-link text-gray-600 hover:text-gray-900 font-medium transition duration-300">Contact</a>
            </nav>

            <div class="hidden md:flex space-x-4 ml-auto items-center">
                <button id="theme-toggle" class="p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors duration-300 ease-in-out">
                    <svg id="sun-icon" class="dark-mode-icon w-6 h-6 text-gray-700 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <svg id="moon-icon" class="dark-mode-icon w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                    </svg>
                </button>
                <?php if ($isLoggedIn): ?>
                    <span class="text-gray-700">Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?></span>
                    <a href="logout.php" class="bg-indigo-600 text-white px-4 py-2 rounded-full hover:bg-indigo-700">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="nav-link bg-gray-200 text-gray-800 font-semibold px-4 py-2 rounded-full hover:bg-gray-300 transition duration-300">Log In</a>
                    <a href="register.php" class="nav-link bg-indigo-600 text-white font-semibold px-4 py-2 rounded-full hover:bg-indigo-700 transition duration-300">Sign Up</a>
                <?php endif; ?>
            </div>
        </header>
    </div>

    <div id="mobile-menu" class="hidden md:hidden fixed top-24 left-4 right-4 bg-white/90 backdrop-blur-lg rounded-xl shadow-lg p-6 flex flex-col space-y-4 z-40">
        <nav class="flex flex-col space-y-2">
            <a href="index.php" class="nav-link text-gray-600 hover:text-indigo-700 font-medium transition duration-300">Home</a>
            <a href="index.php#episodes-section" class="nav-link text-gray-600 hover:text-indigo-700 font-medium transition duration-300">Episodes</a>
            <a href="trending.php" class="nav-link text-gray-600 hover:text-indigo-700 font-medium transition duration-300">Trending</a>
            <a href="videos.php" class="nav-link text-gray-600 hover:text-indigo-700 font-medium transition duration-300">My Videos</a>
            <a href="index.php#about-section" class="nav-link text-gray-600 hover:text-indigo-700 font-medium transition duration-300">About Us</a>
            <a href="contact.php" class="nav-link text-gray-600 hover:text-indigo-700 font-medium transition duration-300">Contact</a>
        </nav>
        <div class="flex flex-col space-y-3 mt-4 pt-4 border-t border-gray-200">
            <?php if ($isLoggedIn): ?>
                <span class="text-gray-700 text-center">Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?></span>
                <a href="logout.php" class="nav-link w-full text-center bg-indigo-600 text-white font-semibold px-4 py-2 rounded-full hover:bg-indigo-700 transition duration-300">Logout</a>
            <?php else: ?>
                <a href="login.php" class="nav-link w-full text-center bg-gray-200 text-gray-800 font-semibold px-4 py-2 rounded-full hover:bg-gray-300 transition duration-300">Log In</a>
                <a href="register.php" class="nav-link w-full text-center bg-indigo-600 text-white font-semibold px-4 py-2 rounded-full hover:bg-indigo-700 transition duration-300">Sign Up</a>
            <?php endif; ?>
        </div>
    </div>

    <main class="no-gap">
        <div class="content-wrapper">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 mt-24">

                <div class="flex justify-between items-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-900"><?php echo $isLoggedIn ? 'My Uploaded Videos' : 'Public Video Feed'; ?></h1>
                    <?php if ($isLoggedIn): ?>
                    <a href="upload.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Upload Video
                    </a>
                    <?php endif; ?>
                </div>
                
                <?php if (isset($_GET['success'])): ?>
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
                        <p><?php echo htmlspecialchars($_GET['success']); ?></p>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['error'])): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                        <p><?php echo htmlspecialchars($_GET['error']); ?></p>
                    </div>
                <?php endif; ?>
                
                <div id="videos-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php
                    // --- BEGIN NEW VIDEO FETCH LOGIC ---
                    
                    // Determine which videos to fetch
                    if ($isLoggedIn) {
                        // Logged in: Fetch ONLY the user's videos
                        $user_id = $_SESSION["user_id"];
                        $sql = "SELECT * FROM videos WHERE user_id = ? ORDER BY upload_date DESC";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $user_id);
                    } else {
                        // Not logged in: Fetch ALL videos (for public viewing)
                        $sql = "SELECT * FROM videos ORDER BY upload_date DESC";
                        $stmt = $conn->prepare($sql);
                    }
                    
                    if ($stmt->execute()) {
                        $result = $stmt->get_result();
                        
                        if ($result->num_rows > 0) {
                            while ($video = $result->fetch_assoc()) {
                                // Determine thumbnail path
                                $thumbnailPath = !empty($video['thumbnail_path']) ? $video['thumbnail_path'] : 'https://picsum.photos/seed/' . $video['id'] . '/640/360.jpg';
                                ?>
                                <div class="video-card bg-white rounded-lg shadow-md overflow-hidden">
                                    <a href="view_video.php?id=<?php echo $video['id']; ?>">
                                        <img src="<?php echo htmlspecialchars($thumbnailPath); ?>" alt="<?php echo htmlspecialchars($video['title']); ?>" class="w-full h-48 object-cover">
                                    </a>
                                    <div class="p-4">
                                        <h3 class="text-lg font-semibold text-gray-800 mb-2"><?php echo htmlspecialchars($video['title']); ?></h3>
                                        <p class="text-gray-600 mb-4"><?php echo htmlspecialchars(substr($video['description'], 0, 100)) . '...'; ?></p>
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-500"><?php echo htmlspecialchars($video['file_size']); ?></span>
                                            <div class="flex space-x-2">
                                                <a href="view_video.php?id=<?php echo $video['id']; ?>" class="text-indigo-600 hover:text-indigo-800 font-medium">View</a>
                                                <?php 
                                                // Only show Delete if logged in AND is the owner
                                                if ($isLoggedIn && isset($_SESSION["user_id"]) && $video['user_id'] == $_SESSION["user_id"]): 
                                                ?>
                                                <form action="delete_video.php" method="post" onsubmit="return confirm('Are you sure you want to delete this video?');">
                                                    <input type="hidden" name="video_id" value="<?php echo $video['id']; ?>">
                                                    <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Delete</button>
                                                </form>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            // No videos found message
                            $message = $isLoggedIn ? "No videos uploaded yet" : "There are currently no videos available in the public feed.";
                            $prompt = $isLoggedIn ? 'Upload your first video to get started.' : 'Check back later for new content.';
                            $link = $isLoggedIn ? '<a href="upload.php" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Upload Your First Video</a>' : '';
                            ?>
                            <div class="col-span-full text-center py-12">
                                <h2 class="text-2xl font-semibold text-gray-900 mb-2"><?php echo $message; ?></h2>
                                <p class="text-gray-600 mb-6"><?php echo $prompt; ?></p>
                                <?php echo $link; ?>
                            </div>
                            <?php
                        }
                        $stmt->close();
                    }
                    ?>
                </div>
                
                <?php if (!$isLoggedIn): ?>
                <div class="col-span-full text-center py-12 mt-12 border-t border-gray-200">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-indigo-100 rounded-full mb-4">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-semibold text-gray-900 mb-2">Want to manage your own videos?</h2>
                    <p class="text-gray-600 mb-6">Sign in to upload your own content and access personalized content.</p>
                    <a href="login.php" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Sign In
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer class="bg-gray-800 text-white py-8 px-4 mt-12 rounded-t-lg">
        <div class="container mx-auto text-center">
            <p class="mb-4">&copy; 2025 The Daily Dose Podcast. All rights reserved.</p>
            <div class="flex justify-center space-x-6">
                <a href="#" class="text-gray-400 hover:text-white transition duration-300">Facebook</a>
                <a href="#" class="text-gray-400 hover:text-white transition duration-300">Twitter</a>
                <a href="#" class="text-gray-400 hover:text-white transition duration-300">Instagram</a>
                <a href="#" class="text-gray-400 hover:text-white transition duration-300">LinkedIn</a>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
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