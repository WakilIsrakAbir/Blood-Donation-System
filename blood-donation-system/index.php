<?php
/**
 * Home Page - index.php
 * Public landing page with hero, live stats, and recent requests
 */

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/session.php';

$pageTitle = 'Home';
$extraCSS = ['home.css'];

// Fetch live stats
$db = getDB();

$totalDonors = $db->query("SELECT COUNT(*) FROM users WHERE role='user' AND status='active'")->fetchColumn();
$totalDonations = $db->query("SELECT COUNT(*) FROM donations WHERE status='Completed'")->fetchColumn();
$activeRequests = $db->query("SELECT COUNT(*) FROM blood_requests WHERE status='Approved'")->fetchColumn();

// Fetch recent approved urgent requests
$stmt = $db->query("SELECT br.*, u.name as requester_name 
                     FROM blood_requests br 
                     JOIN users u ON br.user_id = u.id 
                     WHERE br.status = 'Approved' 
                     ORDER BY br.urgency DESC, br.created_at DESC 
                     LIMIT 6");
$recentRequests = $stmt->fetchAll();

// Blood group counts
$bgCounts = [];
$bgStmt = $db->query("SELECT blood_group, COUNT(*) as count FROM users WHERE role='user' AND status='active' GROUP BY blood_group");
while ($row = $bgStmt->fetch()) {
    $bgCounts[$row['blood_group']] = $row['count'];
}

include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero" id="hero">
    <div class="hero-bg">
        <div class="hero-grid"></div>
    </div>
    
    <div class="hero-content">
        <div class="hero-text">
            <div class="hero-badge">
                <span class="pulse-dot"></span>
                Saving Lives Every Day
            </div>
            
            <h1 class="hero-title">
                Donate Blood,<br>
                <span class="highlight">Save a Life</span> Today
            </h1>
            
            <p class="hero-subtitle">
                Join thousands of voluntary blood donors in Bangladesh. Your single donation 
                can save up to three lives. Find donors near you or register to become one.
            </p>
            
            <div class="hero-actions">
                <?php if (isLoggedIn()): ?>
                    <a href="<?php echo getUserRole() === 'admin' ? 'admin/dashboard.php' : 'user/dashboard.php'; ?>" class="btn btn-primary btn-lg">📊 Go to Dashboard</a>
                <?php else: ?>
                    <a href="register.php" class="btn btn-primary btn-lg">Become a Donor</a>
                <?php endif; ?>
                <a href="search_donors.php" class="btn btn-secondary btn-lg">Find Donors</a>
            </div>
            
            <div class="hero-stats">
                <div class="hero-stat">
                    <div class="stat-num"><span data-count="<?php echo $totalDonors; ?>">0</span><span>+</span></div>
                    <div class="stat-text">Registered Donors</div>
                </div>
                <div class="hero-stat">
                    <div class="stat-num"><span data-count="<?php echo $totalDonations; ?>">0</span><span>+</span></div>
                    <div class="stat-text">Successful Donations</div>
                </div>
                <div class="hero-stat">
                    <div class="stat-num"><span data-count="<?php echo $activeRequests; ?>">0</span><span>+</span></div>
                    <div class="stat-text">Active Requests</div>
                </div>
            </div>
        </div>
        
        <div class="hero-visual">
            <div class="hero-card-stack">
                <div class="hero-card">
                    <div class="hero-card-label">Available Blood Groups</div>
                    <div class="blood-groups-grid">
                        <?php
                        $bloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
                        foreach ($bloodGroups as $bg):
                        ?>
                        <div class="blood-group-item">
                            <span class="bg-type"><?php echo $bg; ?></span>
                            <span class="bg-count"><?php echo $bgCounts[$bg] ?? 0; ?> donors</span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="hero-card-info">
                        <div class="info-icon">✓</div>
                        <div class="info-text">All blood types available. Register now to help!</div>
                    </div>
                </div>
                
                <div class="hero-float-card top-right">
                    <div class="float-label">Total Donors</div>
                    <div class="float-value"><?php echo $totalDonors; ?>+</div>
                </div>
                
                <div class="hero-float-card bottom-left">
                    <div class="float-label">Lives Saved</div>
                    <div class="float-value"><?php echo $totalDonations * 3; ?>+</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="section" id="how-it-works">
    <div class="container">
        <div class="section-header" data-animate>
            <div class="section-badge">How It Works</div>
            <h2>Three Simple Steps</h2>
            <p>Saving a life has never been easier. Follow these simple steps to become a blood donor.</p>
        </div>
        
        <div class="steps-grid">
            <div class="step-card" data-animate>
                <div class="step-number">1</div>
                <h3>Register</h3>
                <p>Create your free account with your blood group, location, and contact details to join our donor network.</p>
            </div>
            <div class="step-card" data-animate>
                <div class="step-number">2</div>
                <h3>Find or Post</h3>
                <p>Search for available donors by blood group and district, or post a blood request when someone is in need.</p>
            </div>
            <div class="step-card" data-animate>
                <div class="step-number">3</div>
                <h3>Donate & Save</h3>
                <p>Respond to blood requests and donate. Every donation can save up to three precious lives.</p>
            </div>
        </div>
    </div>
</section>

<!-- Recent Urgent Requests -->
<?php if (!empty($recentRequests)): ?>
<section class="section" id="recent-requests">
    <div class="container">
        <div class="section-header" data-animate>
            <div class="section-badge">Urgent Needs</div>
            <h2>Recent Blood Requests</h2>
            <p>These patients urgently need blood. If you can help, please login and donate.</p>
        </div>
        
        <div class="requests-grid">
            <?php foreach ($recentRequests as $request): ?>
            <div class="request-card <?php echo strtolower($request['urgency']); ?>" data-animate>
                <div class="request-card-header">
                    <div>
                        <div class="patient-name"><?php echo e($request['patient_name']); ?></div>
                        <div class="hospital">📍 <?php echo e($request['hospital_address']); ?></div>
                    </div>
                    <span class="blood-type"><?php echo e($request['required_blood_group']); ?></span>
                </div>
                
                <div class="request-card-footer">
                    <span class="badge <?php echo getUrgencyClass($request['urgency']); ?>"><?php echo e($request['urgency']); ?></span>
                    <span>📅 Needed by <?php echo formatDate($request['date_needed']); ?></span>
                    <span>🩸 <?php echo $request['units_needed']; ?> unit(s)</span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (isLoggedIn()): ?>
            <div class="text-center mt-2">
                <a href="user/available_requests.php" class="btn btn-outline btn-lg">View All Requests →</a>
            </div>
        <?php else: ?>
            <div class="text-center mt-2">
                <a href="login.php" class="btn btn-outline btn-lg">Login to Help →</a>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<!-- CTA Section -->
<section class="section">
    <div class="container">
        <div class="cta-section" data-animate>
            <?php if (isLoggedIn()): ?>
                <h2>Make a Difference Today!</h2>
                <p>Check available blood requests and help save someone's life. Every donation counts.</p>
                <div style="display: flex; gap: 1rem; justify-content: center; position: relative;">
                    <a href="<?php echo getUserRole() === 'admin' ? 'admin/dashboard.php' : 'user/available_requests.php'; ?>" class="btn btn-primary btn-lg">View Blood Requests</a>
                    <a href="about.php" class="btn btn-secondary btn-lg">Learn More</a>
                </div>
            <?php else: ?>
                <h2>Ready to Save a Life?</h2>
                <p>Join our community of heroes. Register as a blood donor today and be the reason someone smiles tomorrow.</p>
                <div style="display: flex; gap: 1rem; justify-content: center; position: relative;">
                    <a href="register.php" class="btn btn-primary btn-lg">Register Now</a>
                    <a href="about.php" class="btn btn-secondary btn-lg">Learn More</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
