<?php
/**
 * Admin Sidebar Component
 */

$basePath = '../';

// Count pending items for badges
$pendingRequests = $db->query("SELECT COUNT(*) FROM blood_requests WHERE status='Pending'")->fetchColumn();
$pendingDonations = $db->query("SELECT COUNT(*) FROM donations WHERE status='Pledged'")->fetchColumn();
$unreadMessages = $db->query("SELECT COUNT(*) FROM contact_messages WHERE is_read=0")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? e($pageTitle) . ' | ' : ''; ?>BloodConnect Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../assets/css/dashboard.css?v=<?php echo time(); ?>">
</head>
<body>
<div class="dashboard-layout">
    <aside class="sidebar" id="sidebar">
        <a href="../index.php" class="sidebar-brand">
            <div class="brand-icon">🩸</div>
            <span>BloodConnect</span>
        </a>

        <div class="sidebar-section">Admin Panel</div>
        <ul class="sidebar-nav">
            <li>
                <a href="dashboard.php" class="<?php echo ($currentPage ?? '') === 'dashboard' ? 'active' : ''; ?>">
                    <span class="nav-icon">📊</span> Dashboard
                </a>
            </li>
            <li>
                <a href="manage_users.php" class="<?php echo ($currentPage ?? '') === 'manage_users' ? 'active' : ''; ?>">
                    <span class="nav-icon">👥</span> Manage Users
                </a>
            </li>
            <li>
                <a href="manage_requests.php" class="<?php echo ($currentPage ?? '') === 'manage_requests' ? 'active' : ''; ?>">
                    <span class="nav-icon">📝</span> Blood Requests
                    <?php if ($pendingRequests > 0): ?>
                        <span class="notification-badge"><?php echo $pendingRequests; ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li>
                <a href="manage_donations.php" class="<?php echo ($currentPage ?? '') === 'manage_donations' ? 'active' : ''; ?>">
                    <span class="nav-icon">🩸</span> Donations
                    <?php if ($pendingDonations > 0): ?>
                        <span class="notification-badge"><?php echo $pendingDonations; ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li>
                <a href="contact_messages.php" class="<?php echo ($currentPage ?? '') === 'contact_messages' ? 'active' : ''; ?>">
                    <span class="nav-icon">📬</span> Messages
                    <?php if ($unreadMessages > 0): ?>
                        <span class="notification-badge"><?php echo $unreadMessages; ?></span>
                    <?php endif; ?>
                </a>
            </li>
        </ul>

        <div class="sidebar-section">Navigation</div>
        <ul class="sidebar-nav">
            <li><a href="../index.php"><span class="nav-icon">🏠</span> View Site</a></li>
            <li><a href="../logout.php"><span class="nav-icon">🚪</span> Logout</a></li>
        </ul>
    </aside>

    <div style="position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 400px;">
        <?php echo displayFlashMessages(); ?>
    </div>
