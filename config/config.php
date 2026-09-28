<?php
/**
 * SMART QR - Application Configuration
 * Developed by BCA Students | Siddharth Shikshan Avam Prashikshan Sansthan
 * Mentored by Abhishek Chaudhary | ALAG INNOVATIVE SOLUTIONS
 */

// Prevent direct execution outside entry points if needed
defined('SMARTQR_INIT') || define('SMARTQR_INIT', true);

// Application Metadata
define('APP_NAME', 'SMART QR');
define('APP_TAGLINE', 'Scan. Pay. Connect.');
define('APP_ORG', 'ALAG INNOVATIVE SOLUTIONS');
define('APP_VERSION', '1.0.0');

// Base URL calculation (Auto-detects current protocol, host, and script root)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
// Trim any sub-sub directories like /admin or /api to get project root
$rootDir = preg_replace('/(\/(admin|api|config|includes|assets|js))+$/', '', $scriptDir);
$baseUrl = rtrim($protocol . $host . $rootDir, '/');

define('BASE_URL', $baseUrl);

// Database Credentials (Standard XAMPP default: root / empty password)
define('DB_DRIVER', 'mysql'); // 'mysql' (default for XAMPP / production) or 'sqlite'
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'smartqr');
define('DB_USER', 'root');
define('DB_PASS', '');

// Optional SQLite file path for zero-configuration standalone testing
define('DB_SQLITE_PATH', __DIR__ . '/../smartqr.sqlite');

// Session Settings
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    // If running on HTTPS, enable secure cookies
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        ini_set('session.cookie_secure', 1);
    }
    session_name('SMARTQR_SESSION');
    session_start();
}

// Timezone (Indian Standard Time)
date_default_timezone_set('Asia/Kolkata');

// Error reporting settings
// In development, log to server error log instead of displaying raw stack traces to users
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/../error.log');
