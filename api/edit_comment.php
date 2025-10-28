<?php
// Start session
session_start();

// Include the database connection from the parent folder
require_once '../config.php';

// Check if user is logged in
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to edit a comment']);
    exit;
}

// Get POST data
 $data = json_decode(file_get_contents('php://input'), true);
 $commentId = $data['comment_id'] ?? null;
 $content = $data['content'] ?? null;

if (!$commentId || !$content) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

 $userId = $_SESSION['user_id'];

// Check if user owns the comment
 $checkSql = "SELECT user_id FROM video_comments WHERE id = ?";
 $stmt = $conn->prepare($checkSql);
 $stmt->bind_param("i", $commentId);
 $stmt->execute();
 $result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Comment not found']);
    exit;
}

 $comment = $result->fetch_assoc();

if ($comment['user_id'] != $userId) {
    echo json_encode(['success' => false, 'message' => 'You can only edit your own comments']);
    exit;
}

// Update comment
 $updateSql = "UPDATE video_comments SET content = ? WHERE id = ?";
 $stmt = $conn->prepare($updateSql);
 $stmt->bind_param("si", $content, $commentId);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Comment updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update comment']);
}

 $stmt->close();
 $conn->close();
?>