<?php
/**
 * Search Donors Page
 * Visitors can search by blood group and district
 * Contact info hidden for non-logged-in users
 */

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Find Donors';
$extraCSS = [];

$db = getDB();
$donors = [];
$searchPerformed = false;
$bloodGroupFilter = $_GET['blood_group'] ?? '';
$districtFilter = $_GET['district'] ?? '';

if ($bloodGroupFilter || $districtFilter) {
    $searchPerformed = true;
    $sql = "SELECT name, blood_group, district, age, phone, email, last_donation_date 
            FROM users WHERE role='user' AND status='active'";
    $params = [];

    if ($bloodGroupFilter) {
        $sql .= " AND blood_group = ?";
        $params[] = $bloodGroupFilter;
    }

    if ($districtFilter) {
        $sql .= " AND district = ?";
        $params[] = $districtFilter;
    }

    $sql .= " ORDER BY name ASC";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $donors = $stmt->fetchAll();
}

$districts = getDistricts();
$bloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];

include 'includes/header.php';
?>

<div class="page-content">
    <div class="container">
        <div class="page-header text-center" style="padding-top: 20px;">
            <h1>🔍 Find Blood Donors</h1>
            <p>Search for blood donors by blood group and location. <?php if (!isLoggedIn()): ?>Login to view contact details.<?php endif; ?></p>
        </div>

        <!-- Search Form -->
        <div class="card" style="max-width: 800px; margin: 0 auto var(--space-2xl);">
            <form method="GET" action="search_donors.php" id="searchDonorForm">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="blood_group">Blood Group</label>
                        <select class="form-control" id="blood_group" name="blood_group">
                            <option value="">All Blood Groups</option>
                            <?php foreach ($bloodGroups as $bg): ?>
                                <option value="<?php echo $bg; ?>" <?php echo $bloodGroupFilter === $bg ? 'selected' : ''; ?>>
                                    <?php echo $bg; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="district">District</label>
                        <select class="form-control" id="district" name="district">
                            <option value="">All Districts</option>
                            <?php foreach ($districts as $dist): ?>
                                <option value="<?php echo $dist; ?>" <?php echo $districtFilter === $dist ? 'selected' : ''; ?>>
                                    <?php echo $dist; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div style="display: flex; gap: 1rem;">
                    <button type="submit" class="btn btn-primary">🔍 Search Donors</button>
                    <a href="search_donors.php" class="btn btn-secondary">Clear</a>
                </div>
            </form>
        </div>

        <!-- Results -->
        <?php if ($searchPerformed): ?>
            <div class="animate-fadeInUp">
                <p class="text-muted" style="margin-bottom: var(--space-lg);">
                    Found <strong style="color: var(--primary-light);"><?php echo count($donors); ?></strong> donor(s)
                    <?php if ($bloodGroupFilter): ?> with blood group <strong><?php echo e($bloodGroupFilter); ?></strong><?php endif; ?>
                    <?php if ($districtFilter): ?> in <strong><?php echo e($districtFilter); ?></strong><?php endif; ?>
                </p>

                <?php if (empty($donors)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">😔</div>
                        <h3>No Donors Found</h3>
                        <p>Try searching with different criteria or check back later.</p>
                    </div>
                <?php else: ?>
                    <div class="table-wrapper">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Blood Group</th>
                                    <th>District</th>
                                    <th>Age</th>
                                    <th>Last Donation</th>
                                    <th>Contact</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($donors as $i => $donor): ?>
                                <tr>
                                    <td><?php echo $i + 1; ?></td>
                                    <td><strong><?php echo e($donor['name']); ?></strong></td>
                                    <td><span class="badge badge-blood"><?php echo e($donor['blood_group']); ?></span></td>
                                    <td><?php echo e($donor['district']); ?></td>
                                    <td><?php echo $donor['age']; ?></td>
                                    <td><?php echo formatDate($donor['last_donation_date']); ?></td>
                                    <td>
                                        <?php if (isLoggedIn()): ?>
                                            <div style="font-size: 0.8rem;">
                                                📞 <?php echo e($donor['phone']); ?><br>
                                                📧 <?php echo e($donor['email']); ?>
                                            </div>
                                        <?php else: ?>
                                            <a href="login.php" class="btn btn-sm btn-outline">Login to View</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-icon">🔍</div>
                <h3>Search for Donors</h3>
                <p>Select a blood group or district above to find available donors near you.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
