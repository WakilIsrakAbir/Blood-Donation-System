<?php
/**
 * Session Management & Access Control
 * Include this at the top of every protected page
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Get the base path of the project dynamically
 * @return string e.g. "/blood-donation-system/"
 */
function getProjectBasePath() {
    $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
    $basePath = str_replace('\\', '/', $scriptDir);
    // Remove /user, /admin subfolders to get project root
    $basePath = preg_replace('#/(user|admin)$#', '', $basePath);
    return rtrim($basePath, '/') . '/';
}

/**
 * Check if user is logged in
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get current user's role
 * @return string|null
 */
function getUserRole() {
    return $_SESSION['role'] ?? null;
}

/**
 * Get current user's ID
 * @return int|null
 */
function getUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user's name
 * @return string|null
 */
function getUserName() {
    return $_SESSION['user_name'] ?? null;
}

/**
 * Require login - redirect to login page if not authenticated
 */
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['flash_error'] = "Please login to access this page.";
        $base = getProjectBasePath();
        header("Location: " . $base . "login.php");
        exit();
    }
}

/**
 * Require specific role - redirect if user doesn't have the required role
 * @param string $role Required role ('user' or 'admin')
 */
function requireRole($role) {
    requireLogin();
    
    if (getUserRole() !== $role) {
        $base = getProjectBasePath();
        if ($role === 'admin') {
            $_SESSION['flash_error'] = "Access denied. Admin privileges required.";
            header("Location: " . $base . "login.php");
        } else {
            $_SESSION['flash_error'] = "Access denied.";
            header("Location: " . $base . "login.php");
        }
        exit();
    }
}

/**
 * Redirect if already logged in
 */
function redirectIfLoggedIn() {
    if (isLoggedIn()) {
        $base = getProjectBasePath();
        $role = getUserRole();
        if ($role === 'admin') {
            header("Location: " . $base . "admin/dashboard.php");
        } else {
            header("Location: " . $base . "user/dashboard.php");
        }
        exit();
    }
}

