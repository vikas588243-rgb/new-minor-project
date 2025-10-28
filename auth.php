<?php
// Set explicit session cookie parameters to ensure they work on localhost
session_set_cookie_params([
    'lifetime' => 86400, // Cookie lasts for 1 day
    'path' => '/',       // Available across the entire application
    'domain' => '',      // Use empty string for localhost
    'secure' => false,   // Must be false for non-HTTPS (http://localhost)
    'httponly' => true,  // Good security practice
    'samesite' => 'Lax'  // Modern browser requirement
]);
// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in, if not then redirect to login page
function requireLogin() {
    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: login.php");
        exit;
    }
}

// Get current user ID
function getCurrentUserId() {
    return $_SESSION["user_id"] ?? null;
}

// Get current username
function getCurrentUsername() {
    return $_SESSION["username"] ?? null;
}

// Get current user email
function getCurrentUserEmail() {
    return $_SESSION["email"] ?? null;
}

// Get current user role
function getCurrentUserRole() {
    return $_SESSION["user_role"] ?? null;
}

// Check if user has a specific role
function hasRole($role) {
    return isset($_SESSION["user_role"]) && $_SESSION["user_role"] === $role;
}

// Get current user data
function getCurrentUser() {
    return [
        'id' => $_SESSION["user_id"] ?? null,
        'username' => $_SESSION["username"] ?? null,
        'email' => $_SESSION["email"] ?? null,
        'role' => $_SESSION["user_role"] ?? null
    ];
}
?>