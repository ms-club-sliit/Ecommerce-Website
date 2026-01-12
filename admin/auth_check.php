<?php
/**
 * Admin Authentication Middleware
 * Include this file at the top of all admin pages to protect them
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in as admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    // Redirect to login page
    header('Location: ../login.php?error=admin_required');
    exit();
}

// Optional: Check if session has expired (30 minutes of inactivity)
$inactive_timeout = 1800; // 30 minutes in seconds
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $inactive_timeout)) {
    // Session expired
    session_unset();
    session_destroy();
    header('Location: ../login.php?error=session_expired');
    exit();
}

// Update last activity time
$_SESSION['last_activity'] = time();
?>
