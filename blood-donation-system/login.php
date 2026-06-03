<?php
/**
 * Login Page
 * Role-based redirect: admin → admin/dashboard, user → user/dashboard
 */

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Login';
$extraCSS = ['auth.css'];
$hideNavbar = true;

// Redirect if already logged in
redirectIfLoggedIn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid form submission.');
        redirect('login.php');
    }

    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        setFlash('error', 'Please fill in all fields.');
        redirect('login.php');
    }

    $db = getDB();
    $stmt = $db->prepare("SELECT id, name, email, password, role, status FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Check if user is banned
        if ($user['status'] === 'banned') {
            setFlash('error', 'Your account has been suspended. Please contact the admin.');
            redirect('login.php');
        }

        // Regenerate session ID for security
        session_regenerate_id(true);

        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['role'] = $user['role'];

        // Role-based redirect
        if ($user['role'] === 'admin') {
            setFlash('success', 'Welcome back, Admin ' . $user['name'] . '!');
            redirect('admin/dashboard.php');
        } else {
            setFlash('success', 'Welcome back, ' . $user['name'] . '!');
            redirect('user/dashboard.php');
        }
    } else {
        setFlash('error', 'Invalid email or password. Please try again.');
        redirect('login.php');
    }
}

include 'includes/header.php';
?>

<div class="auth-page">
    <div class="auth-container">
        <div class="auth-header">
            <a href="index.php" class="auth-logo">
                <div class="brand-icon">🩸</div>
                <span>BloodConnect</span>
            </a>
            <h1>Welcome Back</h1>
            <p>Login to your account to continue saving lives.</p>
        </div>

        <div class="auth-card">
            <?php echo displayFlashMessages(); ?>
            <form method="POST" action="login.php" id="loginForm">
                <?php echo csrfField(); ?>
                
                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" 
                           placeholder="your@email.com" required autofocus>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="password-wrapper">
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Enter your password" required>
                        <button type="button" class="password-toggle">👁️</button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-full">Login</button>
            </form>

            <div class="auth-divider">or</div>
            
            <div style="text-align: center;">
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: var(--space-sm);">Demo Accounts:</p>
                <p style="font-size: 0.8rem; color: var(--text-muted); margin: 0;">
                    <strong>Admin:</strong> admin@blooddonation.com<br>
                    <strong>Password:</strong> admin123<br>
                    <strong>User:</strong> wakilisrakabir@gmail.com<br>
                    <strong>Password:</strong> 123456
                </p>
            </div>
        </div>

        <div class="auth-footer">
            Don't have an account? <a href="register.php">Register here</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
