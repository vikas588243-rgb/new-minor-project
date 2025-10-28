<?php
// Start session at the beginning
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Daily Dose Podcast - Login</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts for 'Inter' -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc; /* Light background from your main page */
            color: #334155; /* Dark slate grey text */
        }
        /* Custom styles from your main page */
        .play-icon { width: 24px; height: 24px; fill: currentColor; }
        .custom-scrollbar::-webkit-scrollbar { height: 8px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #4f46e5; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background-color: #e0e7ff; border-radius: 4px; }
        @keyframes pulse-once { 0% { transform: scale(1); opacity: 1; } 50% { transform: scale(1.05); opacity: 0.7; } 100% { transform: scale(1); opacity: 1; } }
        .animate-pulse-once { animation: pulse-once 0.3s ease-in-out; }
    </style>
</head>
<body class="antialiased flex flex-col min-h-screen">

    <!-- Fixed container for the navbar -->
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
                <a href="contact.php" class="nav-link text-gray-600 hover:text-gray-900 font-medium transition duration-300">Contact</a>
            </nav>

            <!-- Action Buttons (Hidden on smaller screens) -->
            <div class="hidden md:flex space-x-4 ml-auto">
                <a href="login.php" class="nav-link bg-gray-200 text-gray-800 font-semibold px-4 py-2 rounded-full hover:bg-gray-300 transition duration-300">Log in</a>
                <a href="index.html" class="nav-link bg-indigo-600 text-white font-semibold px-4 py-2 rounded-full hover:bg-indigo-700 transition duration-300">Home</a>
            </div>
        </header>
    </div>

    <!-- Login Page Content - Always visible -->
    <main id="login-page" class="flex-grow flex items-center justify-center p-4 mt-32">
        <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-sm md:max-w-md">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-6">Tune In</h2>
            <p class="text-center text-gray-600 mb-8">Sign in to unlock your listening experience.</p>
            
            <?php
            // Display success message if redirected from signup
            if (isset($_GET['status']) && $_GET['status'] == 'success') {
                echo '<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded">
                    <p>Registration successful! Please log in.</p>
                </div>';
            }
            
            // Display error message if login failed
            if (isset($_GET['error'])) {
                echo '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded">
                    <p>' . htmlspecialchars($_GET['error']) . '</p>
                </div>';
            }
            ?>
            
            <form id="login-form" action="login_process.php" method="POST" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email address</label>
                    <input type="email" id="email" name="email" required class="mt-1 block w-full px-4 py-2 bg-gray-50 text-gray-900 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-300">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" id="password" name="password" required class="mt-1 block w-full px-4 py-2 bg-gray-50 text-gray-900 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-300">
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember_me" name="remember_me" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded-sm bg-white">
                        <label for="remember_me" class="ml-2 block text-sm text-gray-900">Remember me</label>
                    </div>
                    <a href="#" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">Forgot password?</a>
                </div>
                <div>
                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-300">
                        Log In
                    </button>
                </div>
            </form>
            
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Don't have an account? 
                    <a href="register.php" class="font-medium text-indigo-600 hover:text-indigo-500">Sign up</a>
                </p>
            </div>
        </div>
    </main>

    <!-- Footer from your main page -->
    <footer class="bg-gray-800 text-white py-8 px-4 mt-12 rounded-t-lg">
        <div class="container mx-auto text-center">
            <p class="mb-4">&copy; 2025 The Daily Dose Podcast. All rights reserved.</p>
            <div class="flex justify-center space-x-6">
                <a href="#" class="text-gray-400 hover:text-white transition duration-300">
                    Facebook
                </a>
                <a href="#" class="text-gray-400 hover:text-white transition duration-300">
                    Twitter
                </a>
                <a href="#" class="text-gray-400 hover:text-white transition duration-300">
                    Instagram
                </a>
                <a href="#" class="text-gray-400 hover:text-white transition duration-300">
                    LinkedIn
                </a>
            </div>
        </div>
    </footer>
</body>
</html>