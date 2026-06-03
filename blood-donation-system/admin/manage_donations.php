<?php
/**
 * Manage Donations - Admin
 * Track pledges and mark donations as completed
 * Completing a donation auto-updates donor's last_donation_date
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('admin');

$pageTitle = 'Manage Donations';
$hideNavbar = true;
$currentPage = 'manage_donations';

$db = getDB();

// Handle complete action
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid form submission.');
        redirect('manage_donations.php');
    }

    $donationId = (int) ($_POST['donation_id'] ?? 0);

    // Mark donation as completed
    $stmt = $db->prepare("UPDATE donations SET status = 'Completed', donated_at = NOW() WHERE id = ?");
    $stmt->execute([$donationId]);

    // Get donor ID
    $donorStmt = $db->prepare("SELECT donor_id, request_id FROM donations WHERE id = ?");
    $donorStmt->execute([$donationId]);
    $donation = $donorStmt->fetch();

    if ($donation) {
        // Auto-update donor's last_donation_date
        $updateDonor = $db->prepare("UPDATE users SET last_donation_date = CURDATE() WHERE id = ?");
        $updateDonor->execute([$donation['donor_id']]);

        // Check if all units for this request are fulfilled
        // If so, mark the request as Fulfilled
        $requestStmt = $db->prepare("SELECT units_needed FROM blood_requests WHERE id = ?");
        $requestStmt->execute([$donation['request_id']]);
        $request = $requestStmt->fetch();

        $completedCount = $db->prepare("SELECT COUNT(*) FROM donations WHERE request_id = ? AND status = 'Completed'");
        $completedCount->execute([$donation['request_id']]);
        $completed = $completedCount->fetchColumn();

        if ($completed >= $request['units_needed']) {
            $db->prepare("UPDATE blood_requests SET status = 'Fulfilled' WHERE id = ?")->execute([$donation['request_id']]);
        }

        setFlash('success', 'Donation marked as completed. Donor\'s last donation date has been updated.');
    }

    redirect('manage_donations.php');
}

// Filter
$statusFilter = $_GET['status'] ?? 'Pledged';

$sql = "SELECT d.*, 
               u.name as donor_name, u.phone as donor_phone, u.blood_group as donor_blood, u.email as donor_email,
               br.patient_name, br.hospital_address, br.required_blood_group, br.date_needed, br.urgency
        FROM donations d
        JOIN users u ON d.donor_id = u.id
        JOIN blood_requests br ON d.request_id = br.id";
$params = [];

if ($statusFilter !== 'All') {
    $sql .= " WHERE d.status = ?";
    $params[] = $statusFilter;
}

$sql .= " ORDER BY d.created_at DESC";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$donations = $stmt->fetchAll();

include __DIR__ . '/sidebar.php';
?>

<main class="dashboard-main">
    <div class="dash-header">
        <div>
            <h1>🩸 Manage Donations</h1>
            <p class="welcome-text">Track donation pledges and mark them as completed</p>
        </div>
    </div>

    <div class="filter-bar">
        <a href="manage_donations.php?status=All" class="btn btn-sm <?php echo $statusFilter === 'All' ? 'btn-primary' : 'btn-secondary'; ?>">All</a>
        <a href="manage_donations.php?status=Pledged" class="btn btn-sm <?php echo $statusFilter === 'Pledged' ? 'btn-primary' : 'btn-secondary'; ?>">🤝 Pledged</a>
        <a href="manage_donations.php?status=Completed" class="btn btn-sm <?php echo $statusFilter === 'Completed' ? 'btn-primary' : 'btn-secondary'; ?>">✅ Completed</a>
    </div>

    <?php if (empty($donations)): ?>
        <div class="card">
            <div class="empty-state">
                <div class="empty-icon">🩸</div>
                <h3>No Donations</h3>
                <p>No donation records with status "<?php echo e($statusFilter); ?>".</p>
            </div>
        </div>
    <?php else: ?>
        <div class="table-card">
            <div class="table-card-header">
                <h3><?php echo count($donations); ?> donation(s)</h3>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Donor</th>
                        <th>Donor Blood</th>
                        <th>For Patient</th>
                        <th>Needed Blood</th>
                        <th>Hospital</th>
                        <th>Pledged On</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($donations as $d): ?>
                    <tr>
                        <td><?php echo $d['id']; ?></td>
                        <td>
                            <strong><?php echo e($d['donor_name']); ?></strong><br>
                            <span style="font-size: 0.75rem; color: var(--text-muted);">📞 <?php echo e($d['donor_phone']); ?></span>
                        </td>
                        <td><span class="badge badge-blood"><?php echo e($d['donor_blood']); ?></span></td>
                        <td><strong><?php echo e($d['patient_name']); ?></strong></td>
                        <td><span class="badge badge-blood"><?php echo e($d['required_blood_group']); ?></span></td>
                        <td style="max-width: 160px; font-size: 0.8rem;"><?php echo e($d['hospital_address']); ?></td>
                        <td style="font-size: 0.8rem;"><?php echo formatDate($d['created_at']); ?></td>
                        <td><span class="badge <?php echo getStatusClass($d['status']); ?>"><?php echo e($d['status']); ?></span></td>
                        <td>
                            <?php if ($d['status'] === 'Pledged'): ?>
                                <form method="POST" style="margin:0;">
                                    <?php echo csrfField(); ?>
                                    <input type="hidden" name="donation_id" value="<?php echo $d['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-success" data-confirm="Mark this donation as completed? This will update the donor's last donation date.">
                                        ✅ Complete
                                    </button>
                                </form>
                            <?php else: ?>
                                <span style="font-size: 0.8rem; color: var(--accent-green);">
                                    ✅ <?php echo formatDate($d['donated_at']); ?>
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
<button class="sidebar-toggle" onclick="document.querySelector('.sidebar').classList.toggle('active')">☰</button>
<?php include __DIR__ . '/../includes/footer.php'; ?>
