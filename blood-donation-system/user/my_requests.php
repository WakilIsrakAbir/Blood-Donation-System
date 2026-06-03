<?php
/**
 * My Requests History
 * Shows all requests posted by the current user with status tracking
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('user');

$pageTitle = 'My Requests';
$hideNavbar = true;
$currentPage = 'my_requests';

$db = getDB();
$userId = getUserId();

// Filter
$statusFilter = $_GET['status'] ?? '';

$sql = "SELECT * FROM blood_requests WHERE user_id = ?";
$params = [$userId];

if ($statusFilter && in_array($statusFilter, ['Pending', 'Approved', 'Rejected', 'Fulfilled'])) {
    $sql .= " AND status = ?";
    $params[] = $statusFilter;
}

$sql .= " ORDER BY created_at DESC";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$requests = $stmt->fetchAll();

include __DIR__ . '/sidebar.php';
?>

<main class="dashboard-main">
    <div class="dash-header">
        <div>
            <h1>📋 My Blood Requests</h1>
            <p class="welcome-text">Track the status of all your blood requests</p>
        </div>
        <a href="request_blood.php" class="btn btn-primary">🩸 New Request</a>
    </div>

    <!-- Filter -->
    <div class="filter-bar">
        <a href="my_requests.php" class="btn btn-sm <?php echo !$statusFilter ? 'btn-primary' : 'btn-secondary'; ?>">All</a>
        <a href="my_requests.php?status=Pending" class="btn btn-sm <?php echo $statusFilter === 'Pending' ? 'btn-primary' : 'btn-secondary'; ?>">🟡 Pending</a>
        <a href="my_requests.php?status=Approved" class="btn btn-sm <?php echo $statusFilter === 'Approved' ? 'btn-primary' : 'btn-secondary'; ?>">🟢 Approved</a>
        <a href="my_requests.php?status=Rejected" class="btn btn-sm <?php echo $statusFilter === 'Rejected' ? 'btn-primary' : 'btn-secondary'; ?>">🔴 Rejected</a>
        <a href="my_requests.php?status=Fulfilled" class="btn btn-sm <?php echo $statusFilter === 'Fulfilled' ? 'btn-primary' : 'btn-secondary'; ?>">✅ Fulfilled</a>
    </div>

    <?php if (empty($requests)): ?>
        <div class="card">
            <div class="empty-state">
                <div class="empty-icon">📋</div>
                <h3>No Requests Found</h3>
                <p><?php echo $statusFilter ? 'No requests with status "' . e($statusFilter) . '".' : 'You haven\'t posted any blood requests yet.'; ?></p>
                <a href="request_blood.php" class="btn btn-primary mt-1">Post a Request</a>
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
                        <th>Units</th>
                        <th>Date Needed</th>
                        <th>Urgency</th>
                        <th>Status</th>
                        <th>Posted</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $i => $req): ?>
                    <tr>
                        <td><?php echo $i + 1; ?></td>
                        <td><strong><?php echo e($req['patient_name']); ?></strong></td>
                        <td style="max-width: 200px; font-size: 0.8rem;"><?php echo e($req['hospital_address']); ?></td>
                        <td><span class="badge badge-blood"><?php echo e($req['required_blood_group']); ?></span></td>
                        <td><?php echo $req['units_needed']; ?></td>
                        <td><?php echo formatDate($req['date_needed']); ?></td>
                        <td><span class="badge <?php echo getUrgencyClass($req['urgency']); ?>"><?php echo e($req['urgency']); ?></span></td>
                        <td><span class="badge <?php echo getStatusClass($req['status']); ?>"><?php echo e($req['status']); ?></span></td>
                        <td style="font-size: 0.8rem;"><?php echo formatDate($req['created_at']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>
