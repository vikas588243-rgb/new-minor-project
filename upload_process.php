<?php
// Include authentication functions
require_once 'auth.php';
require_once 'config.php';

// Require login to access this page
requireLogin();
// Check if the connection exists (assuming $conn is defined in config.php)
if (!isset($conn) || $conn->connect_error) {
    header("location: upload.php?error=Database connection failed before upload.");
    exit();
}

// ... rest of your code ...
if (move_uploaded_file($_FILES["video"]["tmp_name"], $targetFilePath)) {}

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
    
    // If no errors, proceed with upload
    if (empty($upload_err)) {
        // Create uploads directory if it doesn't exist
        $targetDir = "uploads/videos/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        
        // Create thumbnails directory if it doesn't exist
        $thumbnailDir = "uploads/thumbnails/";
        if (!file_exists($thumbnailDir)) {
            mkdir($thumbnailDir, 0777, true);
        }
        
        // Generate unique filename for video
        $videoFileType = strtolower(pathinfo($_FILES["video"]["name"], PATHINFO_EXTENSION));
        $videoFileName = uniqid() . "." . $videoFileType;
        $targetFilePath = $targetDir . $videoFileName;
        
        // Check file size (100MB limit)
        if ($_FILES["video"]["size"] > 100000000) {
            $upload_err = "Video file is too large. Maximum size is 100MB.";
        }
        
        // Allow certain file formats
        $allowedTypes = array("mp4", "avi", "mov", "wmv");
        if (!in_array($videoFileType, $allowedTypes)) {
            $upload_err = "Sorry, only MP4, AVI, MOV & WMV files are allowed.";
        }
        
        // If still no errors, try to upload file
        if (empty($upload_err)) {
            if (move_uploaded_file($_FILES["video"]["tmp_name"], $targetFilePath)) {
                // Get file size in a readable format
                $fileSize = $_FILES["video"]["size"];
                if ($fileSize >= 1000000) {
                    $fileSize = round($fileSize / 1000000, 2) . " MB";
                } elseif ($fileSize >= 1000) {
                    $fileSize = round($fileSize / 1000, 2) . " KB";
                } else {
                    $fileSize = $fileSize . " bytes";
                }
                
                // Handle thumbnail upload if provided
                $thumbnailPath = "";
                if (!empty($_FILES["thumbnail"]["tmp_name"])) {
                    $thumbnailFileType = strtolower(pathinfo($_FILES["thumbnail"]["name"], PATHINFO_EXTENSION));
                    $thumbnailFileName = uniqid() . "." . $thumbnailFileType;
                    $thumbnailFilePath = $thumbnailDir . $thumbnailFileName;
                    
                    // Check if thumbnail file is a valid image
                    $allowedImageTypes = array("jpg", "jpeg", "png", "gif");
                    if (in_array($thumbnailFileType, $allowedImageTypes)) {
                        if (move_uploaded_file($_FILES["thumbnail"]["tmp_name"], $thumbnailFilePath)) {
                            $thumbnailPath = $thumbnailFilePath;
                        }
                    }
                }
                
                // Insert video information into database
                $user_id = getCurrentUserId();
                $sql = "INSERT INTO videos (user_id, title, description, file_path, thumbnail_path, file_size) VALUES (?, ?, ?, ?, ?, ?)";
                
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param("isssss", $user_id, $title, $description, $targetFilePath, $thumbnailPath, $fileSize);
                    
                    if ($stmt->execute()) {
                        // Redirect to videos page with success message
                        header("location: videos.php?success=Video uploaded successfully");
                        exit();
                    } else {
                        $upload_err = "Oops! Something went wrong. Please try again later.";
                    }
                    $stmt->close();
                }
            } else {
                $upload_err = "Sorry, there was an error uploading your file.";
            }
        }
    }
    
    // Close connection
    $conn->close();
    
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