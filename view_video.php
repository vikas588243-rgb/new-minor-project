<?php
// Set explicit session cookie parameters to ensure they work on localhost
session_set_cookie_params([
    'lifetime' => 86400, // Cookie lasts for 1 day
    'path' => '/',       // Available across the entire application
    'domain' => '',      // Use empty string for localhost
    'secure' => false,   // Must be false for non-HTTPS (http://localhost)
    'httponly' => true,  // Good security practice
    'samesite' => 'Lax'  // Modern browser requirement
]);
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Put this in view_video.php, immediately after session_start()

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
// 1. CORRECTED LOGIN CHECK: Use the universal session variable for login status
$isLoggedIn = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true; 

// Get video ID from URL parameter (e.g., view_video.php?id=123)
$videoId = $_GET['id'] ?? null;

// If no ID is provided, redirect to the videos list
if (!$videoId) {
    header("location: videos.php");
    exit;
}

// Include the database connection
require_once 'config.php';

// Initialize variables to prevent errors
$video = null;
$likeCount = 0;
$userHasLiked = false;
$comments = [];

try {
    // Get video details (Public viewing is correct)
    $sql = "SELECT * FROM videos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $videoId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // If no video found with that ID, redirect
    if ($result->num_rows === 0) {
        header("location: videos.php");
        exit;
    }
    
    $video = $result->fetch_assoc();
    
    // Get like count for this specific video
    $sql = "SELECT COUNT(*) as like_count FROM video_likes WHERE video_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $videoId);
    $stmt->execute();
    $likeCount = $stmt->get_result()->fetch_assoc()['like_count'];
    
    // Check if the CURRENTLY LOGGED IN user has liked this video
    if ($isLoggedIn) { 
        $user_id = $_SESSION['user_id'] ?? 0;
        $sql = "SELECT id FROM video_likes WHERE video_id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $videoId, $user_id);
        $stmt->execute();
        $userHasLiked = $stmt->get_result()->num_rows > 0;
    }
    
    // Get all comments for this specific video
    $sql = "
        SELECT c.id, c.content, c.created_at, u.username, u.id as user_id
        FROM video_comments c
        JOIN users u ON c.user_id = u.id
        WHERE c.video_id = ?
        ORDER BY c.created_at DESC
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $videoId);
    $stmt->execute();
    $comments_result = $stmt->get_result();
    $comments = $comments_result->fetch_all(MYSQLI_ASSOC);

} catch (Exception $e) {
    // Handle database error
    die("Database error: " . $e->getMessage());
}

// Define helper functions
function getCurrentUsername() {
    return $_SESSION['username'] ?? 'Guest';
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($video['title']); ?> - The Daily Dose Podcast</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #334155;
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
        
        body.dark .bg-gray-50 {
            background-color: #2d3748;
        }

        body.dark .border-gray-200 {
            border-color: #4a5568;
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

        /* Enhanced navbar styles for video player */
        .navbar-enhanced {
            background-color: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        body.dark .navbar-enhanced {
            background-color: rgba(26, 32, 44, 0.85);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Mobile menu animation */
        .mobile-menu {
            transform: translateX(100%);
            transition: transform 0.3s ease-in-out;
        }

        .mobile-menu.active {
            transform: translateX(0);
        }

        /* Video container responsive aspect ratio */
        .video-container {
            position: relative;
            padding-bottom: 56.25%; /* 16:9 aspect ratio */
            height: 0;
            overflow: hidden;
        }

        .video-container video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        /* Like button animation */
        .like-button {
            transition: all 0.2s ease;
        }

        .like-button.liked {
            color: #ef4444;
        }

        .like-button:hover {
            transform: scale(1.1);
        }

        /* Share dropdown */
        .share-dropdown {
            display: none;
            position: absolute;
            right: 0;
            top: 100%;
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            z-index: 10;
            padding: 0.5rem;
            min-width: 200px;
        }

        body.dark .share-dropdown {
            background-color: #2d3748;
        }

        .share-dropdown.active {
            display: block;
        }

        /* Comment styles */
        .comment {
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }

        body.dark .comment {
            border-color: #4a5568;
        }

        .comment:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        /* Notification toast */
        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #10b981;
            color: white;
            padding: 12px 24px;
            border-radius: 0.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            transform: translateY(100px);
            opacity: 0;
            transition: all 0.3s ease;
            z-index: 100;
        }

        .toast.show {
            transform: translateY(0);
            opacity: 1;
        }
    </style>
</head>
<body class="antialiased">
    <div class="fixed top-0 left-0 right-0 z-50">
        <header class="navbar-enhanced shadow-md rounded-full mx-auto max-w-7xl mt-4 px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between relative">
            <a href="index.php" class="flex items-center space-x-2 mr-auto">
                <div class="w-8 h-8 md:w-10 md:h-10 bg-indigo-600 rounded-full"></div>
                <span class="text-lg md:text-xl font-semibold text-gray-800">The Daily Dose</span>
            </a>

            <nav id="main-nav" class="hidden md:flex flex-grow justify-center items-center space-x-6 md:space-x-8">
                <a href="index.php" class="nav-link text-gray-600 hover:text-gray-900 font-medium transition duration-300">Home</a>
                <a href="index.php#episodes-section" class="nav-link text-gray-600 hover:text-gray-900 font-medium transition duration-300">Episodes</a>
                <a href="trending.php" class="nav-link text-gray-600 hover:text-gray-900 font-medium transition duration-300">Trending</a>
                <?php if ($isLoggedIn): ?>
                <a href="videos.php" class="nav-link text-gray-600 hover:text-gray-900 font-medium transition duration-300">My Videos</a>
                <?php endif; ?>
                <a href="index.php#about-section" class="nav-link text-gray-600 hover:text-gray-900 font-medium transition duration-300">About Us</a>
                <a href="contact.html" class="nav-link text-gray-600 hover:text-gray-900 font-medium transition duration-300">Contact</a>
            </nav>

            <div class="hidden md:flex space-x-4 ml-auto items-center">
                <button id="dark-mode-toggle" class="text-gray-700 hover:text-gray-900 transition duration-300">
                    <i class="fas fa-moon"></i>
                </button>
                <?php if ($isLoggedIn): ?>
                <span class="text-gray-700">Welcome, <?php echo htmlspecialchars(getCurrentUsername()); ?></span>
                <a href="logout.php" class="bg-indigo-600 text-white px-4 py-2 rounded-full hover:bg-indigo-700">Logout</a>
                <?php else: ?>
                <a href="login.php" class="bg-indigo-600 text-white px-4 py-2 rounded-full hover:bg-indigo-700">Login</a>
                <?php endif; ?>
            </div>

            <button id="mobile-menu-button" class="md:hidden text-gray-700 hover:text-gray-900 focus:outline-none">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </header>
    </div>

    <div id="mobile-menu" class="mobile-menu fixed top-0 right-0 h-full w-64 bg-white dark:bg-gray-800 shadow-lg z-40 md:hidden">
        <div class="p-4">
            <button id="close-mobile-menu" class="text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white focus:outline-none mb-6">
                <i class="fas fa-times text-xl"></i>
            </button>
            <nav class="flex flex-col space-y-4">
                <a href="index.php" class="text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white font-medium">Home</a>
                <a href="index.php#episodes-section" class="text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white font-medium">Episodes</a>
                <a href="trending.php" class="text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white font-medium">Trending</a>
                <?php if ($isLoggedIn): ?>
                <a href="videos.php" class="text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white font-medium">My Videos</a>
                <?php endif; ?>
                <a href="index.php#about-section" class="text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white font-medium">About Us</a>
                <a href="contact.html" class="text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white font-medium">Contact</a>
                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button id="mobile-dark-mode-toggle" class="text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white font-medium mb-4">
                        <i class="fas fa-moon mr-2"></i> Toggle Dark Mode
                    </button>
                    <?php if ($isLoggedIn): ?>
                    <span class="text-gray-700 dark:text-gray-300 block mb-4">Welcome, <?php echo htmlspecialchars(getCurrentUsername()); ?></span>
                    <a href="logout.php" class="bg-indigo-600 text-white px-4 py-2 rounded-full hover:bg-indigo-700 inline-block">Logout</a>
                    <?php else: ?>
                    <a href="login.php" class="bg-indigo-600 text-white px-4 py-2 rounded-full hover:bg-indigo-700 inline-block">Login</a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </div>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 mt-24">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="video-container bg-black">
                <video controls class="w-full h-full">
                    <source src="<?php echo htmlspecialchars($video['file_path']); ?>" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
            <div class="p-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-4"><?php echo htmlspecialchars($video['title']); ?></h1>
                <div class="flex items-center mb-6">
                    <span class="text-gray-500 mr-4">Size: <?php echo htmlspecialchars($video['file_size']); ?></span>
                    <span class="text-gray-500">Uploaded: <?php echo date('F j, Y', strtotime($video['upload_date'])); ?></span>
                </div>
                
                <div class="flex items-center space-x-4 mb-6">
                    <div class="flex items-center space-x-2">
                        <button id="like-button" class="like-button <?php echo $userHasLiked ? 'liked' : ''; ?>" data-video-id="<?php echo $video['id']; ?>">
                            <i class="fas fa-heart text-2xl"></i>
                        </button>
                        <span id="like-count" class="text-gray-700 font-medium"><?php echo $likeCount; ?></span>
                    </div>
                    
                    <div class="relative">
                        <button id="share-button" class="flex items-center space-x-2 text-gray-700 hover:text-gray-900">
                            <i class="fas fa-share-alt text-xl"></i>
                            <span>Share</span>
                        </button>
                        <div id="share-dropdown" class="share-dropdown">
                            <a href="#" class="share-option block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded" data-platform="facebook">
                                <i class="fab fa-facebook mr-2"></i> Facebook
                            </a>
                            <a href="#" class="share-option block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded" data-platform="twitter">
                                <i class="fab fa-twitter mr-2"></i> Twitter
                            </a>
                            <a href="#" class="share-option block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded" data-platform="linkedin">
                                <i class="fab fa-linkedin mr-2"></i> LinkedIn
                            </a>
                            <a href="#" class="share-option block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded" data-platform="whatsapp">
                                <i class="fab fa-whatsapp mr-2"></i> WhatsApp
                            </a>
                            <a href="#" class="share-option block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded" data-platform="copy">
                                <i class="fas fa-link mr-2"></i> Copy Link
                            </a>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-comment text-xl text-gray-700"></i>
                        <span id="comment-count" class="text-gray-700 font-medium"><?php echo count($comments); ?></span>
                    </div>
                </div>
                
                <div class="prose max-w-none">
                    <p><?php echo nl2br(htmlspecialchars($video['description'])); ?></p>
                </div>
                
                <div class="mt-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Comments</h2>
                    
                    <?php if ($isLoggedIn): ?>
                    <div class="mb-6">
                        <form id="comment-form" class="space-y-4">
                            <div>
                                <label for="comment-text" class="block text-sm font-medium text-gray-700">Add a comment</label>
                                <textarea id="comment-text" name="comment" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Share your thoughts..."></textarea>
                            </div>
                            <div>
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Post Comment
                                </button>
                            </div>
                        </form>
                    </div>
                    <?php else: ?>
                    <div class="mb-6 p-4 bg-gray-100 rounded-md">
                        <p class="text-gray-700">Please <a href="login.php" class="text-indigo-600 hover:text-indigo-800">log in</a> to post a comment.</p>
                    </div>
                    <?php endif; ?>
                    
                    <div id="comments-container">
                        <?php if (empty($comments)): ?>
                        <p class="text-gray-500">No comments yet. Be the first to comment!</p>
                        <?php else: ?>
                        <?php foreach ($comments as $comment): ?>
                        <div class="comment" data-comment-id="<?php echo $comment['id']; ?>">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-indigo-500 flex items-center justify-center text-white font-semibold">
                                        <?php echo strtoupper(substr($comment['username'], 0, 1)); ?>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center">
                                        <h3 class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($comment['username']); ?></h3>
                                        <span class="ml-2 text-xs text-gray-500"><?php echo date('M j, Y', strtotime($comment['created_at'])); ?></span>
                                    </div>
                                    <div class="mt-1 text-sm text-gray-700">
                                        <?php echo nl2br(htmlspecialchars($comment['content'])); ?>
                                    </div>
                                    <?php if ($isLoggedIn && $comment['user_id'] == ($_SESSION['user_id'] ?? null)): ?>
                                    <div class="mt-2 flex space-x-2">
                                        <button class="edit-comment text-xs text-indigo-600 hover:text-indigo-800" data-comment-id="<?php echo $comment['id']; ?>">Edit</button>
                                        <button class="delete-comment text-xs text-red-600 hover:text-red-800" data-comment-id="<?php echo $comment['id']; ?>">Delete</button>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="mt-8">
                    <a href="trending.php" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Back to Videos
                    </a>
                    <?php 
                    // CORRECT: Only show Delete if Logged In AND Session User ID matches Video Owner ID
                    if ($isLoggedIn && isset($_SESSION['user_id']) && $video['user_id'] == $_SESSION['user_id']): 
                    ?>
                    <form action="delete_video.php" method="post" class="inline-block ml-4" onsubmit="return confirm('Are you sure you want to delete this video?');">
                        <input type="hidden" name="video_id" value="<?php echo $video['id']; ?>">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Delete Video
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
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

    <div id="toast" class="toast"></div>

    <script>
        // Dark mode toggle
        const darkModeToggle = document.getElementById('dark-mode-toggle');
        const mobileDarkModeToggle = document.getElementById('mobile-dark-mode-toggle');
        const body = document.body;
        const darkModeIcon = darkModeToggle.querySelector('i');

        // Check for saved dark mode preference or default to light mode
        const currentTheme = localStorage.getItem('theme') || 'light';
        if (currentTheme === 'dark') {
            body.classList.add('dark');
            darkModeIcon.classList.replace('fa-moon', 'fa-sun');
        }

        function toggleDarkMode() {
            body.classList.toggle('dark');
            const isDarkMode = body.classList.contains('dark');
            
            // Update icon
            if (isDarkMode) {
                darkModeIcon.classList.replace('fa-moon', 'fa-sun');
            } else {
                darkModeIcon.classList.replace('fa-sun', 'fa-moon');
            }
            
            // Save preference to localStorage
            localStorage.setItem('theme', isDarkMode ? 'dark' : 'light');
        }

        darkModeToggle.addEventListener('click', toggleDarkMode);
        mobileDarkModeToggle.addEventListener('click', toggleDarkMode);

        // Mobile menu toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const closeMobileMenu = document.getElementById('close-mobile-menu');
        const mobileMenu = document.getElementById('mobile-menu');

        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.add('active');
        });

        closeMobileMenu.addEventListener('click', () => {
            mobileMenu.classList.remove('active');
        });

        // Close mobile menu when clicking on a link
        const mobileMenuLinks = mobileMenu.querySelectorAll('a');
        mobileMenuLinks.forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.remove('active');
            });
        });

        // Ensure navbar is always visible when scrolling
        let lastScrollTop = 0;
        const navbar = document.querySelector('.fixed.top-0');
        
        window.addEventListener('scroll', () => {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            // Always show navbar when at the top of the page
            if (scrollTop === 0) {
                navbar.style.transform = 'translateY(0)';
                return;
            }
            
            // Hide navbar when scrolling down, show when scrolling up
            if (scrollTop > lastScrollTop && scrollTop > 100) {
                navbar.style.transform = 'translateY(-100%)';
            } else {
                navbar.style.transform = 'translateY(0)';
            }
            
            lastScrollTop = scrollTop;
        });

        // Like button functionality
        const likeButton = document.getElementById('like-button');
        const likeCount = document.getElementById('like-count');
        const videoId = likeButton.getAttribute('data-video-id');
        let isLiked = likeButton.classList.contains('liked');

        likeButton.addEventListener('click', () => {
            // FIX: Use $isLoggedIn variable in JavaScript
            if (!<?php echo $isLoggedIn ? 'true' : 'false'; ?>) { 
                showToast('Please log in to like this video', 'error');
                return;
            }

            // Toggle like state
            isLiked = !isLiked;
            
            // Update UI
            if (isLiked) {
                likeButton.classList.add('liked');
                likeCount.textContent = parseInt(likeCount.textContent) + 1;
            } else {
                likeButton.classList.remove('liked');
                likeCount.textContent = parseInt(likeCount.textContent) - 1;
            }

            // Send request to server
            fetch('api/like_video.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    video_id: videoId,
                    action: isLiked ? 'like' : 'unlike'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    // Revert UI change if request failed
                    isLiked = !isLiked;
                    if (isLiked) {
                        likeButton.classList.add('liked');
                        likeCount.textContent = parseInt(likeCount.textContent) + 1;
                    } else {
                        likeButton.classList.remove('liked');
                        likeCount.textContent = parseInt(likeCount.textContent) - 1;
                    }
                    showToast(data.message || 'Something went wrong', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Revert UI change if request failed
                isLiked = !isLiked;
                if (isLiked) {
                    likeButton.classList.add('liked');
                    likeCount.textContent = parseInt(likeCount.textContent) + 1;
                } else {
                    likeButton.classList.remove('liked');
                    likeCount.textContent = parseInt(likeCount.textContent) - 1;
                }
                showToast('Something went wrong', 'error');
            });
        });

        // Share button functionality
        const shareButton = document.getElementById('share-button');
        const shareDropdown = document.getElementById('share-dropdown');
        const shareOptions = document.querySelectorAll('.share-option');

        shareButton.addEventListener('click', () => {
            shareDropdown.classList.toggle('active');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!shareButton.contains(e.target) && !shareDropdown.contains(e.target)) {
                shareDropdown.classList.remove('active');
            }
        });

        // Handle share options
        shareOptions.forEach(option => {
            option.addEventListener('click', (e) => {
                e.preventDefault();
                const platform = option.getAttribute('data-platform');
                const videoUrl = window.location.href;
                const videoTitle = document.querySelector('h1').textContent;
                
                let shareUrl = '';
                
                switch(platform) {
                    case 'facebook':
                        shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(videoUrl)}`;
                        break;
                    case 'twitter':
                        shareUrl = `https://twitter.com/intent/tweet?url=${encodeURIComponent(videoUrl)}&text=${encodeURIComponent(videoTitle)}`;
                        break;
                    case 'linkedin':
                        shareUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(videoUrl)}`;
                        break;
                    case 'whatsapp':
                        shareUrl = `https://wa.me/?text=${encodeURIComponent(videoTitle + ' ' + videoUrl)}`;
                        break;
                    case 'copy':
                        navigator.clipboard.writeText(videoUrl).then(() => {
                            showToast('Link copied to clipboard!', 'success');
                        }).catch(() => {
                            showToast('Failed to copy link', 'error');
                        });
                        return;
                }
                
                window.open(shareUrl, '_blank', 'width=600,height=400');
                shareDropdown.classList.remove('active');
            });
        });

        // Comment form functionality
        const commentForm = document.getElementById('comment-form');
        const commentText = document.getElementById('comment-text');
        const commentsContainer = document.getElementById('comments-container');
        const commentCount = document.getElementById('comment-count');

        if (commentForm) {
            commentForm.addEventListener('submit', (e) => {
                e.preventDefault();
                
                const comment = commentText.value.trim();
                if (!comment) {
                    showToast('Please enter a comment', 'error');
                    return;
                }

                // Send request to server
                fetch('api/add_comment.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        video_id: videoId,
                        comment: comment
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Clear form
                        commentText.value = '';
                        
                        // Add new comment to UI
                        const newComment = document.createElement('div');
                        newComment.className = 'comment';
                        newComment.setAttribute('data-comment-id', data.comment_id);
                        
                        const username = '<?php echo htmlspecialchars(getCurrentUsername()); ?>';
                        const firstLetter = username.charAt(0).toUpperCase();
                        
                        newComment.innerHTML = `
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-indigo-500 flex items-center justify-center text-white font-semibold">
                                        ${firstLetter}
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center">
                                        <h3 class="text-sm font-medium text-gray-900">${username}</h3>
                                        <span class="ml-2 text-xs text-gray-500">Just now</span>
                                    </div>
                                    <div class="mt-1 text-sm text-gray-700">
                                        ${comment.replace(/\n/g, '<br>')}
                                    </div>
                                    <div class="mt-2 flex space-x-2">
                                        <button class="edit-comment text-xs text-indigo-600 hover:text-indigo-800" data-comment-id="${data.comment_id}">Edit</button>
                                        <button class="delete-comment text-xs text-red-600 hover:text-red-800" data-comment-id="${data.comment_id}">Delete</button>
                                    </div>
                                </div>
                            </div>
                        `;
                        
                        // Add to the top of comments
                        if (commentsContainer.firstChild.textContent.includes('No comments yet')) {
                            commentsContainer.innerHTML = '';
                        }
                        commentsContainer.insertBefore(newComment, commentsContainer.firstChild);
                        
                        // Update comment count
                        commentCount.textContent = parseInt(commentCount.textContent) + 1;
                        
                        // Add event listeners to new buttons
                        addCommentEventListeners();
                        
                        showToast('Comment posted successfully!', 'success');
                    } else {
                        showToast(data.message || 'Failed to post comment', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Something went wrong', 'error');
                });
            });
        }

        // Delete comment functionality
        function addCommentEventListeners() {
            const deleteButtons = document.querySelectorAll('.delete-comment');
            const editButtons = document.querySelectorAll('.edit-comment');
            
            deleteButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const commentId = button.getAttribute('data-comment-id');
                    const commentElement = document.querySelector(`.comment[data-comment-id="${commentId}"]`);
                    
                    if (confirm('Are you sure you want to delete this comment?')) {
                        fetch('api/delete_comment.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                comment_id: commentId
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                commentElement.remove();
                                commentCount.textContent = parseInt(commentCount.textContent) - 1;
                                
                                // Show "No comments yet" if all comments are deleted
                                if (commentsContainer.children.length === 0) {
                                    commentsContainer.innerHTML = '<p class="text-gray-500">No comments yet. Be the first to comment!</p>';
                                }
                                
                                showToast('Comment deleted successfully', 'success');
                            } else {
                                showToast(data.message || 'Failed to delete comment', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showToast('Something went wrong', 'error');
                        });
                    }
                });
            });
            
            editButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const commentId = button.getAttribute('data-comment-id');
                    const commentElement = document.querySelector(`.comment[data-comment-id="${commentId}"]`);
                    const contentElement = commentElement.querySelector('.text-gray-700');
                    const currentContent = contentElement.textContent;
                    
                    // Create edit form
                    const editForm = document.createElement('form');
                    editForm.className = 'mt-1';
                    editForm.innerHTML = `
                        <textarea class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" rows="3">${currentContent}</textarea>
                        <div class="mt-2 flex space-x-2">
                            <button type="submit" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Save
                            </button>
                            <button type="button" class="cancel-edit inline-flex items-center px-3 py-1 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Cancel
                            </button>
                        </div>
                    `;
                    
                    // Replace content with edit form
                    contentElement.replaceWith(editForm);
                    
                    // Handle form submission
                    editForm.addEventListener('submit', (e) => {
                        e.preventDefault();
                        const newContent = editForm.querySelector('textarea').value.trim();
                        
                        if (!newContent) {
                            showToast('Comment cannot be empty', 'error');
                            return;
                        }
                        
                        fetch('api/edit_comment.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                comment_id: commentId,
                                content: newContent
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Replace form with updated content
                                const newContentElement = document.createElement('div');
                                newContentElement.className = 'mt-1 text-sm text-gray-700';
                                newContentElement.innerHTML = newContent.replace(/\n/g, '<br>');
                                editForm.replaceWith(newContentElement);
                                
                                showToast('Comment updated successfully', 'success');
                            } else {
                                showToast(data.message || 'Failed to update comment', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showToast('Something went wrong', 'error');
                        });
                    });
                    
                    // Handle cancel button
                    editForm.querySelector('.cancel-edit').addEventListener('click', () => {
                        const newContentElement = document.createElement('div');
                        newContentElement.className = 'mt-1 text-sm text-gray-700';
                        newContentElement.innerHTML = currentContent.replace(/\n/g, '<br>');
                        editForm.replaceWith(newContentElement);
                    });
                });
            });
        }
        
        // Initialize event listeners for existing comments
        addCommentEventListeners();

        // Toast notification function
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = 'toast show';
            
            if (type === 'error') {
                toast.style.backgroundColor = '#ef4444';
            } else {
                toast.style.backgroundColor = '#10b981';
            }
            
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }
    </script>
</body>
</html>