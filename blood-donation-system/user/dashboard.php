<?php
/**
 * User Dashboard
 * Overview with stats and quick actions
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('user');

$pageTitle = 'Dashboard';
$extraCSS = ['dashboard.css'];
$hideNavbar = true;

$db = getDB();
$userId = getUserId();

// Fetch user data
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

// Stats
$totalDonations = $db->prepare("SELECT COUNT(*) FROM donations WHERE donor_id = ? AND status = 'Completed'");
$totalDonations->execute([$userId]);
$donationCount = $totalDonations->fetchColumn();

$activeRequests = $db->prepare("SELECT COUNT(*) FROM blood_requests WHERE user_id = ? AND status IN ('Pending', 'Approved')");
$activeRequests->execute([$userId]);
$requestCount = $activeRequests->fetchColumn();

$eligibility = checkDonationEligibility($user['last_donation_date']);

// Recent requests
$recentRequests = $db->prepare("SELECT * FROM blood_requests WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$recentRequests->execute([$userId]);
$myRecentRequests = $recentRequests->fetchAll();

$currentPage = 'dashboard';
include __DIR__ . '/sidebar.php';
?>

<main class="dashboard-main">
    <div class="dash-header">
        <div>
            <h1>Dashboard</h1>
            <p class="welcome-text">Welcome back, <strong><?php echo e($user['name']); ?></strong> 👋</p>
        </div>
        <a href="request_blood.php" class="btn btn-primary">🩸 Post Blood Request</a>
    </div>

    <!-- Stats -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon red">🩸</div>
            <div class="stat-value"><?php echo $donationCount; ?></div>
            <div class="stat-label">Total Donations</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon blue">📋</div>
            <div class="stat-value"><?php echo $requestCount; ?></div>
            <div class="stat-label">Active Requests</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon <?php echo $eligibility['eligible'] ? 'green' : 'yellow'; ?>">
                <?php echo $eligibility['eligible'] ? '✅' : '⏳'; ?>
            </div>
            <div class="stat-value"><?php echo $eligibility['eligible'] ? 'Eligible' : $eligibility['days_remaining'] . ' days'; ?></div>
            <div class="stat-label"><?php echo $eligibility['eligible'] ? 'Ready to Donate' : 'Until Next Donation'; ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon purple">📅</div>
            <div class="stat-value" style="font-size: 1.2rem;"><?php echo formatDate($user['last_donation_date']); ?></div>
            <div class="stat-label">Last Donation</div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <a href="available_requests.php" class="quick-action-btn"><span class="action-icon">📢</span> Available Requests</a>
        <a href="my_requests.php" class="quick-action-btn"><span class="action-icon">📋</span> My Requests</a>
        <a href="donation_history.php" class="quick-action-btn"><span class="action-icon">📜</span> Donation History</a>
        <a href="profile.php" class="quick-action-btn"><span class="action-icon">👤</span> Edit Profile</a>
    </div>

    <!-- Recent Requests Table -->
    <div class="table-card">
        <div class="table-card-header">
            <h3>📋 My Recent Requests</h3>
            <a href="my_requests.php" class="btn btn-sm btn-secondary">View All</a>
        </div>
        <?php if (empty($myRecentRequests)): ?>
            <div class="empty-state" style="padding: var(--space-2xl);">
                <div class="empty-icon">📋</div>
                <h3>No Requests Yet</h3>
                <p>You haven't posted any blood requests yet.</p>
            </div>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Blood Group</th>
                        <th>Urgency</th>
                        <th>Status</th>
                        <th>Date Needed</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($myRecentRequests as $req): ?>
                    <tr>
                        <td><strong><?php echo e($req['patient_name']); ?></strong></td>
                        <td><span class="badge badge-blood"><?php echo e($req['required_blood_group']); ?></span></td>
                        <td><span class="badge <?php echo getUrgencyClass($req['urgency']); ?>"><?php echo e($req['urgency']); ?></span></td>
                        <td><span class="badge <?php echo getStatusClass($req['status']); ?>"><?php echo e($req['status']); ?></span></td>
                        <td><?php echo formatDate($req['date_needed']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

<button class="sidebar-toggle" onclick="document.querySelector('.sidebar').classList.toggle('active')">☰</button>

<?php include __DIR__ . '/../includes/footer.php'; ?>

