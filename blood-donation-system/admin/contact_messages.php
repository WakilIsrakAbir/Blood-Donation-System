<?php
/**
 * Contact Messages - Admin
 * View, read, and delete contact form submissions
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('admin');

$pageTitle = 'Contact Messages';
$hideNavbar = true;
$currentPage = 'contact_messages';

$db = getDB();

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid form submission.');
        redirect('admin/contact_messages.php');
    }

    $action = $_POST['action'] ?? '';
    $msgId = (int) ($_POST['message_id'] ?? 0);

    if ($action === 'mark_read') {
        $db->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?")->execute([$msgId]);
        setFlash('success', 'Message marked as read.');
    } elseif ($action === 'mark_unread') {
        $db->prepare("UPDATE contact_messages SET is_read = 0 WHERE id = ?")->execute([$msgId]);
        setFlash('success', 'Message marked as unread.');
    } elseif ($action === 'delete') {
        $db->prepare("DELETE FROM contact_messages WHERE id = ?")->execute([$msgId]);
        setFlash('success', 'Message deleted.');
    }

    redirect('admin/contact_messages.php');
}

$messages = $db->query("SELECT * FROM contact_messages ORDER BY is_read ASC, created_at DESC")->fetchAll();

include __DIR__ . '/sidebar.php';
?>

<main class="dashboard-main">
    <div class="dash-header">
        <div>
            <h1>📬 Contact Messages</h1>
            <p class="welcome-text"><?php echo count($messages); ?> message(s) total</p>
        </div>
    </div>

    <?php if (empty($messages)): ?>
        <div class="card">
            <div class="empty-state">
                <div class="empty-icon">📬</div>
                <h3>No Messages</h3>
                <p>No contact form submissions yet.</p>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($messages as $msg): ?>
        <div class="card" style="margin-bottom: var(--space-lg); <?php echo !$msg['is_read'] ? 'border-left: 3px solid var(--primary);' : 'opacity: 0.8;'; ?>">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: var(--space-md);">
                <div>
                    <h4 style="margin-bottom: 2px;">
                        <?php if (!$msg['is_read']): ?><span style="color: var(--primary-light);">● </span><?php endif; ?>
                        <?php echo e($msg['name']); ?>
                    </h4>
                    <span style="font-size: 0.8rem; color: var(--text-muted);">📧 <?php echo e($msg['email']); ?> · <?php echo formatDate($msg['created_at']); ?></span>
                </div>
                <div style="display: flex; gap: 4px;">
                    <form method="POST" style="margin:0;">
                        <?php echo csrfField(); ?>
                        <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                        <?php if ($msg['is_read']): ?>
                            <input type="hidden" name="action" value="mark_unread">
                            <button type="submit" class="btn btn-sm btn-secondary" title="Mark as unread">📩</button>
                        <?php else: ?>
                            <input type="hidden" name="action" value="mark_read">
                            <button type="submit" class="btn btn-sm btn-secondary" title="Mark as read">✅</button>
                        <?php endif; ?>
                    </form>
                    <form method="POST" style="margin:0;">
                        <?php echo csrfField(); ?>
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                        <button type="submit" class="btn btn-sm btn-danger" data-confirm="Delete this message?">🗑️</button>
                    </form>
                </div>
            </div>
            <p style="font-size: 0.9rem; color: var(--text-secondary); margin: 0; line-height: 1.7; white-space: pre-wrap;"><?php echo e($msg['message']); ?></p>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>
