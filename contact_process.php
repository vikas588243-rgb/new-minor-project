<?php
// Start session
session_start();

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get form data
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $message = trim($_POST["message"]);
    
    // Validate form data
    if (empty($name) || empty($email) || empty($message)) {
        header("location: contact.php?error=Please fill in all fields");
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("location: contact.php?error=Please enter a valid email address");
        exit;
    }
    
    // Here you would typically:
    // 1. Save the message to database
    // 2. Send an email notification
    // 3. Send a confirmation email to the user
    
    // For now, we'll just redirect with a success message
    header("location: contact.php?success=1");
    exit;
} else {
    // If not a POST request, redirect to contact page
    header("location: contact.php");
    exit;
}
?>