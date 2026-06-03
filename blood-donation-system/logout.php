<?php
/**
 * Logout
 * Destroy session and redirect to login
 */

session_start();
$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

// Build base path dynamically
$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
$basePath = rtrim(str_replace('\\', '/', $scriptDir), '/') . '/';

header("Location: " . $basePath . "login.php");
exit();

