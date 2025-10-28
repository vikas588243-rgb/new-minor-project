<?php
// Start session
session_start();

// Include the database connection from the parent folder
require_once '../config.php';

// Check if user is logged in
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to like a video']);
    exit;
}

// Get POST data
 $data = json_decode(file_get_contents('php://input'), true);
 $videoId = $data['video_id'] ?? null;
 $action = $data['action'] ?? null;

if (!$videoId || !$action) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

 $userId = $_SESSION['user_id'];

if ($action === 'like') {
    // Check if user has already liked this video
    $checkSql = "SELECT id FROM video_likes WHERE video_id = ? AND user_id = ?";
    $stmt = $conn->prepare($checkSql);
    $stmt->bind_param("ii", $videoId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        // Add like
        $insertSql = "INSERT INTO video_likes (video_id, user_id) VALUES (?, ?)";
        $stmt = $conn->prepare($insertSql);
        $stmt->bind_param("ii", $videoId, $userId);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Video liked successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to like video']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'You have already liked this video']);
    }
} else if ($action === 'unlike') {
    // Remove like
    $deleteSql = "DELETE FROM video_likes WHERE video_id = ? AND user_id = ?";
    $stmt = $conn->prepare($deleteSql);
    $stmt->bind_param("ii", $videoId, $userId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Video unliked successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to unlike video']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

 $stmt->close();
 $conn->close();
?>