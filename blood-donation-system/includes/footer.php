<?php
/**
 * Footer Component
 * - Public pages: Full footer with links (closes page-wrapper div from header)
 * - Dashboard pages: Minimal copyright footer inside dashboard-main
 */
?>

<?php if (isset($hideNavbar) && $hideNavbar): ?>
    <!-- Dashboard Footer -->
    <div class="dashboard-content-spacer"></div>
    <footer class="dashboard-footer">
        <p>&copy; <?php echo date('Y'); ?> BloodConnect. All rights reserved. Built with ❤️ for saving lives.</p>
    </footer>
    </main>

    <!-- Sidebar Toggle Button (mobile) -->
    <button class="sidebar-toggle">☰</button>

    <!-- Close dashboard-layout -->
    </div>
<?php else: ?>
    <!-- Close page-wrapper from header.php -->
    </div>
    
    <!-- Full Footer (public pages) -->
    <footer class="footer" id="footer">
        <div class="container">
            <div class="footer-grid">
                <div>
                    <div class="footer-brand">🩸 <span>BloodConnect</span></div>
                    <p class="footer-desc">
                        Connecting blood donors with patients in need. Every drop counts, 
                        every donation saves a life. Join our community and make a difference today.
                    </p>
                </div>
                <div>
                    <h4 class="footer-title">Quick Links</h4>
                    <ul class="footer-links">
                        <li><a href="<?php echo $basePath ?? ''; ?>index.php">Home</a></li>
                        <li><a href="<?php echo $basePath ?? ''; ?>search_donors.php">Find Donors</a></li>
                        <li><a href="<?php echo $basePath ?? ''; ?>about.php">About Us</a></li>
                        <li><a href="<?php echo $basePath ?? ''; ?>contact.php">Contact Us</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="footer-title">Resources</h4>
                    <ul class="footer-links">
                        <li><a href="<?php echo $basePath ?? ''; ?>register.php">Become a Donor</a></li>
                        <li><a href="<?php echo $basePath ?? ''; ?>about.php#eligibility">Eligibility</a></li>
                        <li><a href="<?php echo $basePath ?? ''; ?>about.php#faq">FAQ</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="footer-title">Contact Info</h4>
                    <ul class="footer-links">
                        <li>📍 Dhaka, Bangladesh</li>
                        <li>📧 info@bloodconnect.com</li>
                        <li>📞 +880 1700-000000</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> BloodConnect. All rights reserved. Built with ❤️ for saving lives.</p>
            </div>
        </div>
    </footer>
<?php endif; ?>

<!-- Scripts -->
<script src="<?php echo $basePath ?? ''; ?>assets/js/main.js"></script>
<script src="<?php echo $basePath ?? ''; ?>assets/js/validation.js"></script>
<?php if (isset($extraJS)): ?>
    <?php foreach ($extraJS as $js): ?>
        <script src="<?php echo $basePath ?? ''; ?>assets/js/<?php echo $js; ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>
