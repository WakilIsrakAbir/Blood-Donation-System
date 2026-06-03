<?php
/**
 * User Sidebar Component
 * Included in all user panel pages
 */

$basePath = '../';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? e($pageTitle) . ' | ' : ''; ?>BloodConnect</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <?php if (isset($extraCSS)): ?>
        <?php foreach ($extraCSS as $css): ?>
            <link rel="stylesheet" href="../assets/css/<?php echo $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
<div class="dashboard-layout">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <a href="../index.php" class="sidebar-brand">
            <div class="brand-icon">🩸</div>
            <span>BloodConnect</span>
        </a>

        <div class="sidebar-section">Main Menu</div>
        <ul class="sidebar-nav">
            <li>
                <a href="dashboard.php" class="<?php echo ($currentPage ?? '') === 'dashboard' ? 'active' : ''; ?>">
                    <span class="nav-icon">📊</span> Dashboard
                </a>
            </li>
            <li>
                <a href="available_requests.php" class="<?php echo ($currentPage ?? '') === 'available_requests' ? 'active' : ''; ?>">
                    <span class="nav-icon">📢</span> Available Requests
                </a>
            </li>
            <li>
                <a href="request_blood.php" class="<?php echo ($currentPage ?? '') === 'request_blood' ? 'active' : ''; ?>">
                    <span class="nav-icon">🩸</span> Post Request
                </a>
            </li>
        </ul>

        <div class="sidebar-section">History</div>
        <ul class="sidebar-nav">
            <li>
                <a href="my_requests.php" class="<?php echo ($currentPage ?? '') === 'my_requests' ? 'active' : ''; ?>">
                    <span class="nav-icon">📋</span> My Requests
                </a>
            </li>
            <li>
                <a href="donation_history.php" class="<?php echo ($currentPage ?? '') === 'donation_history' ? 'active' : ''; ?>">
                    <span class="nav-icon">📜</span> Donation History
                </a>
            </li>
        </ul>

        <div class="sidebar-section">Account</div>
        <ul class="sidebar-nav">
            <li>
                <a href="profile.php" class="<?php echo ($currentPage ?? '') === 'profile' ? 'active' : ''; ?>">
                    <span class="nav-icon">👤</span> Profile
                </a>
            </li>
            <li>
                <a href="../logout.php">
                    <span class="nav-icon">🚪</span> Logout
                </a>
            </li>
        </ul>
    </aside>

    <!-- Flash messages inside dashboard -->
    <div style="position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 400px;">
        <?php echo displayFlashMessages(); ?>
    </div>
