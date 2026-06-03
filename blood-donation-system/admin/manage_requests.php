<?php
/**
 * Manage Blood Requests - Admin
 * Approve or reject pending blood requests
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('admin');

$pageTitle = 'Manage Requests';
$hideNavbar = true;
$currentPage = 'manage_requests';

$db = getDB();

// Handle approve/reject
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid form submission.');
        redirect('admin/manage_requests.php');
    }

    $action = $_POST['action'] ?? '';
    $requestId = (int) ($_POST['request_id'] ?? 0);

    if ($action === 'approve') {
        $stmt = $db->prepare("UPDATE blood_requests SET status = 'Approved' WHERE id = ?");
        $stmt->execute([$requestId]);
        setFlash('success', 'Request approved and now visible in user feed.');
    } elseif ($action === 'reject') {
        $stmt = $db->prepare("UPDATE blood_requests SET status = 'Rejected' WHERE id = ?");
        $stmt->execute([$requestId]);
        setFlash('success', 'Request rejected.');
    }

    redirect('admin/manage_requests.php');
}

// Filter
$statusFilter = $_GET['status'] ?? 'Pending';
$validStatuses = ['All', 'Pending', 'Approved', 'Rejected', 'Fulfilled'];

$sql = "SELECT br.*, u.name as requester_name, u.email as requester_email, u.phone as requester_phone
        FROM blood_requests br
        JOIN users u ON br.user_id = u.id";
$params = [];

if ($statusFilter !== 'All') {
    $sql .= " WHERE br.status = ?";
    $params[] = $statusFilter;
}

$sql .= " ORDER BY FIELD(br.urgency, 'Critical', 'Urgent', 'Normal'), br.created_at DESC";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$requests = $stmt->fetchAll();

include __DIR__ . '/sidebar.php';
?>

<main class="dashboard-main">
    <div class="dash-header">
        <div>
            <h1>📝 Manage Blood Requests</h1>
            <p class="welcome-text">Review and moderate blood requests</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-bar">
        <a href="manage_requests.php?status=All" class="btn btn-sm <?php echo $statusFilter === 'All' ? 'btn-primary' : 'btn-secondary'; ?>">All</a>
        <a href="manage_requests.php?status=Pending" class="btn btn-sm <?php echo $statusFilter === 'Pending' ? 'btn-primary' : 'btn-secondary'; ?>">⏳ Pending</a>
        <a href="manage_requests.php?status=Approved" class="btn btn-sm <?php echo $statusFilter === 'Approved' ? 'btn-primary' : 'btn-secondary'; ?>">✅ Approved</a>
        <a href="manage_requests.php?status=Rejected" class="btn btn-sm <?php echo $statusFilter === 'Rejected' ? 'btn-primary' : 'btn-secondary'; ?>">❌ Rejected</a>
        <a href="manage_requests.php?status=Fulfilled" class="btn btn-sm <?php echo $statusFilter === 'Fulfilled' ? 'btn-primary' : 'btn-secondary'; ?>">🎉 Fulfilled</a>
    </div>

    <?php if (empty($requests)): ?>
        <div class="card">
            <div class="empty-state">
                <div class="empty-icon">📝</div>
                <h3>No Requests</h3>
                <p>No blood requests with status "<?php echo e($statusFilter); ?>".</p>
            </div>
        </div>
    <?php else: ?>
        <div class="table-card">
            <div class="table-card-header">
                <h3><?php echo count($requests); ?> request(s)</h3>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Patient</th>
                        <th>Hospital</th>
                        <th>Blood</th>
                        <th>Units</th>
                        <th>Urgency</th>
                        <th>Date Needed</th>
                        <th>Requested By</th>
                        <th>Status</th>
                        <?php if ($statusFilter === 'Pending' || $statusFilter === 'All'): ?>
                        <th>Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $req): ?>
                    <tr>
                        <td><?php echo $req['id']; ?></td>
                        <td><strong><?php echo e($req['patient_name']); ?></strong></td>
                        <td style="max-width: 180px; font-size: 0.8rem;"><?php echo e($req['hospital_address']); ?></td>
                        <td><span class="badge badge-blood"><?php echo e($req['required_blood_group']); ?></span></td>
                        <td><?php echo $req['units_needed']; ?></td>
                        <td><span class="badge <?php echo getUrgencyClass($req['urgency']); ?>"><?php echo e($req['urgency']); ?></span></td>
                        <td><?php echo formatDate($req['date_needed']); ?></td>
                        <td>
                            <strong style="font-size: 0.85rem;"><?php echo e($req['requester_name']); ?></strong><br>
                            <span style="font-size: 0.75rem; color: var(--text-muted);">📞 <?php echo e($req['requester_phone']); ?></span>
                        </td>
                        <td><span class="badge <?php echo getStatusClass($req['status']); ?>"><?php echo e($req['status']); ?></span></td>
                        <?php if ($statusFilter === 'Pending' || $statusFilter === 'All'): ?>
                        <td>
                            <?php if ($req['status'] === 'Pending'): ?>
                            <div style="display: flex; gap: 4px;">
                                <form method="POST" style="margin:0;">
                                    <?php echo csrfField(); ?>
                                    <input type="hidden" name="action" value="approve">
                                    <input type="hidden" name="request_id" value="<?php echo $req['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-success" data-confirm="Approve this request?">✅</button>
                                </form>
                                <form method="POST" style="margin:0;">
                                    <?php echo csrfField(); ?>
                                    <input type="hidden" name="action" value="reject">
                                    <input type="hidden" name="request_id" value="<?php echo $req['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger" data-confirm="Reject this request?">❌</button>
                                </form>
                            </div>
                            <?php endif; ?>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>
