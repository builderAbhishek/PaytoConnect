<?php
/**
 * SMART QR - Core Utility Functions
 * Developed by BCA Students | Siddharth Shikshan Avam Prashikshan Sansthan
 * Mentored by Abhishek Chaudhary | ALAG INNOVATIVE SOLUTIONS
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

/**
 * Escape string for safe HTML output (prevents XSS)
 */
function e(?string $value): string {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

/**
 * Sanitize simple user string input
 */
function sanitize(?string $value): string {
    if ($value === null) {
        return '';
    }
    return trim(strip_tags($value));
}

/**
 * Generate or get existing CSRF Token
 */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Render a hidden CSRF input field
 */
function csrf_field(): string {
    $token = csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . e($token) . '">';
}

/**
 * Verify submitted CSRF token
 */
function verify_csrf_token(?string $token): bool {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Clean phone number to 10 digits
 * Handles +91, 0, spaces, and hyphens
 */
function clean_indian_mobile(?string $rawMobile): string {
    if (empty($rawMobile)) {
        return '';
    }
    // Remove all non-digits
    $digits = preg_replace('/\D/', '', $rawMobile);

    // If starts with 91 and has 12 digits, strip 91
    if (strlen($digits) === 12 && str_starts_with($digits, '91')) {
        $digits = substr($digits, 2);
    }
    // If starts with 0 and has 11 digits, strip 0
    if (strlen($digits) === 11 && str_starts_with($digits, '0')) {
        $digits = substr($digits, 1);
    }

    return $digits;
}

/**
 * Validate Indian 10-digit mobile number
 * Must be 10 digits starting with 6, 7, 8, or 9
 */
function is_valid_indian_mobile(?string $mobile): bool {
    $clean = clean_indian_mobile($mobile);
    return (bool)preg_match('/^[6-9]\d{9}$/', $clean);
}

/**
 * Mask mobile number for privacy (e.g., 98XXXXXX21)
 */
function mask_mobile(string $mobile): string {
    $clean = clean_indian_mobile($mobile);
    if (strlen($clean) === 10) {
        return substr($clean, 0, 2) . 'XXXXXX' . substr($clean, 8, 2);
    }
    return $clean;
}

/**
 * Format Indian Rupee currency
 */
function format_currency(float|int|string $amount): string {
    $num = (float)$amount;
    return '₹' . number_format($num, 2, '.', ',');
}

/**
 * Generate UPI Deep Link
 * Standard UPI intent scheme:
 * upi://pay?pa=UPI_ID&pn=RESTAURANT_NAME&am=AMOUNT&cu=INR&tn=NOTE
 */
function build_upi_intent(string $upiId, string $restaurantName, float $amount, string $note = 'Restaurant Bill'): string {
    $params = [
        'pa' => trim($upiId),
        'pn' => trim($restaurantName),
        'am' => number_format($amount, 2, '.', ''),
        'cu' => 'INR',
        'tn' => trim($note)
    ];

    $query = http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    return 'upi://pay?' . $query;
}

/**
 * Send JSON response and exit
 */
function json_response(array $data, int $statusCode = 200): void {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/**
 * Fetch restaurant by its unique URL slug
 */
function get_restaurant_by_slug(string $slug): ?array {
    $slug = trim(strtolower($slug));
    if (empty($slug)) {
        return null;
    }
    try {
        $db = get_db();
        $stmt = $db->prepare("SELECT * FROM restaurants WHERE slug = ? LIMIT 1");
        $stmt->execute([$slug]);
        $row = $stmt->fetch();
        return $row ?: null;
    } catch (PDOException $e) {
        error_log("Error in get_restaurant_by_slug: " . $e->getMessage());
        return null;
    }
}

/**
 * Fetch restaurant by ID
 */
function get_restaurant_by_id(int $id): ?array {
    try {
        $db = get_db();
        $stmt = $db->prepare("SELECT * FROM restaurants WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    } catch (PDOException $e) {
        error_log("Error in get_restaurant_by_id: " . $e->getMessage());
        return null;
    }
}

/**
 * Format relative time (e.g. '2 mins ago', 'Today at 3:15 PM')
 */
function format_relative_time(string $datetime): string {
    $time = strtotime($datetime);
    if (!$time) {
        return $datetime;
    }
    $diff = time() - $time;

    if ($diff < 60) {
        return 'Just now';
    }
    if ($diff < 3600) {
        $m = floor($diff / 60);
        return $m . ($m > 1 ? ' mins' : ' min') . ' ago';
    }
    if ($diff < 86400 && date('Y-m-d', $time) === date('Y-m-d')) {
        return 'Today at ' . date('h:i A', $time);
    }
    if ($diff < 172800 && date('Y-m-d', $time) === date('Y-m-d', strtotime('-1 day'))) {
        return 'Yesterday at ' . date('h:i A', $time);
    }
    return date('d M Y, h:i A', $time);
}

/**
 * Flash messaging system for admin notifications
 */
function set_flash(string $type, string $message): void {
    $_SESSION['flash'] = [
        'type'    => $type, // 'success', 'error', 'info', 'warning'
        'message' => $message,
    ];
}

function get_flash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
