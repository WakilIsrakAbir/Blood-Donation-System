<?php
/**
 * Admin Dashboard
 * Overview with stats widgets and recent activity
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('admin');

$pageTitle = 'Admin Dashboard';
$hideNavbar = true;
$currentPage = 'dashboard';

$db = getDB();

// Stats
$totalUsers = $db->query("SELECT COUNT(*) FROM users WHERE role='user'")->fetchColumn();
$totalPending = $db->query("SELECT COUNT(*) FROM blood_requests WHERE status='Pending'")->fetchColumn();
$totalApproved = $db->query("SELECT COUNT(*) FROM blood_requests WHERE status='Approved'")->fetchColumn();
$totalDonations = $db->query("SELECT COUNT(*) FROM donations WHERE status='Completed'")->fetchColumn();
$totalPledged = $db->query("SELECT COUNT(*) FROM donations WHERE status='Pledged'")->fetchColumn();
$unreadMessages = $db->query("SELECT COUNT(*) FROM contact_messages WHERE is_read=0")->fetchColumn();

// Recent pending requests
$recentPending = $db->query("SELECT br.*, u.name as requester_name 
                              FROM blood_requests br 
                              JOIN users u ON br.user_id = u.id 
                              WHERE br.status = 'Pending' 
                              ORDER BY br.created_at DESC LIMIT 5")->fetchAll();

// Recent pledges
$recentPledges = $db->query("SELECT d.*, u.name as donor_name, br.patient_name, br.required_blood_group
                              FROM donations d
                              JOIN users u ON d.donor_id = u.id
                              JOIN blood_requests br ON d.request_id = br.id
                              WHERE d.status = 'Pledged'
                              ORDER BY d.created_at DESC LIMIT 5")->fetchAll();

// Blood group distribution for chart
$bgDistribution = $db->query("SELECT blood_group, COUNT(*) as count FROM users WHERE role='user' GROUP BY blood_group ORDER BY blood_group")->fetchAll();

include __DIR__ . '/sidebar.php';
?>

<main class="dashboard-main">
    <div class="dash-header">
        <div>
            <h1>Admin Dashboard</h1>
            <p class="welcome-text">Welcome, <strong><?php echo e(getUserName()); ?></strong> — Here's your system overview.</p>
        </div>
    </div>

    <!-- Stats -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon blue">👥</div>
            <div class="stat-value"><?php echo $totalUsers; ?></div>
            <div class="stat-label">Total Users</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon yellow">⏳</div>
            <div class="stat-value"><?php echo $totalPending; ?></div>
            <div class="stat-label">Pending Requests</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green">✅</div>
            <div class="stat-value"><?php echo $totalApproved; ?></div>
            <div class="stat-label">Approved Requests</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon red">🩸</div>
            <div class="stat-value"><?php echo $totalDonations; ?></div>
            <div class="stat-label">Completed Donations</div>
        </div>
    </div>

    <!-- Additional Stats -->
    <div class="grid-3" style="margin-bottom: var(--space-2xl);">
        <div class="card" style="text-align: center;">
            <div style="font-size: 2rem; margin-bottom: var(--space-sm);">🤝</div>
            <div style="font-size: 1.5rem; font-weight: 800;"><?php echo $totalPledged; ?></div>
            <div style="font-size: 0.8rem; color: var(--text-muted);">Pending Pledges</div>
        </div>
        <div class="card" style="text-align: center;">
            <div style="font-size: 2rem; margin-bottom: var(--space-sm);">📬</div>
            <div style="font-size: 1.5rem; font-weight: 800;"><?php echo $unreadMessages; ?></div>
            <div style="font-size: 0.8rem; color: var(--text-muted);">Unread Messages</div>
        </div>
        <div class="card" style="text-align: center;">
            <div style="font-size: 2rem; margin-bottom: var(--space-sm);">❤️</div>
            <div style="font-size: 1.5rem; font-weight: 800;"><?php echo $totalDonations * 3; ?></div>
            <div style="font-size: 0.8rem; color: var(--text-muted);">Lives Saved</div>
        </div>
    </div>

    <!-- Blood Group Distribution -->
    <div class="card" style="margin-bottom: var(--space-2xl);">
        <div class="card-header">
            <h3 class="card-title">🩸 Donor Blood Group Distribution</h3>
        </div>
        <div style="display: flex; gap: var(--space-md); flex-wrap: wrap;">
            <?php foreach ($bgDistribution as $bg): ?>
            <div style="flex: 1; min-width: 100px; text-align: center; padding: var(--space-md); background: rgba(220, 20, 60, 0.06); border: 1px solid rgba(220, 20, 60, 0.15); border-radius: var(--radius-md);">
                <div style="font-family: var(--font-heading); font-size: 1.5rem; font-weight: 800; color: var(--primary-light);"><?php echo $bg['blood_group']; ?></div>
                <div style="font-size: 0.8rem; color: var(--text-muted);"><?php echo $bg['count']; ?> donor(s)</div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="grid-2">
        <!-- Recent Pending Requests -->
        <div class="table-card">
            <div class="table-card-header">
                <h3>⏳ Pending Requests</h3>
                <a href="manage_requests.php" class="btn btn-sm btn-secondary">View All</a>
            </div>
            <?php if (empty($recentPending)): ?>
                <div class="empty-state" style="padding: var(--space-xl);"><p style="color: var(--text-muted);">No pending requests ✨</p></div>
            <?php else: ?>
                <table class="table">
                    <thead><tr><th>Patient</th><th>Blood</th><th>Urgency</th></tr></thead>
                    <tbody>
                        <?php foreach ($recentPending as $req): ?>
                        <tr>
                            <td><strong><?php echo e($req['patient_name']); ?></strong><br><span style="font-size:0.75rem; color: var(--text-muted);">by <?php echo e($req['requester_name']); ?></span></td>
                            <td><span class="badge badge-blood"><?php echo e($req['required_blood_group']); ?></span></td>
                            <td><span class="badge <?php echo getUrgencyClass($req['urgency']); ?>"><?php echo e($req['urgency']); ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Recent Pledges -->
        <div class="table-card">
            <div class="table-card-header">
                <h3>🤝 Recent Pledges</h3>
                <a href="manage_donations.php" class="btn btn-sm btn-secondary">View All</a>
            </div>
            <?php if (empty($recentPledges)): ?>
                <div class="empty-state" style="padding: var(--space-xl);"><p style="color: var(--text-muted);">No recent pledges</p></div>
            <?php else: ?>
                <table class="table">
                    <thead><tr><th>Donor</th><th>For Patient</th><th>Blood</th></tr></thead>
                    <tbody>
                        <?php foreach ($recentPledges as $p): ?>
                        <tr>
                            <td><strong><?php echo e($p['donor_name']); ?></strong></td>
                            <td><?php echo e($p['patient_name']); ?></td>
                            <td><span class="badge badge-blood"><?php echo e($p['required_blood_group']); ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
