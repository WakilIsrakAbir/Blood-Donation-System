<?php
/**
 * User Profile & Settings
 * Update profile details and last donation date
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('user');

$pageTitle = 'Profile';
$hideNavbar = true;
$currentPage = 'profile';

$db = getDB();
$userId = getUserId();

// Fetch user data
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid form submission.');
        redirect('user/profile.php');
    }

    $action = $_POST['action'] ?? 'update_profile';

    if ($action === 'update_profile') {
        $name = sanitize($_POST['name'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $district = $_POST['district'] ?? '';
        $age = (int) ($_POST['age'] ?? 0);
        $lastDonationDate = !empty($_POST['last_donation_date']) ? $_POST['last_donation_date'] : null;

        $errors = [];
        if (empty($name) || strlen($name) < 3) $errors[] = 'Name must be at least 3 characters.';
        if (empty($phone)) $errors[] = 'Phone number is required.';
        if ($age < 18 || $age > 65) $errors[] = 'Age must be between 18 and 65.';

        if (empty($errors)) {
            $stmt = $db->prepare("UPDATE users SET name=?, phone=?, district=?, age=?, last_donation_date=?, updated_at=NOW() WHERE id=?");
            $stmt->execute([$name, $phone, $district, $age, $lastDonationDate, $userId]);
            
            $_SESSION['user_name'] = $name;
            setFlash('success', 'Profile updated successfully!');
            redirect('user/profile.php');
        } else {
            setFlash('error', implode(' ', $errors));
        }
    } elseif ($action === 'change_password') {
        $oldPassword = $_POST['old_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmNew = $_POST['confirm_new_password'] ?? '';

        if (!password_verify($oldPassword, $user['password'])) {
            setFlash('error', 'Current password is incorrect.');
        } elseif (strlen($newPassword) < 6) {
            setFlash('error', 'New password must be at least 6 characters.');
        } elseif ($newPassword !== $confirmNew) {
            setFlash('error', 'New passwords do not match.');
        } else {
            $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE users SET password=? WHERE id=?");
            $stmt->execute([$hashed, $userId]);
            setFlash('success', 'Password changed successfully!');
        }
        redirect('user/profile.php');
    }
}

$districts = getDistricts();

include __DIR__ . '/sidebar.php';
?>

<main class="dashboard-main">
    <div class="dash-header">
        <div>
            <h1>Profile & Settings</h1>
            <p class="welcome-text">Update your personal information</p>
        </div>
    </div>

    <div class="profile-grid">
        <!-- Profile Sidebar -->
        <div>
            <div class="card" style="text-align: center;">
                <div class="profile-avatar">
                    <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                </div>
                <div class="profile-name"><?php echo e($user['name']); ?></div>
                <div class="profile-role">
                    <span class="badge badge-blood"><?php echo e($user['blood_group']); ?></span>
                </div>
                <ul class="profile-info-list">
                    <li><span class="info-label">📧 Email</span> <span class="info-value"><?php echo e($user['email']); ?></span></li>
                    <li><span class="info-label">📞 Phone</span> <span class="info-value"><?php echo e($user['phone']); ?></span></li>
                    <li><span class="info-label">📍 District</span> <span class="info-value"><?php echo e($user['district']); ?></span></li>
                    <li><span class="info-label">🎂 Age</span> <span class="info-value"><?php echo $user['age']; ?></span></li>
                    <li><span class="info-label">📅 Last Donation</span> <span class="info-value"><?php echo formatDate($user['last_donation_date']); ?></span></li>
                    <li><span class="info-label">🗓️ Joined</span> <span class="info-value"><?php echo formatDate($user['created_at']); ?></span></li>
                </ul>
            </div>
        </div>

        <!-- Edit Forms -->
        <div>
            <!-- Update Profile -->
            <div class="card" style="margin-bottom: var(--space-xl);">
                <div class="card-header">
                    <h3 class="card-title">✏️ Edit Profile</h3>
                </div>
                <form method="POST" id="profileForm">
                    <?php echo csrfField(); ?>
                    <input type="hidden" name="action" value="update_profile">

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="name">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo e($user['name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="phone">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo e($user['phone']); ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="district">District</label>
                            <select class="form-control" id="district" name="district" required>
                                <?php foreach ($districts as $dist): ?>
                                    <option value="<?php echo $dist; ?>" <?php echo $user['district'] === $dist ? 'selected' : ''; ?>>
                                        <?php echo $dist; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="age">Age</label>
                            <input type="number" class="form-control" id="age" name="age" value="<?php echo $user['age']; ?>" min="18" max="65" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="last_donation_date">Last Donation Date</label>
                        <input type="date" class="form-control" id="last_donation_date" name="last_donation_date" 
                               value="<?php echo $user['last_donation_date'] ?? ''; ?>" max="<?php echo date('Y-m-d'); ?>">
                        <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 4px;">
                            Update this if you donated blood outside of BloodConnect.
                        </p>
                    </div>

                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>

            <!-- Change Password -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">🔒 Change Password</h3>
                </div>
                <form method="POST">
                    <?php echo csrfField(); ?>
                    <input type="hidden" name="action" value="change_password">

                    <div class="form-group">
                        <label class="form-label" for="old_password">Current Password</label>
                        <input type="password" class="form-control" id="old_password" name="old_password" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="new_password">New Password</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required minlength="6">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="confirm_new_password">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirm_new_password" name="confirm_new_password" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-secondary">Change Password</button>
                </form>
            </div>
        </div>
    </div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
