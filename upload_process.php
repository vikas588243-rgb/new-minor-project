<?php
// upload_process.php

// Include authentication and config
require_once 'auth.php';
require_once 'config.php';

// Require login to access this page
requireLogin();

// ----------------------------------------------------
// CRITICAL CONNECTION CHECK
// ----------------------------------------------------
// Check if the connection variable exists and is connected (from config.php)
if (!isset($conn) || $conn->connect_error) {
    header("location: upload.php?error=Database connection failed (check config).");
    exit();
}
// ----------------------------------------------------


// Define variables and initialize with empty values
$title = $description = "";
$upload_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Check if title is empty
    if (empty(trim($_POST["title"]))) {
        $upload_err = "Please enter a video title.";
    } else {
        $title = trim($_POST["title"]);
    }
    
    // Get description (optional)
    $description = trim($_POST["description"]);
    
    // Check if video file was uploaded
    if (empty($_FILES["video"]["tmp_name"])) {
        $upload_err = "Please select a video file to upload.";
    }
    
    // ----------------------------------------------------
    // FILE STORAGE IS BLOCKED ON FREE HOSTING
    // ----------------------------------------------------
    if (empty($upload_err)) {
        
        // **WARNING: The code below is a temporary simulation!**
        // It skips the move operation, as file saving is blocked on Render.
        
        // The file saving logic is intentionally bypassed/commented out:
        // if (move_uploaded_file($_FILES["video"]["tmp_name"], $targetFilePath)) {
        
        // Assume file saving FAILED but we insert metadata to confirm DB works:
        if (true) {
            
            // Generate metadata for database entry (Simulation)
            $targetFilePath = "S3_PATH_TO_BE_IMPLEMENTED/video/" . uniqid() . ".mp4";
            $fileSize = round($_FILES["video"]["size"] / 1000000, 2) . " MB"; // Still report real size
            $thumbnailPath = "S3_PATH_TO_BE_IMPLEMENTED/thumb/" . uniqid() . ".jpg";
            
            // Check for actual file system error, which is crucial for debugging local storage
            if ($_FILES["video"]["error"] != UPLOAD_ERR_OK) {
                // If the upload failed before move (e.g., file too large for PHP config), report that.
                $upload_err = "File upload failed before saving: Code " . $_FILES["video"]["error"];
            } 
            
            // ----------------------------------------------------
            // DATABASE INSERT
            // ----------------------------------------------------

            // Insert video information into database
            $user_id = getCurrentUserId();
            $sql = "INSERT INTO videos (user_id, title, description, file_path, thumbnail_path, file_size) VALUES (?, ?, ?, ?, ?, ?)";
            
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("isssss", $user_id, $title, $description, $targetFilePath, $thumbnailPath, $fileSize);
                
                if ($stmt->execute()) {
                    // Redirect to videos page with success message
                    header("location: videos.php?success=Video metadata saved. Note: File saving is blocked on this server.");
                    exit();
                } else {
                    $upload_err = "Database Insert Failed: " . $stmt->error; // Report specific DB error
                }
                $stmt->close();
            } else {
                 $upload_err = "SQL Prepare Failed: " . $conn->error; // Report specific SQL prepare error
            }

        } else {
            // This error block would catch file move failures if file saving was attempted
            $upload_err = "FATAL: File storage is blocked on this free hosting platform."; 
        }
    }
    
    // Close connection
    if (isset($conn)) {
        $conn->close();
    }
    
    // If there's an error, redirect back to upload page with error message
    if (!empty($upload_err)) {
        header("location: upload.php?error=" . urlencode($upload_err));
        exit();
    }
} else {
    // If not a POST request, redirect to upload page
    header("location: upload.php");
    exit();
}
?>