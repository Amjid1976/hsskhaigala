<?php
session_start();

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['username']) && isset($_SESSION['role']);
}

// Function to check if user has specific role
function hasRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

// Function to enforce role-based access
function requireRole($role) {
    if (!isLoggedIn()) {
        header("Location: index.php");
        exit();
    }
    
    if (!hasRole($role)) {
        header("Location: unauthorized.php");
        exit();
    }
}

// Function to check session timeout (30 minutes)
function checkSessionTimeout() {
    $timeout = 1800; // 30 minutes in seconds
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
        session_unset();
        session_destroy();
        header("Location: index.php?msg=timeout");
        exit();
    }
    $_SESSION['last_activity'] = time();
}
?>
