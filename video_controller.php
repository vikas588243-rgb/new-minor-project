<?php
// This file handles all PHP logic and session management
// No HTML output here whatsoever

// Start session at the very beginning
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Disable all error output
error_reporting(0);
ini_set('display_errors', 0);

// Include required files - but we'll handle auth differently
require_once 'config.php';

// Check if video_id is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "Video ID is required";
    header("location: videos.php");
    exit;
}

 $video_id = $_GET['id'];

// Check if user is logged in
 $isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);

// Get video details - different query based on login status
if ($isLoggedIn) {
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT * FROM videos WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $video_id, $user_id);
} else {
    // For non-logged-in users, get any video (public videos)
    $sql = "SELECT * FROM videos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $video_id);
}

 $stmt->execute();
 $result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = "Video not found";
    header("location: videos.php");
    exit;
}

 $video = $result->fetch_assoc();
 $stmt->close();
 $conn->close();

// Store video data in session
 $_SESSION['current_video'] = $video;
 $_SESSION['is_logged_in'] = $isLoggedIn;

// Redirect to the display page
header("location: view_video.php");
exit;
?>