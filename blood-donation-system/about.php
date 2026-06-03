<?php
/**
 * About Us Page
 */

$pageTitle = 'About Us';
include 'includes/header.php';
?>

<div class="page-content">
    <div class="container">
        <!-- About Hero -->
        <div style="text-align: center; padding: var(--space-3xl) 0 var(--space-2xl);">
            <h1 style="font-size: 2.5rem; margin-bottom: var(--space-md);">About <span style="background: linear-gradient(135deg, var(--primary-light), #FF6B8A); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">BloodConnect</span></h1>
            <p style="max-width: 600px; margin: 0 auto; font-size: 1.05rem; line-height: 1.8;">
                We are a community-driven platform connecting blood donors with patients in need across Bangladesh.
                Our mission is to make blood donation accessible, safe, and efficient.
            </p>
        </div>

        <!-- Mission/Vision Cards -->
        <div class="grid-3" style="margin-bottom: var(--space-3xl);" data-animate>
            <div class="card" style="text-align: center;">
                <div style="font-size: 2.5rem; margin-bottom: var(--space-md);">🎯</div>
                <h3 style="margin-bottom: var(--space-sm);">Our Mission</h3>
                <p style="font-size: 0.9rem;">To bridge the gap between blood donors and patients, ensuring no life is lost due to lack of blood availability.</p>
            </div>
            <div class="card" style="text-align: center;">
                <div style="font-size: 2.5rem; margin-bottom: var(--space-md);">👁️</div>
                <h3 style="margin-bottom: var(--space-sm);">Our Vision</h3>
                <p style="font-size: 0.9rem;">A Bangladesh where every patient has timely access to safe blood, supported by a network of voluntary donors.</p>
            </div>
            <div class="card" style="text-align: center;">
                <div style="font-size: 2.5rem; margin-bottom: var(--space-md);">💪</div>
                <h3 style="margin-bottom: var(--space-sm);">Our Values</h3>
                <p style="font-size: 0.9rem;">Transparency, community service, safety, and the belief that every drop of blood can make a difference.</p>
            </div>
        </div>

        <!-- Blood Donation Facts -->
        <div class="card" style="margin-bottom: var(--space-3xl);" data-animate>
            <div class="card-header">
                <h3 class="card-title">🩸 Blood Donation Facts</h3>
            </div>
            <div class="grid-2" style="gap: var(--space-xl);">
                <div>
                    <ul style="list-style: none; font-size: 0.9rem; color: var(--text-secondary);">
                        <li style="padding: var(--space-md) 0; border-bottom: 1px solid var(--border-color); display: flex; gap: var(--space-md);">
                            <span>🔴</span> Every 2 seconds, someone needs blood.
                        </li>
                        <li style="padding: var(--space-md) 0; border-bottom: 1px solid var(--border-color); display: flex; gap: var(--space-md);">
                            <span>🔴</span> One donation can save up to 3 lives.
                        </li>
                        <li style="padding: var(--space-md) 0; border-bottom: 1px solid var(--border-color); display: flex; gap: var(--space-md);">
                            <span>🔴</span> Blood cannot be manufactured — it can only come from donors.
                        </li>
                        <li style="padding: var(--space-md) 0; display: flex; gap: var(--space-md);">
                            <span>🔴</span> The donation process takes only about 10-15 minutes.
                        </li>
                    </ul>
                </div>
                <div>
                    <ul style="list-style: none; font-size: 0.9rem; color: var(--text-secondary);">
                        <li style="padding: var(--space-md) 0; border-bottom: 1px solid var(--border-color); display: flex; gap: var(--space-md);">
                            <span>🔴</span> Type O- is the universal donor blood type.
                        </li>
                        <li style="padding: var(--space-md) 0; border-bottom: 1px solid var(--border-color); display: flex; gap: var(--space-md);">
                            <span>🔴</span> You can donate blood every 90 days (3 months).
                        </li>
                        <li style="padding: var(--space-md) 0; border-bottom: 1px solid var(--border-color); display: flex; gap: var(--space-md);">
                            <span>🔴</span> Your body replaces the donated blood within 24-48 hours.
                        </li>
                        <li style="padding: var(--space-md) 0; display: flex; gap: var(--space-md);">
                            <span>🔴</span> Blood donors must be between 18-65 years of age.
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Eligibility Criteria -->
        <div id="eligibility" data-animate>
            <div class="section-header">
                <div class="section-badge" style="background: rgba(0, 214, 143, 0.1); border-color: rgba(0, 214, 143, 0.2); color: var(--accent-green);">Eligibility</div>
                <h2>Who Can Donate Blood?</h2>
                <p>Before donating, make sure you meet these basic requirements.</p>
            </div>

            <div class="grid-2" style="max-width: 800px; margin: 0 auto var(--space-3xl);">
                <div class="card">
                    <h4 style="color: var(--accent-green); margin-bottom: var(--space-lg);">✅ You CAN Donate If:</h4>
                    <ul style="list-style: none; font-size: 0.85rem; color: var(--text-secondary); line-height: 2;">
                        <li>• You are between 18-65 years old</li>
                        <li>• You weigh at least 50 kg (110 lbs)</li>
                        <li>• You are in good general health</li>
                        <li>• Your last donation was 90+ days ago</li>
                        <li>• Your hemoglobin is at normal levels</li>
                    </ul>
                </div>
                <div class="card">
                    <h4 style="color: var(--status-rejected); margin-bottom: var(--space-lg);">❌ You CANNOT Donate If:</h4>
                    <ul style="list-style: none; font-size: 0.85rem; color: var(--text-secondary); line-height: 2;">
                        <li>• You have a cold, flu, or infection</li>
                        <li>• You are pregnant or recently gave birth</li>
                        <li>• You had surgery in the last 6 months</li>
                        <li>• You have chronic diseases (consult doctor)</li>
                        <li>• You donated blood less than 90 days ago</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <div id="faq" data-animate>
            <div class="section-header">
                <div class="section-badge">FAQ</div>
                <h2>Frequently Asked Questions</h2>
            </div>

            <div style="max-width: 800px; margin: 0 auto;" class="card">
                <?php
                $faqs = [
                    ['Is blood donation safe?', 'Absolutely! Blood donation is a safe process. A sterile needle is used only once for each donor and then discarded.'],
                    ['How long does it take?', 'The entire process takes about 30-45 minutes, but the actual blood donation only takes about 10-15 minutes.'],
                    ['How often can I donate?', 'You can donate whole blood every 90 days (3 months). Our system automatically tracks your eligibility.'],
                    ['Does it hurt?', 'You may feel a slight pinch when the needle is inserted, but the process is generally painless.'],
                    ['What should I do before donating?', 'Eat a healthy meal, drink plenty of water, get a good night\'s sleep, and avoid alcohol 24 hours before donation.'],
                ];
                foreach ($faqs as $i => $faq):
                ?>
                <div style="padding: var(--space-lg); <?php echo $i < count($faqs) - 1 ? 'border-bottom: 1px solid var(--border-color);' : ''; ?>">
                    <h4 style="margin-bottom: var(--space-sm); font-size: 0.95rem;"><?php echo $faq[0]; ?></h4>
                    <p style="font-size: 0.85rem; color: var(--text-muted); margin: 0;"><?php echo $faq[1]; ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
