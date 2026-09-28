<?php
/**
 * SMART QR - Admin Authentication & Authorization Guard
 * Developed by BCA Students | Siddharth Shikshan Avam Prashikshan Sansthan
 * Mentored by Abhishek Chaudhary | ALAG INNOVATIVE SOLUTIONS
 *
 * Included at the top of every admin page to enforce access control
 * and multi-tenant restaurant scoping.
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/functions.php';

// Check if admin is authenticated
if (
    empty($_SESSION['admin_logged_in']) ||
    $_SESSION['admin_logged_in'] !== true ||
    empty($_SESSION['admin_id']) ||
    empty($_SESSION['admin_restaurant_id'])
) {
    // Save attempted URL for post-login redirect
    $currentUri = $_SERVER['REQUEST_URI'] ?? '';
    if ($currentUri && !str_contains($currentUri, 'login.php') && !str_contains($currentUri, 'logout.php')) {
        $_SESSION['redirect_after_login'] = $currentUri;
    }

    set_flash('error', 'Please log in to access the restaurant admin portal.');
    header('Location: ' . BASE_URL . '/admin/login.php');
    exit;
}

// Session activity timeout (2 hours)
$sessionLifetime = 7200;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $sessionLifetime)) {
    // Session expired
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }
    session_destroy();
    session_start();
    set_flash('info', 'Your session expired due to inactivity. Please log in again.');
    header('Location: ' . BASE_URL . '/admin/login.php');
    exit;
}
$_SESSION['last_activity'] = time();

// Fetch fresh admin and restaurant details
$currentAdminId = (int)$_SESSION['admin_id'];
$currentRestaurantId = (int)$_SESSION['admin_restaurant_id'];

$currentRestaurant = get_restaurant_by_id($currentRestaurantId);
if (!$currentRestaurant) {
    // Restaurant no longer exists or invalid
    session_destroy();
    header('Location: ' . BASE_URL . '/admin/login.php?error=invalid_restaurant');
    exit;
}

// Current logged in admin details array
$currentAdmin = [
    'id'            => $currentAdminId,
    'name'          => $_SESSION['admin_name'] ?? 'Admin',
    'email'         => $_SESSION['admin_email'] ?? '',
    'restaurant_id' => $currentRestaurantId,
];
