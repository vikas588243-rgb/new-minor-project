<?php
// Include authentication functions
require_once 'auth.php';
require_once 'config.php';

// Require login to access this page
requireLogin();

// Check if episode_id is provided
if (!isset($_POST['episode_id']) || empty($_POST['episode_id'])) {
    header("location: dashboard.php?error=Episode ID is required");
    exit;
}

 $episode_id = $_POST['episode_id'];
 $user_id = getCurrentUserId();

// Check if episode is already in favorites
 $check_sql = "SELECT * FROM user_favorites WHERE user_id = ? AND episode_id = ?";
 $check_stmt = $conn->prepare($check_sql);
 $check_stmt->bind_param("ii", $user_id, $episode_id);
 $check_stmt->execute();
 $result = $check_stmt->get_result();

if ($result->num_rows > 0) {
    header("location: dashboard.php?error=Episode is already in your favorites");
    exit;
}

// Add episode to favorites
 $insert_sql = "INSERT INTO user_favorites (user_id, episode_id) VALUES (?, ?)";
 $insert_stmt = $conn->prepare($insert_sql);
 $insert_stmt->bind_param("ii", $user_id, $episode_id);

if ($insert_stmt->execute()) {
    header("location: dashboard.php?success=Episode added to favorites");
} else {
    header("location: dashboard.php?error=Failed to add episode to favorites");
}

 $insert_stmt->close();
 $conn->close();
?>