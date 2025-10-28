<?php
// Include the database connection file
require_once "config.php";

// Check if video_id is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("location: index.php?error=Video ID is required");
    exit;
}

 $video_id = $_GET['id'];

// Get video details
 $sql = "SELECT v.*, u.username FROM videos v JOIN users u ON v.user_id = u.id WHERE v.id = ?";
 $stmt = $conn->prepare($sql);
 $stmt->bind_param("i", $video_id);
 $stmt->execute();
 $result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("location: index.php?error=Video not found");
    exit;
}

 $video = $result->fetch_assoc();
 $stmt->close();
 $conn->close();
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($video['title']); ?> - The Daily Dose Podcast</title>
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
                <?php
                session_start();
                if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true):
                ?>
                    <span class="text-gray-700">Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?></span>
                    <a href="logout.php" class="bg-indigo-600 text-white px-4 py-2 rounded-full hover:bg-indigo-700">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="nav-link bg-gray-200 text-gray-800 font-semibold px-4 py-2 rounded-full hover:bg-gray-300 transition duration-300">Log In</a>
                    <a href="register.php" class="nav-link bg-indigo-600 text-white font-semibold px-4 py-2 rounded-full hover:bg-indigo-700 transition duration-300">Sign Up</a>
                <?php endif; ?>
            </div>
        </header>
    </div>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 mt-24">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="aspect-w-16 aspect-h-9 bg-black">
                <video controls class="w-full h-auto max-h-[600px]">
                    <source src="<?php echo htmlspecialchars($video['file_path']); ?>" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
            <div class="p-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-4"><?php echo htmlspecialchars($video['title']); ?></h1>
                <div class="flex items-center mb-6">
                    <span class="text-gray-500 mr-4">By <?php echo htmlspecialchars($video['username']); ?></span>
                    <span class="text-gray-500 mr-4">Size: <?php echo htmlspecialchars($video['file_size']); ?></span>
                    <span class="text-gray-500">Uploaded: <?php echo date('F j, Y', strtotime($video['upload_date'])); ?></span>
                </div>
                <div class="prose max-w-none">
                    <p><?php echo nl2br(htmlspecialchars($video['description'])); ?></p>
                </div>
                <div class="mt-8">
                    <a href="javascript:history.back()" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Back
                    </a>
                    <?php
                    // Show edit/delete options only if the user owns the video
                    if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && $_SESSION["user_id"] == $video['user_id']):
                    ?>
                        <a href="view_video.php?id=<?php echo $video['id']; ?>" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 ml-4">
                            Manage Video
                        </a>
                    <?php endif; ?>
                </div>
            </div>
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