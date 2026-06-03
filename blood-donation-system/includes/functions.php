<?php
/**
 * Utility Functions
 * Common helper functions used throughout the application
 */

require_once __DIR__ . '/../config/db.php';

/**
 * Sanitize output to prevent XSS
 * @param string $data
 * @return string
 */
function e($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * Sanitize input data
 * @param string $data
 * @return string
 */
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Redirect to a URL
 * @param string $url
 */
function redirect($url) {
    // If it's already an absolute URL, use as-is
    if (strpos($url, 'http') === 0 || strpos($url, '/') === 0) {
        header("Location: $url");
        exit();
    }
    
    // Build base path from current script location
    $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
    $basePath = str_replace('\\', '/', $scriptDir);
    // Remove /user, /admin subfolders to get project root
    $basePath = preg_replace('#/(user|admin)$#', '', $basePath);
    // Ensure trailing slash
    $basePath = rtrim($basePath, '/') . '/';
    
    header("Location: " . $basePath . $url);
    exit();
}

/**
 * Set flash message
 * @param string $type 'success' or 'error'
 * @param string $message
 */
function setFlash($type, $message) {
    $_SESSION['flash_' . $type] = $message;
}

/**
 * Get and clear flash message
 * @param string $type 'success' or 'error'
 * @return string|null
 */
function getFlash($type) {
    $key = 'flash_' . $type;
    if (isset($_SESSION[$key])) {
        $msg = $_SESSION[$key];
        unset($_SESSION[$key]);
        return $msg;
    }
    return null;
}

/**
 * Display flash messages HTML
 * @return string HTML
 */
function displayFlashMessages() {
    $html = '';
    
    $success = getFlash('success');
    if ($success) {
        $html .= '<div class="alert alert-success" id="flash-success">';
        $html .= '<span class="alert-icon">✓</span>';
        $html .= '<span>' . e($success) . '</span>';
        $html .= '<button class="alert-close" onclick="this.parentElement.remove()">×</button>';
        $html .= '</div>';
    }
    
    $error = getFlash('error');
    if ($error) {
        $html .= '<div class="alert alert-error" id="flash-error">';
        $html .= '<span class="alert-icon">✕</span>';
        $html .= '<span>' . e($error) . '</span>';
        $html .= '<button class="alert-close" onclick="this.parentElement.remove()">×</button>';
        $html .= '</div>';
    }
    
    return $html;
}

/**
 * Generate CSRF token
 * @return string
 */
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 * @param string $token
 * @return bool
 */
function verifyCSRFToken($token) {
    if (isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token)) {
        unset($_SESSION['csrf_token']); // Invalidate after use
        return true;
    }
    return false;
}

/**
 * CSRF hidden input field
 * @return string HTML
 */
function csrfField() {
    return '<input type="hidden" name="csrf_token" value="' . generateCSRFToken() . '">';
}

/**
 * Get count from database
 * @param string $table
 * @param string $condition
 * @param array $params
 * @return int
 */
function getCount($table, $condition = '', $params = []) {
    $db = getDB();
    $sql = "SELECT COUNT(*) as count FROM $table";
    if ($condition) {
        $sql .= " WHERE $condition";
    }
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return (int) $stmt->fetch()['count'];
}

/**
 * Check if donation is eligible (90-day rule)
 * @param string|null $lastDonationDate
 * @return array ['eligible' => bool, 'days_remaining' => int]
 */
function checkDonationEligibility($lastDonationDate) {
    if ($lastDonationDate === null) {
        return ['eligible' => true, 'days_remaining' => 0];
    }
    
    $lastDate = new DateTime($lastDonationDate);
    $today = new DateTime();
    $diff = $today->diff($lastDate)->days;
    
    if ($diff >= 90) {
        return ['eligible' => true, 'days_remaining' => 0];
    }
    
    return ['eligible' => false, 'days_remaining' => 90 - $diff];
}

/**
 * Get all Bangladesh districts
 * @return array
 */
function getDistricts() {
    return [
        'Bagerhat', 'Bandarban', 'Barguna', 'Barishal', 'Bhola', 'Bogra',
        'Brahmanbaria', 'Chandpur', 'Chapainawabganj', 'Chittagong', 'Chuadanga',
        'Comilla', 'Cox\'s Bazar', 'Dhaka', 'Dinajpur', 'Faridpur', 'Feni',
        'Gaibandha', 'Gazipur', 'Gopalganj', 'Habiganj', 'Jamalpur', 'Jessore',
        'Jhalokati', 'Jhenaidah', 'Joypurhat', 'Khagrachhari', 'Khulna', 'Kishoreganj',
        'Kurigram', 'Kushtia', 'Lakshmipur', 'Lalmonirhat', 'Madaripur', 'Magura',
        'Manikganj', 'Meherpur', 'Moulvibazar', 'Munshiganj', 'Mymensingh', 'Naogaon',
        'Narail', 'Narayanganj', 'Narsingdi', 'Natore', 'Nawabganj', 'Netrokona',
        'Nilphamari', 'Noakhali', 'Pabna', 'Panchagarh', 'Patuakhali', 'Pirojpur',
        'Rajbari', 'Rajshahi', 'Rangamati', 'Rangpur', 'Satkhira', 'Shariatpur',
        'Sherpur', 'Sirajganj', 'Sunamganj', 'Sylhet', 'Tangail', 'Thakurgaon'
    ];
}

/**
 * Format date nicely
 * @param string $date
 * @return string
 */
function formatDate($date) {
    if (!$date) return 'N/A';
    return date('d M Y', strtotime($date));
}

/**
 * Get urgency badge class
 * @param string $urgency
 * @return string
 */
function getUrgencyClass($urgency) {
    switch ($urgency) {
        case 'Critical': return 'badge-critical';
        case 'Urgent': return 'badge-urgent';
        default: return 'badge-normal';
    }
}

/**
 * Get status badge class
 * @param string $status
 * @return string
 */
function getStatusClass($status) {
    switch ($status) {
        case 'Approved': return 'badge-approved';
        case 'Rejected': return 'badge-rejected';
        case 'Fulfilled': return 'badge-fulfilled';
        case 'Completed': return 'badge-fulfilled';
        case 'Pledged': return 'badge-pending';
        default: return 'badge-pending';
    }
}

/**
 * Get base URL dynamically
 * @return string
 */
function getBaseURL() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $script = dirname($_SERVER['SCRIPT_NAME']);
    // Find the root of the project
    $base = str_replace('\\', '/', $script);
    // Remove /user, /admin, /includes etc from the path
    $base = preg_replace('#/(user|admin|includes|config|assets).*$#', '', $base);
    return $protocol . '://' . $host . $base;
}
