<?php
/**
 * SMART QR - Admin Authentication API
 * Developed by BCA Students | Siddharth Shikshan Avam Prashikshan Sansthan
 * Mentored by Abhishek Chaudhary | ALAG INNOVATIVE SOLUTIONS
 *
 * POST /api/auth.php
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'error' => 'Method not allowed.'], 405);
}

// Support JSON or Form POST
$rawInput = file_get_contents('php://input');
$jsonData = json_decode($rawInput, true);
$data = is_array($jsonData) ? $jsonData : $_POST;

$email = filter_var(trim($data['email'] ?? ''), FILTER_VALIDATE_EMAIL);
$password = trim($data['password'] ?? '');
$csrfToken = $data['csrf_token'] ?? '';

// 1. Verify CSRF Token
if (!verify_csrf_token($csrfToken)) {
    json_response(['success' => false, 'error' => 'Security token mismatch. Please refresh the page.'], 403);
}

// 2. Validate Inputs
if (!$email || empty($password)) {
    json_response(['success' => false, 'error' => 'Please provide both valid email and password.'], 422);
}

// 3. Brute force check (Session-based)
$_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0);
if ($_SESSION['login_attempts'] >= 7) {
    if (isset($_SESSION['lockout_time']) && time() < $_SESSION['lockout_time']) {
        $remaining = $_SESSION['lockout_time'] - time();
        json_response([
            'success' => false,
            'error'   => "Too many failed attempts. Please wait {$remaining} seconds before trying again."
        ], 429);
    } else {
        // Reset after lockout window passes
        $_SESSION['login_attempts'] = 0;
        unset($_SESSION['lockout_time']);
    }
}

try {
    $db = get_db();
    $stmt = $db->prepare("SELECT * FROM admins WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        // Successful login
        session_regenerate_id(true);

        $_SESSION['admin_logged_in']      = true;
        $_SESSION['admin_id']             = (int)$admin['id'];
        $_SESSION['admin_name']           = $admin['name'];
        $_SESSION['admin_email']          = $admin['email'];
        $_SESSION['admin_restaurant_id']  = (int)$admin['restaurant_id'];
        $_SESSION['last_activity']        = time();
        $_SESSION['login_attempts']       = 0;
        unset($_SESSION['lockout_time']);

        $redirectUrl = $_SESSION['redirect_after_login'] ?? (BASE_URL . '/admin/dashboard.php');
        unset($_SESSION['redirect_after_login']);

        json_response([
            'success'  => true,
            'message'  => 'Authentication successful.',
            'redirect' => $redirectUrl,
        ]);
    } else {
        // Failed attempt
        $_SESSION['login_attempts']++;
        if ($_SESSION['login_attempts'] >= 7) {
            $_SESSION['lockout_time'] = time() + 300; // 5 min lockout
        }

        json_response([
            'success' => false,
            'error'   => 'Invalid email or password. Please check your credentials.'
        ], 401);
    }

} catch (PDOException $e) {
    error_log("Login error in api/auth.php: " . $e->getMessage());
    json_response(['success' => false, 'error' => 'Internal server error. Please try again later.'], 500);
}
