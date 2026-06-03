<?php
/**
 * Available Blood Requests Feed
 * Shows approved requests with "I Want to Donate" button
 * Implements self-block and 90-day rule
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('user');

$pageTitle = 'Available Requests';
$hideNavbar = true;
$currentPage = 'available_requests';

$db = getDB();
$userId = getUserId();

// Get current user's info for eligibility check
$userStmt = $db->prepare("SELECT last_donation_date FROM users WHERE id = ?");
$userStmt->execute([$userId]);
$currentUser = $userStmt->fetch();
$eligibility = checkDonationEligibility($currentUser['last_donation_date']);

// Handle "I Want to Donate" action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['donate_request_id'])) {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid form submission.');
        redirect('user/available_requests.php');
    }

    $requestId = (int) $_POST['donate_request_id'];

    // Fetch the request
    $reqStmt = $db->prepare("SELECT * FROM blood_requests WHERE id = ? AND status = 'Approved'");
    $reqStmt->execute([$requestId]);
    $request = $reqStmt->fetch();

    if (!$request) {
        setFlash('error', 'Request not found or not available.');
    } elseif ($request['user_id'] == $userId) {
        // Self-block: Can't donate to own request
        setFlash('error', 'You cannot donate to your own blood request.');
    } elseif (!$eligibility['eligible']) {
        // 90-day rule
        setFlash('error', 'You are not eligible to donate yet. Please wait ' . $eligibility['days_remaining'] . ' more days.');
    } else {
        // Check if already pledged
        $existingPledge = $db->prepare("SELECT id FROM donations WHERE donor_id = ? AND request_id = ?");
        $existingPledge->execute([$userId, $requestId]);

        if ($existingPledge->fetch()) {
            setFlash('error', 'You have already pledged to donate for this request.');
        } else {
            $stmt = $db->prepare("INSERT INTO donations (donor_id, request_id, status) VALUES (?, ?, 'Pledged')");
            $stmt->execute([$userId, $requestId]);
            setFlash('success', 'Thank you! Your donation pledge has been recorded. Admin will confirm upon completion.');
        }
    }
    redirect('user/available_requests.php');
}

// Fetch all approved requests
$requests = $db->query("SELECT br.*, u.name as requester_name, u.phone as requester_phone, u.district as requester_district
                         FROM blood_requests br 
                         JOIN users u ON br.user_id = u.id 
                         WHERE br.status = 'Approved' 
                         ORDER BY FIELD(br.urgency, 'Critical', 'Urgent', 'Normal'), br.date_needed ASC")
                ->fetchAll();

// Get user's existing pledges
$pledgeStmt = $db->prepare("SELECT request_id FROM donations WHERE donor_id = ?");
$pledgeStmt->execute([$userId]);
$myPledges = array_column($pledgeStmt->fetchAll(), 'request_id');

include __DIR__ . '/sidebar.php';
?>

<main class="dashboard-main">
    <div class="dash-header">
        <div>
            <h1>📢 Available Blood Requests</h1>
            <p class="welcome-text">
                Approved requests from users who need blood. 
                <?php if (!$eligibility['eligible']): ?>
                    <span style="color: var(--status-rejected);">⚠️ You are not eligible to donate for <?php echo $eligibility['days_remaining']; ?> more days.</span>
                <?php else: ?>
                    <span style="color: var(--accent-green);">✅ You are eligible to donate!</span>
                <?php endif; ?>
            </p>
        </div>
    </div>

    <?php if (empty($requests)): ?>
        <div class="card">
            <div class="empty-state">
                <div class="empty-icon">📢</div>
                <h3>No Active Requests</h3>
                <p>There are no approved blood requests at the moment. Check back later!</p>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($requests as $request): ?>
        <div class="feed-card">
            <div class="feed-card-top">
                <div class="blood-badge"><?php echo e($request['required_blood_group']); ?></div>
                <div class="feed-card-info">
                    <h4><?php echo e($request['patient_name']); ?></h4>
                    <div class="hospital-name">📍 <?php echo e($request['hospital_address']); ?></div>
                </div>
                <span class="badge <?php echo getUrgencyClass($request['urgency']); ?>"><?php echo e($request['urgency']); ?></span>
            </div>

            <div class="feed-card-meta">
                <div class="meta-item">🩸 <?php echo $request['units_needed']; ?> unit(s) needed</div>
                <div class="meta-item">📅 By <?php echo formatDate($request['date_needed']); ?></div>
                <div class="meta-item">👤 Requested by <?php echo e($request['requester_name']); ?></div>
                <div class="meta-item">📍 <?php echo e($request['requester_district']); ?></div>
            </div>

            <div class="feed-card-actions">
                <span style="font-size: 0.8rem; color: var(--text-muted);">
                    Posted <?php echo formatDate($request['created_at']); ?>
                </span>

                <?php if (in_array($request['id'], $myPledges)): ?>
                    <span class="btn btn-sm btn-success" style="opacity: 0.7; cursor: default;">✅ Already Pledged</span>
                <?php elseif ($request['user_id'] == $userId): ?>
                    <span class="btn btn-sm btn-secondary" style="opacity: 0.5; cursor: default;">Your Request</span>
                <?php else: ?>
                    <form method="POST" style="margin: 0;" onsubmit="return confirm('Are you sure you want to pledge to donate blood for this request?');">
                        <?php echo csrfField(); ?>
                        <input type="hidden" name="donate_request_id" value="<?php echo $request['id']; ?>">
                        <button type="submit" class="btn btn-sm btn-primary" <?php echo !$eligibility['eligible'] ? 'disabled title="Not eligible yet"' : ''; ?>>
                            🩸 I Want to Donate
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>
