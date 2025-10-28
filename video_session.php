<?php
// This file handles only session management
error_reporting(0);
ini_set('display_errors', 0);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include config
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("location: login.php");
    exit;
}

// Get video details
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("location: videos.php?error=Video ID is required");
    exit;
}

 $video_id = $_GET['id'];
 $user_id = $_SESSION['user_id'];

// Get video details
 $sql = "SELECT * FROM videos WHERE id = ? AND user_id = ?";
 $stmt = $conn->prepare($sql);
 $stmt->bind_param("ii", $video_id, $user_id);
 $stmt->execute();
 $result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("location: videos.php?error=Video not found");
    exit;
}

 $video = $result->fetch_assoc();
 $stmt->close();
 $conn->close();

// Store video data in session
 $_SESSION['video_data'] = $video;

// Redirect to the actual video page
header("location: view_video.php");
exit;
?>