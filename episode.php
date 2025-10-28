<?php
// Start session at the very beginning
session_start(); 

// Include the database connection file
require_once "config.php";

// Check if database connection is successful
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if authentication functions file exists (needed for getCurrentUsername)
// Note: We REMOVE require_once 'auth.php'; and requireLogin()
// Instead, we check the session directly.
$isLoggedIn = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;

// Define getCurrentUsername helper function since we removed auth.php
function getCurrentUsername() {
    return $_SESSION['username'] ?? 'Guest';
}

// Check if episode_id is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Redirect to index.php for public-facing access
    header("location: index.php?error=Episode ID is required"); 
    exit;
}

$episode_id = $_GET['id'];

// Get episode details (No user check needed here; view is public)
$sql = "SELECT * FROM episodes WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $episode_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Redirect to index.php for public-facing access
    header("location: index.php?error=Episode not found"); 
    exit;
}

$episode = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($episode['title']); ?> - The Daily Dose Podcast</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; color: #334155; }
    </style>
</head>
<body class="antialiased">
    <nav class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="index.php" class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-indigo-600 rounded-full"></div>
                        <span class="text-lg font-semibold text-gray-800">The Daily Dose</span>
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <?php if ($isLoggedIn): ?>
                        <span class="text-gray-700">Welcome, <?php echo htmlspecialchars(getCurrentUsername()); ?></span>
                        <a href="logout.php" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="text-gray-700 hover:text-indigo-600">Log In</a>
                        <a href="register.php" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Sign Up</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <img src="<?php echo htmlspecialchars($episode['poster_url']); ?>" alt="<?php echo htmlspecialchars($episode['title']); ?>" class="w-full h-64 object-cover">
            <div class="p-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-4"><?php echo htmlspecialchars($episode['title']); ?></h1>
                <div class="flex items-center mb-6">
                    <span class="text-gray-500 mr-4">Duration: <?php echo htmlspecialchars($episode['duration']); ?></span>
                    <span class="text-gray-500">Published: <?php echo date('F j, Y', strtotime($episode['created_at'])); ?></span>
                </div>
                <div class="prose max-w-none">
                    <p><?php echo nl2br(htmlspecialchars($episode['description'])); ?></p>
                </div>
                <div class="mt-8">
                    <?php if ($isLoggedIn): ?>
                        <form action="add_favorite.php" method="post" class="inline-block">
                            <input type="hidden" name="episode_id" value="<?php echo $episode['id']; ?>">
                            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Add to Favorites</button>
                        </form>
                        <a href="dashboard.php" class="ml-4 text-gray-600 hover:text-gray-800">Back to Dashboard</a>
                    <?php else: ?>
                        <div class="p-3 bg-gray-100 rounded-md inline-block">
                            <p class="text-gray-700 text-sm">
                                <a href="login.php" class="text-indigo-600 hover:text-indigo-800 font-medium">Log in</a> to save this episode to your favorites.
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-gray-800 text-white py-8 px-4 mt-12 rounded-t-lg">
        <div class="container mx-auto text-center">
            <p class="mb-4">&copy; 2025 The Daily Dose Podcast. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>