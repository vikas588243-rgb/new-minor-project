<?php
// Start session
session_start();

// Include the database connection from the parent folder
require_once '../config.php';

// Check if user is logged in
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to comment']);
    exit;
}

// Get POST data
 $data = json_decode(file_get_contents('php://input'), true);
 $videoId = $data['video_id'] ?? null;
 $comment = $data['comment'] ?? null;

if (!$videoId || !$comment) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

 $userId = $_SESSION['user_id'];

// Add comment
 $sql = "INSERT INTO video_comments (video_id, user_id, content) VALUES (?, ?, ?)";
 $stmt = $conn->prepare($sql);
 $stmt->bind_param("iis", $videoId, $userId, $comment);

if ($stmt->execute()) {
    $commentId = $conn->insert_id;
    echo json_encode(['success' => true, 'message' => 'Comment added successfully', 'comment_id' => $commentId]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to post comment']);
}

 $stmt->close();
 $conn->close();
?>