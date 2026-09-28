<?php
/**
 * SMART QR - Example Configuration Template
 * Copy this file to config.php and update your database credentials.
 *
 * For XAMPP:
 *   DB_HOST = 127.0.0.1
 *   DB_PORT = 3306
 *   DB_NAME = smartqr
 *   DB_USER = root
 *   DB_PASS = 
 */

define('SMARTQR_INIT', true);

define('APP_NAME', 'SMART QR');
define('APP_TAGLINE', 'Scan. Pay. Connect.');
define('APP_ORG', 'ALAG INNOVATIVE SOLUTIONS');
define('APP_VERSION', '1.0.0');

// Database Configuration
define('DB_DRIVER', 'mysql'); // 'mysql' or 'sqlite'
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'smartqr');
define('DB_USER', 'root');
define('DB_PASS', '');

// Optional SQLite path if testing without MySQL server
define('DB_SQLITE_PATH', __DIR__ . '/../smartqr.sqlite');

date_default_timezone_set('Asia/Kolkata');
