<?php
/**
 * Post a Blood Request
 * Submits with status='Pending' for admin approval
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('user');

$pageTitle = 'Post Blood Request';
$hideNavbar = true;
$currentPage = 'request_blood';

$db = getDB();
$old = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid form submission.');
        redirect('request_blood.php');
    }

    $patientName = sanitize($_POST['patient_name'] ?? '');
    $hospitalAddress = sanitize($_POST['hospital_address'] ?? '');
    $requiredBloodGroup = $_POST['required_blood_group'] ?? '';
    $unitsNeeded = (int) ($_POST['units_needed'] ?? 1);
    $dateNeeded = $_POST['date_needed'] ?? '';
    $urgency = $_POST['urgency'] ?? 'Normal';

    $old = $_POST;

    $errors = [];
    if (empty($patientName)) $errors[] = 'Patient name is required.';
    if (empty($hospitalAddress)) $errors[] = 'Hospital address is required.';
    if (empty($requiredBloodGroup)) $errors[] = 'Blood group is required.';
    if ($unitsNeeded < 1) $errors[] = 'At least 1 unit is required.';
    if (empty($dateNeeded)) $errors[] = 'Date needed is required.';
    if (!in_array($urgency, ['Normal', 'Urgent', 'Critical'])) $errors[] = 'Invalid urgency level.';

    if (empty($errors)) {
        $stmt = $db->prepare("INSERT INTO blood_requests (user_id, patient_name, hospital_address, required_blood_group, units_needed, date_needed, urgency) 
                              VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([getUserId(), $patientName, $hospitalAddress, $requiredBloodGroup, $unitsNeeded, $dateNeeded, $urgency]);

        setFlash('success', 'Blood request submitted successfully! It will be visible once approved by admin.');
        redirect('my_requests.php');
    } else {
        setFlash('error', implode(' ', $errors));
    }
}

include __DIR__ . '/sidebar.php';
?>

<main class="dashboard-main">
    <div class="dash-header">
        <div>
            <h1>🩸 Post a Blood Request</h1>
            <p class="welcome-text">Fill in the details below. Your request will be reviewed by admin.</p>
        </div>
    </div>

    <div class="card" style="max-width: 700px;">
        <form method="POST" action="request_blood.php" id="requestBloodForm">
            <?php echo csrfField(); ?>

            <div class="form-group">
                <label class="form-label" for="patient_name">Patient Name *</label>
                <input type="text" class="form-control" id="patient_name" name="patient_name" 
                       placeholder="Enter patient's full name" value="<?php echo e($old['patient_name'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="hospital_address">Hospital Name & Address *</label>
                <input type="text" class="form-control" id="hospital_address" name="hospital_address" 
                       placeholder="e.g., Dhaka Medical College Hospital, Dhaka" value="<?php echo e($old['hospital_address'] ?? ''); ?>" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="required_blood_group">Required Blood Group *</label>
                    <select class="form-control" id="required_blood_group" name="required_blood_group" required>
                        <option value="">Select Blood Group</option>
                        <?php foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg): ?>
                            <option value="<?php echo $bg; ?>" <?php echo ($old['required_blood_group'] ?? '') === $bg ? 'selected' : ''; ?>>
                                <?php echo $bg; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="units_needed">Units (Bags) Needed *</label>
                    <input type="number" class="form-control" id="units_needed" name="units_needed" 
                           min="1" max="10" value="<?php echo e($old['units_needed'] ?? '1'); ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="date_needed">Date Needed *</label>
                    <input type="date" class="form-control" id="date_needed" name="date_needed" 
                           min="<?php echo date('Y-m-d'); ?>" value="<?php echo e($old['date_needed'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="urgency">Urgency Level *</label>
                    <select class="form-control" id="urgency" name="urgency" required>
                        <option value="Normal" <?php echo ($old['urgency'] ?? '') === 'Normal' ? 'selected' : ''; ?>>🟢 Normal</option>
                        <option value="Urgent" <?php echo ($old['urgency'] ?? '') === 'Urgent' ? 'selected' : ''; ?>>🟡 Urgent</option>
                        <option value="Critical" <?php echo ($old['urgency'] ?? '') === 'Critical' ? 'selected' : ''; ?>>🔴 Critical</option>
                    </select>
                </div>
            </div>

            <div style="display: flex; gap: 1rem;">
                <button type="submit" class="btn btn-primary btn-lg">Submit Request</button>
                <a href="dashboard.php" class="btn btn-secondary btn-lg">Cancel</a>
            </div>
        </form>
    </div>
<button class="sidebar-toggle" onclick="document.querySelector('.sidebar').classList.toggle('active')">☰</button>
<?php include __DIR__ . '/../includes/footer.php'; ?>
