<?php
// Include authentication functions
require_once 'auth.php';
require_once 'config.php';

// Require admin role to access this page
requireAdmin();

// Get episode ID from URL
 $episode_id = $_GET['id'] ?? '';

// Validate episode ID
if (empty($episode_id) || !is_numeric($episode_id)) {
    header("location: admin_dashboard.php");
    exit;
}

// Delete episode
 $sql = "DELETE FROM episodes WHERE id = ?";
 $stmt = $conn->prepare($sql);
 $stmt->bind_param("i", $episode_id);

if ($stmt->execute()) {
    header("location: admin_dashboard.php?message=Episode deleted successfully");
} else {
    header("location: admin_dashboard.php?error=Error deleting episode");
}
?>