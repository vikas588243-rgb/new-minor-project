<?php
// Start session at the beginning
session_start();

// Include the database connection file
require_once "config.php";

// Check if database connection is successful
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check for login status (needed for navbar)
$isLoggedIn = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trending - The Daily Dose Podcast</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
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

        /* Custom styles */
        .play-icon { width: 24px; height: 24px; fill: currentColor; }
        .custom-scrollbar::-webkit-scrollbar { height: 8px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #4f46e5; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background-color: #e0e7ff; border-radius: 4px; }
        @keyframes pulse-once { 0% { transform: scale(1); opacity: 1; } 50% { transform: scale(1.05); opacity: 0.7; } 100% { transform: scale(1); opacity: 1; } }
        .animate-pulse-once { animation: pulse-once 0.3s ease-in-out; }
        .dark-mode-icon { transition: transform 0.3s ease-in-out; }
        .dark-mode-icon.rotate { transform: rotate(180deg); }
        .episode-card { transition: transform 0.3s ease; }
        .episode-card:hover { transform: translateY(-5px); }
        
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

    <section class="relative h-96 flex items-center justify-center overflow-hidden">
        <div class="absolute inset-0 z-0">
            <img src="https://picsum.photos/seed/trending/1920/600.jpg" alt="Trending background" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-black/50"></div>
        </div>
        <div class="relative z-10 text-center text-white px-4">
            <h1 class="text-4xl md:text-6xl font-bold mb-4">Trending Videos</h1>
            <p class="text-xl md:text-2xl">Discover what's popular right now</p>
        </div>
    </section>

    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Featured Videos</h2>
                <p class="text-xl text-gray-600">Hand-picked videos that are making waves</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php
                // Fetch featured videos from database with error handling
                $sql = "SELECT v.*, u.username FROM videos v JOIN users u ON v.user_id = u.id ORDER BY v.upload_date DESC LIMIT 9";
                $result = $conn->query($sql);
                
                if ($result && $result->num_rows > 0) {
                    while ($video = $result->fetch_assoc()) {
                        $thumbnailPath = !empty($video['thumbnail_path']) ? $video['thumbnail_path'] : 'https://picsum.photos/seed/' . $video['id'] . '/640/360.jpg';
                        ?>
                        <div class="episode-card bg-white rounded-lg shadow-lg overflow-hidden">
                            <a href="view_video.php?id=<?php echo $video['id']; ?>">
                                <div class="relative">
                                    <img src="<?php echo htmlspecialchars($thumbnailPath); ?>" alt="<?php echo htmlspecialchars($video['title']); ?>" class="w-full h-48 object-cover">
                                    <div class="absolute inset-0 bg-black/30 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity duration-300">
                                        <svg class="w-16 h-16 text-white play-icon" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M8 5v14l11-7z"/>
                                        </svg>
                                    </div>
                                </div>
                            </a>
                            <div class="p-6">
                                <h3 class="text-xl font-semibold text-gray-900 mb-2"><?php echo htmlspecialchars($video['title']); ?></h3>
                                <p class="text-gray-600 mb-4"><?php echo htmlspecialchars(substr($video['description'], 0, 100)) . '...'; ?></p>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">By <?php echo htmlspecialchars($video['username']); ?></span>
                                    <span class="text-sm text-gray-500"><?php echo date('M j, Y', strtotime($video['upload_date'])); ?></span>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo '<div class="col-span-full text-center py-12"><p class="text-gray-600">No videos uploaded yet.</p></div>';
                }
                ?>
            </div>
        </div>
    </section>

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