<?php
// Include the database connection file
require_once "config.php";

// Start session at the beginning
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Define variables and initialize with empty values
 $email = $password = "";
 $login_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $login_err = "Invalid request. Please try again.";
    } else {
        // Check if email is empty
        if (empty(trim($_POST["email"]))) {
            $login_err = "Please enter your email.";
        } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
            $login_err = "Please enter a valid email address.";
        } else {
            $email = trim($_POST["email"]);
        }
        
        // Check if password is empty
        if (empty(trim($_POST["password"]))) {
            $login_err = "Please enter your password.";
        } else {
            $password = trim($_POST["password"]);
        }
    }
    
    // Validate credentials
    if (empty($login_err)) {
        // Prepare a select statement
        $sql = "SELECT id, username, email, password, role, is_active FROM users WHERE email = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $param_email);
            $param_email = $email;
            
            if ($stmt->execute()) {
                $stmt->store_result();
                
                // Check if email exists, if yes then verify password
                if ($stmt->num_rows == 1) {                    
                    $stmt->bind_result($id, $username, $email, $hashed_password, $role, $is_active);
                    if ($stmt->fetch()) {
                        // Check if account is active
                        if (!$is_active) {
                            $login_err = "Your account is not active. Please contact support.";
                        } elseif (password_verify($password, $hashed_password)) {
                            // Password is correct, start a new session
                            
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["user_id"] = $id;
                            $_SESSION["username"] = $username;
                            $_SESSION["email"] = $email;
                            $_SESSION["user_role"] = $role;
                            
                            // Regenerate session ID to prevent session fixation
                            session_regenerate_id();
                            
                            // Determine redirect URL
                            $redirect_url = isset($_SESSION['redirect_url']) ? $_SESSION['redirect_url'] : 'videos.php';
                            unset($_SESSION['redirect_url']); // Clear the redirect URL
                            
                            // Redirect user to the requested page or dashboard
                            header("location: " . $redirect_url);
                            exit();
                        } else {
                            // Password is not valid, display a generic error message
                            $login_err = "Invalid email or password.";
                        }
                    }
                } else {
                    // Email doesn't exist, display a generic error message
                    $login_err = "Invalid email or password.";
                }
            } else {
                $login_err = "Oops! Something went wrong. Please try again later.";
            }
            $stmt->close();
        }
    }
    
    // Close connection
    $conn->close();
    
    // If there's an error, redirect back to login page with error message
    if (!empty($login_err)) {
        // Get the referring page to redirect back to it
        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'login.php';
        header("location: " . $referer . "?error=" . urlencode($login_err));
        exit();
    }
} else {
    // If not a POST request, redirect to login page
    header("location: login.php");
    exit();
}
?>