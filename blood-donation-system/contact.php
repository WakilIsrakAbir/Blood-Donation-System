<?php
/**
 * Contact Us Page
 * Form saves to contact_messages table
 */

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Contact Us';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid form submission. Please try again.');
        redirect('contact.php');
    }

    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Server-side validation
    $errors = [];
    if (empty($name)) $errors[] = 'Name is required.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
    if (empty($message) || strlen($message) < 10) $errors[] = 'Message must be at least 10 characters.';

    if (empty($errors)) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $message]);

        setFlash('success', 'Your message has been sent successfully! We\'ll get back to you soon.');
        redirect('contact.php');
    } else {
        setFlash('error', implode(' ', $errors));
    }
}

include 'includes/header.php';
?>

<div class="page-content">
    <div class="container">
        <div style="text-align: center; padding: var(--space-3xl) 0 var(--space-2xl);">
            <h1 style="font-size: 2.5rem; margin-bottom: var(--space-md);">Get in Touch</h1>
            <p style="max-width: 500px; margin: 0 auto; font-size: 1rem;">
                Have a question or feedback? We'd love to hear from you. Send us a message and we'll respond as soon as possible.
            </p>
        </div>

        <div class="grid-2" style="max-width: 1000px; margin: 0 auto; align-items: start;">
            <!-- Contact Form -->
            <div class="card animate-fadeInUp">
                <h3 style="margin-bottom: var(--space-xl);">📩 Send a Message</h3>
                <form method="POST" action="contact.php" id="contactForm">
                    <?php echo csrfField(); ?>
                    
                    <div class="form-group">
                        <label class="form-label" for="contact_name">Your Name</label>
                        <input type="text" class="form-control" id="contact_name" name="name" 
                               placeholder="Enter your full name" value="<?php echo e($_POST['name'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="contact_email">Email Address</label>
                        <input type="email" class="form-control" id="contact_email" name="email" 
                               placeholder="your@email.com" value="<?php echo e($_POST['email'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="contact_message">Message</label>
                        <textarea class="form-control" id="contact_message" name="message" rows="5" 
                                  placeholder="Write your message here..."><?php echo e($_POST['message'] ?? ''); ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-full">Send Message</button>
                </form>
            </div>

            <!-- Contact Info -->
            <div>
                <div class="card" style="margin-bottom: var(--space-xl);">
                    <h3 style="margin-bottom: var(--space-xl);">📍 Contact Information</h3>
                    <ul style="list-style: none; font-size: 0.9rem;">
                        <li style="display: flex; gap: var(--space-md); padding: var(--space-md) 0; border-bottom: 1px solid var(--border-color);">
                            <span style="font-size: 1.2rem;">📍</span>
                            <div>
                                <strong style="display: block; color: var(--text-primary);">Address</strong>
                                <span style="color: var(--text-muted);">Dhaka, Bangladesh</span>
                            </div>
                        </li>
                        <li style="display: flex; gap: var(--space-md); padding: var(--space-md) 0; border-bottom: 1px solid var(--border-color);">
                            <span style="font-size: 1.2rem;">📧</span>
                            <div>
                                <strong style="display: block; color: var(--text-primary);">Email</strong>
                                <span style="color: var(--text-muted);">info@bloodconnect.com</span>
                            </div>
                        </li>
                        <li style="display: flex; gap: var(--space-md); padding: var(--space-md) 0; border-bottom: 1px solid var(--border-color);">
                            <span style="font-size: 1.2rem;">📞</span>
                            <div>
                                <strong style="display: block; color: var(--text-primary);">Phone</strong>
                                <span style="color: var(--text-muted);">+880 1700-000000</span>
                            </div>
                        </li>
                        <li style="display: flex; gap: var(--space-md); padding: var(--space-md) 0;">
                            <span style="font-size: 1.2rem;">🕐</span>
                            <div>
                                <strong style="display: block; color: var(--text-primary);">Hours</strong>
                                <span style="color: var(--text-muted);">24/7 — We're always here to help</span>
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="card" style="background: rgba(220, 20, 60, 0.05); border-color: rgba(220, 20, 60, 0.15);">
                    <h4 style="color: var(--primary-light); margin-bottom: var(--space-sm);">🆘 Emergency?</h4>
                    <p style="font-size: 0.85rem; color: var(--text-muted); margin: 0;">
                        If you have an urgent blood need, please <a href="register.php" style="color: var(--primary-light); font-weight: 600;">register</a> 
                        and post a blood request. Our team will prioritize urgent cases.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
