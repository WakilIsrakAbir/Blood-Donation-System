<?php
/**
 * Registration Page
 * New user registration with validation and password hashing
 */

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Register';
$extraCSS = ['auth.css'];
$hideNavbar = true;

// Redirect if already logged in
redirectIfLoggedIn();

$errors = [];
$old = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid form submission.');
        redirect('register.php');
    }

    $db = getDB();

    // Collect & sanitize input
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $bloodGroup = $_POST['blood_group'] ?? '';
    $phone = sanitize($_POST['phone'] ?? '');
    $district = $_POST['district'] ?? '';
    $age = (int) ($_POST['age'] ?? 0);
    $lastDonationDate = !empty($_POST['last_donation_date']) ? $_POST['last_donation_date'] : null;

    // Store old values for repopulating form
    $old = $_POST;

    // Server-side validation
    if (empty($name) || strlen($name) < 3) {
        $errors[] = 'Name must be at least 3 characters.';
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'A valid email address is required.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }
    if ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match.';
    }
    if (empty($bloodGroup) || !in_array($bloodGroup, ['A+','A-','B+','B-','AB+','AB-','O+','O-'])) {
        $errors[] = 'Please select a valid blood group.';
    }
    if (empty($phone)) {
        $errors[] = 'Phone number is required.';
    }
    if (empty($district)) {
        $errors[] = 'Please select your district.';
    }
    if ($age < 18) {
        $errors[] = 'You must be at least 18 years old.';
    }
    if ($age > 65) {
        $errors[] = 'Age must be 65 or below.';
    }

    // Check if email already exists
    if (empty($errors)) {
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'An account with this email already exists.';
        }
    }

    // If no errors, create user
    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $db->prepare("INSERT INTO users (name, email, password, role, blood_group, phone, district, age, last_donation_date) 
                              VALUES (?, ?, ?, 'user', ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $hashedPassword, $bloodGroup, $phone, $district, $age, $lastDonationDate]);

        setFlash('success', 'Registration successful! Please login with your credentials.');
        redirect('login.php');
    }
}

$districts = getDistricts();

include 'includes/header.php';
?>

<div class="auth-page">
    <div class="auth-container wide">
        <div class="auth-header">
            <a href="index.php" class="auth-logo">
                <div class="brand-icon">🩸</div>
                <span>BloodConnect</span>
            </a>
            <h1>Create Account</h1>
            <p>Join our community of blood donors and help save lives.</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <span class="alert-icon">✕</span>
                <span><?php echo implode('<br>', array_map('e', $errors)); ?></span>
            </div>
        <?php endif; ?>

        <?php echo displayFlashMessages(); ?>

        <div class="auth-card">
            <form method="POST" action="register.php" id="registerForm">
                <?php echo csrfField(); ?>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="name">Full Name *</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               placeholder="Enter your full name" value="<?php echo e($old['name'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="email">Email Address *</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               placeholder="your@email.com" value="<?php echo e($old['email'] ?? ''); ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="password">Password *</label>
                        <div class="password-wrapper">
                            <input type="password" class="form-control" id="password" name="password" 
                                   placeholder="Min 6 characters" required>
                            <button type="button" class="password-toggle">👁️</button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="confirm_password">Confirm Password *</label>
                        <div class="password-wrapper">
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                   placeholder="Repeat your password" required>
                            <button type="button" class="password-toggle">👁️</button>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="blood_group">Blood Group *</label>
                        <select class="form-control" id="blood_group" name="blood_group" required>
                            <option value="">Select Blood Group</option>
                            <?php foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg): ?>
                                <option value="<?php echo $bg; ?>" <?php echo ($old['blood_group'] ?? '') === $bg ? 'selected' : ''; ?>>
                                    <?php echo $bg; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="phone">Phone Number *</label>
                        <input type="tel" class="form-control" id="phone" name="phone" 
                               placeholder="01XXXXXXXXX" value="<?php echo e($old['phone'] ?? ''); ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="district">District *</label>
                        <select class="form-control" id="district" name="district" required>
                            <option value="">Select District</option>
                            <?php foreach ($districts as $dist): ?>
                                <option value="<?php echo $dist; ?>" <?php echo ($old['district'] ?? '') === $dist ? 'selected' : ''; ?>>
                                    <?php echo $dist; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="age">Age *</label>
                        <input type="number" class="form-control" id="age" name="age" 
                               placeholder="Must be 18+" min="18" max="65" value="<?php echo e($old['age'] ?? ''); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="last_donation_date">Last Donation Date (Optional)</label>
                    <input type="date" class="form-control" id="last_donation_date" name="last_donation_date" 
                           value="<?php echo e($old['last_donation_date'] ?? ''); ?>" max="<?php echo date('Y-m-d'); ?>">
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-full">Create Account</button>
            </form>
        </div>

        <div class="auth-footer">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
