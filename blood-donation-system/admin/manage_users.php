<?php
/**
 * Manage Users - Admin
 * View, delete, ban/unban users
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('admin');

$pageTitle = 'Manage Users';
$hideNavbar = true;
$currentPage = 'manage_users';

$db = getDB();

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid form submission.');
        redirect('admin/manage_users.php');
    }

    $action = $_POST['action'] ?? '';
    $targetUserId = (int) ($_POST['user_id'] ?? 0);

    // Don't allow admin to modify themselves
    if ($targetUserId === getUserId()) {
        setFlash('error', 'You cannot modify your own account from here.');
        redirect('admin/manage_users.php');
    }

    if ($action === 'delete') {
        $stmt = $db->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
        $stmt->execute([$targetUserId]);
        setFlash('success', 'User deleted successfully.');
    } elseif ($action === 'ban') {
        $stmt = $db->prepare("UPDATE users SET status = 'banned' WHERE id = ? AND role != 'admin'");
        $stmt->execute([$targetUserId]);
        setFlash('success', 'User banned successfully.');
    } elseif ($action === 'unban') {
        $stmt = $db->prepare("UPDATE users SET status = 'active' WHERE id = ?");
        $stmt->execute([$targetUserId]);
        setFlash('success', 'User unbanned successfully.');
    }

    redirect('admin/manage_users.php');
}

// Search & fetch
$search = $_GET['search'] ?? '';
$sql = "SELECT * FROM users WHERE role = 'user'";
$params = [];

if ($search) {
    $sql .= " AND (name LIKE ? OR email LIKE ? OR blood_group = ?)";
    $params = ["%$search%", "%$search%", $search];
}

$sql .= " ORDER BY created_at DESC";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll();

include __DIR__ . '/sidebar.php';
?>

<main class="dashboard-main">
    <div class="dash-header">
        <div>
            <h1>👥 Manage Users</h1>
            <p class="welcome-text"><?php echo count($users); ?> registered user(s)</p>
        </div>
    </div>

    <!-- Search -->
    <div class="filter-bar">
        <form method="GET" style="display: flex; gap: var(--space-md); flex: 1;">
            <div class="search-input-wrapper">
                <span class="search-icon">🔍</span>
                <input type="text" class="form-control" name="search" placeholder="Search by name, email or blood group..." value="<?php echo e($search); ?>">
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Search</button>
            <?php if ($search): ?>
                <a href="manage_users.php" class="btn btn-secondary btn-sm">Clear</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if (empty($users)): ?>
        <div class="card">
            <div class="empty-state">
                <div class="empty-icon">👥</div>
                <h3>No Users Found</h3>
                <p><?php echo $search ? 'No users match your search criteria.' : 'No registered users yet.'; ?></p>
            </div>
        </div>
    <?php else: ?>
        <div class="table-card">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Blood</th>
                        <th>Phone</th>
                        <th>District</th>
                        <th>Age</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?php echo $u['id']; ?></td>
                        <td><strong><?php echo e($u['name']); ?></strong></td>
                        <td style="font-size: 0.8rem;"><?php echo e($u['email']); ?></td>
                        <td><span class="badge badge-blood"><?php echo e($u['blood_group']); ?></span></td>
                        <td style="font-size: 0.8rem;"><?php echo e($u['phone']); ?></td>
                        <td><?php echo e($u['district']); ?></td>
                        <td><?php echo $u['age']; ?></td>
                        <td>
                            <?php if ($u['status'] === 'active'): ?>
                                <span class="badge badge-approved">Active</span>
                            <?php else: ?>
                                <span class="badge badge-rejected">Banned</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-size: 0.8rem;"><?php echo formatDate($u['created_at']); ?></td>
                        <td>
                            <div style="display: flex; gap: 4px;">
                                <?php if ($u['status'] === 'active'): ?>
                                    <form method="POST" style="margin:0;">
                                        <?php echo csrfField(); ?>
                                        <input type="hidden" name="action" value="ban">
                                        <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-secondary" data-confirm="Ban this user?">🚫</button>
                                    </form>
                                <?php else: ?>
                                    <form method="POST" style="margin:0;">
                                        <?php echo csrfField(); ?>
                                        <input type="hidden" name="action" value="unban">
                                        <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-success">✅</button>
                                    </form>
                                <?php endif; ?>
                                <form method="POST" style="margin:0;">
                                    <?php echo csrfField(); ?>
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger" data-confirm="Are you sure you want to DELETE this user? This action cannot be undone.">🗑️</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>
