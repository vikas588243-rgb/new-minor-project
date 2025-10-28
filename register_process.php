<?php
// Include the database connection file
require_once "config.php";

// Start session at the beginning
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Define variables and initialize with empty values
 $username = $email = $password = "";
 $signup_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $signup_err = "Invalid request. Please try again.";
    } else {
        // Check if username is empty
        if (empty(trim($_POST["username"]))) {
            $signup_err = "Please enter your username.";
        } else {
            $username = trim($_POST["username"]);
        }
        
        // Check if email is empty
        if (empty(trim($_POST["email"]))) {
            $signup_err = "Please enter your email.";
        } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
            $signup_err = "Please enter a valid email address.";
        } else {
            $email = trim($_POST["email"]);
        }
        
        // Check if password is empty
        if (empty(trim($_POST["password"]))) {
            $signup_err = "Please enter your password.";
        } elseif (strlen(trim($_POST["password"])) < 6) {
            $signup_err = "Password must be at least 6 characters.";
        } else {
            $password = trim($_POST["password"]);
        }
    }
    
    // Check for errors before inserting in database
    if (empty($signup_err)) {
        // Check if email already exists
        $sql = "SELECT id FROM users WHERE email = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $param_email);
            $param_email = $email;
            
            if ($stmt->execute()) {
                $stmt->store_result();
                
                if ($stmt->num_rows == 1) {
                    $signup_err = "This email is already registered.";
                } else {
                    // Prepare an insert statement
                    $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
                    
                    if ($stmt = $conn->prepare($sql)) {
                        $stmt->bind_param("sss", $param_username, $param_email, $param_password);
                        
                        // Set parameters
                        $param_username = $username;
                        $param_email = $email;
                        $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
                        
                        // Attempt to execute the prepared statement
                        if ($stmt->execute()) {
                            // IMPORTANT: This is the correct redirect
                            header("location: login.php?status=success");
                            exit();
                        } else {
                            $signup_err = "Oops! Something went wrong. Please try again later.";
                        }
                    }
                }
            } else {
                $signup_err = "Oops! Something went wrong. Please try again later.";
            }
            $stmt->close();
        }
    }
    
    // Close connection
    $conn->close();
    
    // If there's an error, redirect back to signup page with error message
    if (!empty($signup_err)) {
        // IMPORTANT: This redirect points to your form file
        header("location: register.php?error=" . urlencode($signup_err));
        exit();
    }
} else {
    // If not a POST request, redirect to signup page
    header("location: register.php");
    exit();
}
?>