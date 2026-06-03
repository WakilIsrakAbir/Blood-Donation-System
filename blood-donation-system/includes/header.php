<?php
/**
 * Common Header
 * Role-aware navigation - shows different links based on auth state
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/functions.php';

// Determine the base path for assets and links
$basePath = '';
$currentDir = basename(dirname($_SERVER['SCRIPT_NAME']));
if ($currentDir === 'user' || $currentDir === 'admin') {
    $basePath = '../';
}

// Determine current page for active link highlighting
$currentPage = basename($_SERVER['SCRIPT_NAME'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Blood Donation System - Donate Blood, Save Life. Find blood donors near you and help save lives.">
    <title><?php echo isset($pageTitle) ? e($pageTitle) . ' | ' : ''; ?>BloodConnect - Donate Blood, Save Life</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/css/style.css">
    <?php if (isset($extraCSS)): ?>
        <?php foreach ($extraCSS as $css): ?>
            <link rel="stylesheet" href="<?php echo $basePath; ?>assets/css/<?php echo $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>

<?php if (!isset($hideNavbar) || !$hideNavbar): ?>
<!-- Navigation -->
<nav class="navbar" id="navbar">
    <div class="navbar-inner">
        <a href="<?php echo $basePath; ?>index.php" class="navbar-brand">
            <div class="brand-icon">🩸</div>
            <span>BloodConnect</span>
        </a>

        <ul class="navbar-links" id="navLinks">
            <li><a href="<?php echo $basePath; ?>index.php" class="<?php echo $currentPage === 'index' ? 'active' : ''; ?>">Home</a></li>
            <li><a href="<?php echo $basePath; ?>search_donors.php" class="<?php echo $currentPage === 'search_donors' ? 'active' : ''; ?>">Find Donors</a></li>
            <li><a href="<?php echo $basePath; ?>about.php" class="<?php echo $currentPage === 'about' ? 'active' : ''; ?>">About</a></li>
            <li><a href="<?php echo $basePath; ?>contact.php" class="<?php echo $currentPage === 'contact' ? 'active' : ''; ?>">Contact</a></li>
        </ul>

        <div class="navbar-actions">
            <?php if (isLoggedIn()): ?>
                <?php if (getUserRole() === 'admin'): ?>
                    <a href="<?php echo $basePath; ?>admin/dashboard.php" class="btn btn-sm btn-secondary">📊 Dashboard</a>
                <?php else: ?>
                    <a href="<?php echo $basePath; ?>user/dashboard.php" class="btn btn-sm btn-secondary">📊 Dashboard</a>
                <?php endif; ?>
                <a href="<?php echo $basePath; ?>logout.php" class="btn btn-sm btn-outline">Logout</a>
            <?php else: ?>
                <a href="<?php echo $basePath; ?>login.php" class="btn btn-sm btn-secondary">Login</a>
                <a href="<?php echo $basePath; ?>register.php" class="btn btn-sm btn-primary">Register</a>
            <?php endif; ?>
        </div>

        <div class="nav-toggle" id="navToggle">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
</nav>
<?php endif; ?>

<?php if (!isset($hideNavbar) || !$hideNavbar): ?>
<!-- Flash Messages -->
<div class="container" style="position: relative; z-index: 100; padding-top: 80px;">
    <?php echo displayFlashMessages(); ?>
</div>
<!-- Page Wrapper (closed by footer.php) -->
<div class="page-wrapper">
<?php endif; ?>

