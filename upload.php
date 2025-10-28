<?php
// Include authentication functions
require_once 'auth.php';
require_once 'config.php';

// Require login to access this page
requireLogin();
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Video - The Daily Dose Podcast</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts for 'Inter' -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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
    </style>
</head>
<body class="antialiased">
    <!-- Fixed container for the navbar -->
    <div class="fixed top-0 left-0 right-0 z-50">
        <!-- The actual navbar, now centered within the fixed container -->
        <header class="bg-white/30 backdrop-blur-md shadow-md rounded-full mx-auto max-w-7xl mt-4 px-8 py-3 flex items-center justify-between relative">
            <!-- Logo/Site Title -->
            <a href="index.php" class="flex items-center space-x-2 mr-auto">
                <div class="w-8 h-8 md:w-10 md:h-10 bg-indigo-600 rounded-full"></div>
                <span class="text-lg md:text-xl font-semibold text-gray-800">The Daily Dose</span>
            </a>

            <!-- Navigation Links -->
            <nav id="main-nav" class="hidden md:flex flex-grow justify-center items-center space-x-6 md:space-x-8">
                <a href="index.php" class="nav-link text-gray-600 hover:text-gray-900 font-medium transition duration-300">Home</a>
                <a href="index.php#episodes-section" class="nav-link text-gray-600 hover:text-gray-900 font-medium transition duration-300">Episodes</a>
                <a href="trending.php" class="nav-link text-gray-600 hover:text-gray-900 font-medium transition duration-300">Trending</a>
                <a href="videos.php" class="nav-link text-gray-600 hover:text-gray-900 font-medium transition duration-300">My Videos</a>
                <a href="index.php#about-section" class="nav-link text-gray-600 hover:text-gray-900 font-medium transition duration-300">About Us</a>
                <a href="contact.php" class="nav-link text-gray-600 hover:text-gray-900 font-medium transition duration-300">Contact</a>
            </nav>

            <!-- Action Buttons -->
            <div class="hidden md:flex space-x-4 ml-auto items-center">
                <span class="text-gray-700">Welcome, <?php echo htmlspecialchars(getCurrentUsername()); ?></span>
                <a href="logout.php" class="bg-indigo-600 text-white px-4 py-2 rounded-full hover:bg-indigo-700">Logout</a>
            </div>
        </header>
    </div>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 mt-24">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Upload Video</h1>
        
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
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <form action="upload_process.php" method="POST" enctype="multipart/form-data" class="space-y-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Video Title</label>
                    <input type="text" id="title" name="title" required class="mt-1 block w-full px-4 py-2 bg-gray-50 text-gray-900 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="description" name="description" rows="4" class="mt-1 block w-full px-4 py-2 bg-gray-50 text-gray-900 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                </div>
                
                <div>
                    <label for="video" class="block text-sm font-medium text-gray-700 mb-1">Video File</label>
                    <input type="file" id="video" name="video" accept="video/*" required class="mt-1 block w-full px-4 py-2 bg-gray-50 text-gray-900 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <p class="mt-1 text-sm text-gray-500">Supported formats: MP4, AVI, MOV, WMV. Maximum file size: 100MB.</p>
                </div>
                
                <div>
                    <label for="thumbnail" class="block text-sm font-medium text-gray-700 mb-1">Thumbnail (Optional)</label>
                    <input type="file" id="thumbnail" name="thumbnail" accept="image/*" class="mt-1 block w-full px-4 py-2 bg-gray-50 text-gray-900 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <p class="mt-1 text-sm text-gray-500">Recommended size: 1280x720 pixels. Supported formats: JPG, PNG, GIF.</p>
                </div>
                
                <div class="flex justify-between">
                    <a href="videos.php" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Upload Video
                    </button>
                </div>
            </form>
        </div>
    </main>

    <!-- Footer -->
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
</body>
</html>