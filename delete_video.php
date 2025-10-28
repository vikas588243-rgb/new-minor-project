<?php
// Include authentication functions
require_once 'auth.php';
require_once 'config.php';

// Require login to access this page
requireLogin();

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Check if video_id is provided
    if (!isset($_POST["video_id"]) || empty($_POST["video_id"])) {
        header("location: videos.php?error=Video ID is required");
        exit;
    }
    
    $video_id = $_POST["video_id"];
    $user_id = getCurrentUserId();
    
    // Get video details before deleting
    $sql = "SELECT file_path, thumbnail_path FROM videos WHERE id = ? AND user_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ii", $video_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $video = $result->fetch_assoc();
            
            // Delete video file from server
            if (file_exists($video['file_path'])) {
                unlink($video['file_path']);
            }
            
            // Delete thumbnail file from server if exists
            if (!empty($video['thumbnail_path']) && file_exists($video['thumbnail_path'])) {
                unlink($video['thumbnail_path']);
            }
            
            // Delete video record from database
            $delete_sql = "DELETE FROM videos WHERE id = ? AND user_id = ?";
            if ($delete_stmt = $conn->prepare($delete_sql)) {
                $delete_stmt->bind_param("ii", $video_id, $user_id);
                
                if ($delete_stmt->execute()) {
                    header("location: videos.php?success=Video deleted successfully");
                    exit;
                } else {
                    header("location: videos.php?error=Failed to delete video");
                    exit;
                }
                $delete_stmt->close();
            }
        } else {
            header("location: videos.php?error=Video not found");
            exit;
        }
        $stmt->close();
    }
    
    // Close connection
    $conn->close();
} else {
    // If not a POST request, redirect to videos page
    header("location: videos.php");
    exit;
}
?>