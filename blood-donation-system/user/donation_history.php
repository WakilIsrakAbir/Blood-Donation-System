<?php
/**
 * Donation History
 * Shows all donations (pledged & completed) by the current user
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('user');

$pageTitle = 'Donation History';
$hideNavbar = true;
$currentPage = 'donation_history';

$db = getDB();
$userId = getUserId();

$stmt = $db->prepare("SELECT d.*, br.patient_name, br.hospital_address, br.required_blood_group, br.date_needed
                       FROM donations d
                       JOIN blood_requests br ON d.request_id = br.id
                       WHERE d.donor_id = ?
                       ORDER BY d.created_at DESC");
$stmt->execute([$userId]);
$donations = $stmt->fetchAll();

include __DIR__ . '/sidebar.php';
?>

<main class="dashboard-main">
    <div class="dash-header">
        <div>
            <h1>📜 My Donation History</h1>
            <p class="welcome-text">Track all your blood donation records</p>
        </div>
    </div>

    <?php if (empty($donations)): ?>
        <div class="card">
            <div class="empty-state">
                <div class="empty-icon">📜</div>
                <h3>No Donations Yet</h3>
                <p>You haven't made any blood donations yet. Check the <a href="available_requests.php">available requests</a> to start helping!</p>
            </div>
        </div>
    <?php else: ?>
        <div class="table-card">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Patient Name</th>
                        <th>Hospital</th>
                        <th>Blood Group</th>
                        <th>Date Needed</th>
                        <th>Status</th>
                        <th>Pledged On</th>
                        <th>Completed On</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($donations as $i => $d): ?>
                    <tr>
                        <td><?php echo $i + 1; ?></td>
                        <td><strong><?php echo e($d['patient_name']); ?></strong></td>
                        <td style="max-width: 200px; font-size: 0.8rem;"><?php echo e($d['hospital_address']); ?></td>
                        <td><span class="badge badge-blood"><?php echo e($d['required_blood_group']); ?></span></td>
                        <td><?php echo formatDate($d['date_needed']); ?></td>
                        <td><span class="badge <?php echo getStatusClass($d['status']); ?>"><?php echo e($d['status']); ?></span></td>
                        <td style="font-size: 0.8rem;"><?php echo formatDate($d['created_at']); ?></td>
                        <td style="font-size: 0.8rem;"><?php echo $d['donated_at'] ? formatDate($d['donated_at']) : '-'; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="card mt-2" style="text-align: center;">
            <p style="font-size: 0.9rem; color: var(--text-muted); margin: 0;">
                🩸 You have completed <strong style="color: var(--accent-green);"><?php echo count(array_filter($donations, fn($d) => $d['status'] === 'Completed')); ?></strong> donation(s). Thank you for saving lives!
            </p>
        </div>
    <?php endif; ?>
<button class="sidebar-toggle" onclick="document.querySelector('.sidebar').classList.toggle('active')">☰</button>
<?php include __DIR__ . '/../includes/footer.php'; ?>
