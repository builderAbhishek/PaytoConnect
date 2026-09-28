<?php
/**
 * SMART QR - Database Installer & Migration Tool
 * Developed by BCA Students | Siddharth Shikshan Avam Prashikshan Sansthan
 * Mentored by Abhishek Chaudhary | ALAG INNOVATIVE SOLUTIONS
 *
 * Can be run via CLI: php setup_db.php
 * Or accessed via Browser during initial setup.
 */

require_once __DIR__ . '/config/config.php';

echo "========================================================\n";
echo " SMART QR - Database Setup & Seeding Tool\n";
echo " ALAG INNOVATIVE SOLUTIONS\n";
echo " Mentor: Abhishek Chaudhary\n";
echo "========================================================\n\n";

$isCli = (php_sapi_name() === 'cli');

// Demo Password Hash for 'admin123'
$demoPasswordHash = '$2y$10$XNFvGYducFGhyJqkn6eAwOFIS.ONUQRdprHI2/komfw6fBlUag3xS';

// Try MySQL first
$mysqlConnected = false;
$pdo = null;

try {
    $dsnNoDb = sprintf('mysql:host=%s;port=%s;charset=utf8mb4', DB_HOST, DB_PORT);
    $rootPdo = new PDO($dsnNoDb, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    
    echo "[*] Connected to MySQL Server at " . DB_HOST . ":" . DB_PORT . "\n";
    $rootPdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    echo "[*] Database `" . DB_NAME . "` created or already exists.\n";

    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_PORT, DB_NAME);
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $mysqlConnected = true;
    echo "[+] Using MySQL database engine.\n";
} catch (PDOException $e) {
    echo "[-] MySQL connection could not be established (" . $e->getMessage() . ").\n";
    echo "[*] Initializing SQLite standalone database for zero-config testing: " . DB_SQLITE_PATH . "\n";
    
    try {
        $pdo = new PDO('sqlite:' . DB_SQLITE_PATH, null, null, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        $pdo->exec('PRAGMA foreign_keys = ON;');
        echo "[+] Using SQLite database engine.\n";
    } catch (PDOException $se) {
        die("[!] Fatal error: Could not initialize SQLite: " . $se->getMessage() . "\n");
    }
}

// Create Schema
echo "[*] Creating tables...\n";

if ($mysqlConnected) {
    // MySQL DDL
    $pdo->exec("DROP TABLE IF EXISTS customers;");
    $pdo->exec("DROP TABLE IF EXISTS admins;");
    $pdo->exec("DROP TABLE IF EXISTS restaurants;");

    $pdo->exec("CREATE TABLE restaurants (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(150) NOT NULL,
        slug VARCHAR(100) NOT NULL UNIQUE,
        logo VARCHAR(255) NULL,
        address TEXT NOT NULL,
        phone VARCHAR(20) NOT NULL,
        upi_id VARCHAR(100) NOT NULL,
        description TEXT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_restaurant_slug (slug)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    $pdo->exec("CREATE TABLE admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        restaurant_id INT NOT NULL,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(150) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_admin_restaurant (restaurant_id),
        CONSTRAINT fk_admins_restaurant FOREIGN KEY (restaurant_id) REFERENCES restaurants (id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    $pdo->exec("CREATE TABLE customers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        restaurant_id INT NOT NULL,
        name VARCHAR(100) NOT NULL,
        mobile VARCHAR(15) NOT NULL,
        customer_type ENUM('Local', 'Outsider') NOT NULL DEFAULT 'Local',
        bill_amount DECIMAL(10, 2) NOT NULL,
        marketing_consent TINYINT(1) NOT NULL DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_customer_restaurant (restaurant_id),
        INDEX idx_customer_mobile (mobile),
        INDEX idx_customer_created_at (created_at),
        INDEX idx_customer_type (customer_type),
        CONSTRAINT fk_customers_restaurant FOREIGN KEY (restaurant_id) REFERENCES restaurants (id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

} else {
    // SQLite DDL
    $pdo->exec("DROP TABLE IF EXISTS customers;");
    $pdo->exec("DROP TABLE IF EXISTS admins;");
    $pdo->exec("DROP TABLE IF EXISTS restaurants;");

    $pdo->exec("CREATE TABLE restaurants (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        slug TEXT NOT NULL UNIQUE,
        logo TEXT NULL,
        address TEXT NOT NULL,
        phone TEXT NOT NULL,
        upi_id TEXT NOT NULL,
        description TEXT NULL,
        created_at TEXT DEFAULT (datetime('now', 'localtime')),
        updated_at TEXT DEFAULT (datetime('now', 'localtime'))
    );");

    $pdo->exec("CREATE INDEX idx_restaurant_slug ON restaurants(slug);");

    $pdo->exec("CREATE TABLE admins (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        restaurant_id INTEGER NOT NULL,
        name TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL,
        created_at TEXT DEFAULT (datetime('now', 'localtime')),
        FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE
    );");

    $pdo->exec("CREATE INDEX idx_admin_restaurant ON admins(restaurant_id);");

    $pdo->exec("CREATE TABLE customers (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        restaurant_id INTEGER NOT NULL,
        name TEXT NOT NULL,
        mobile TEXT NOT NULL,
        customer_type TEXT NOT NULL DEFAULT 'Local',
        bill_amount REAL NOT NULL,
        marketing_consent INTEGER NOT NULL DEFAULT 0,
        created_at TEXT DEFAULT (datetime('now', 'localtime')),
        FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE
    );");

    $pdo->exec("CREATE INDEX idx_customer_restaurant ON customers(restaurant_id);");
    $pdo->exec("CREATE INDEX idx_customer_mobile ON customers(mobile);");
    $pdo->exec("CREATE INDEX idx_customer_created_at ON customers(created_at);");
}

echo "[+] Database schema initialized successfully.\n";

// Seed Restaurants
echo "[*] Seeding restaurants...\n";
$stmtRest = $pdo->prepare("INSERT INTO restaurants (id, name, slug, logo, address, phone, upi_id, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmtRest->execute([
    1,
    'Chaudhary Cafe & Family Restaurant',
    'chaudhary',
    'assets/images/chaudhary-logo.svg',
    'Gadakul, Shohratgarh, Siddharthnagar',
    '9137470661',
    '9137470661@superyes',
    'Fresh delicious fast food, meals, coffee, tea & snacks for families and highway travelers.'
]);
$stmtRest->execute([
    2,
    'Royal Cafe & Bakers',
    'royal',
    'assets/images/royal-logo.svg',
    'Station Road, Naugarh, Siddharthnagar',
    '9876543210',
    'royalcafe@okaxis',
    'Finest bakery, cakes, pizzas and family dine-in in Naugarh.'
]);

// Seed Admins
echo "[*] Seeding admin accounts...\n";
$stmtAdmin = $pdo->prepare("INSERT INTO admins (id, restaurant_id, name, email, password) VALUES (?, ?, ?, ?, ?)");
$stmtAdmin->execute([
    1,
    1,
    'Abhishek Chaudhary',
    'chaudhary@smartqr.local',
    $demoPasswordHash
]);
$stmtAdmin->execute([
    2,
    2,
    'Rohan Verma',
    'royal@smartqr.local',
    $demoPasswordHash
]);

// Seed Sample Customers for Chaudhary Cafe (Restaurant ID = 1)
echo "[*] Seeding sample customer transactions for Chaudhary Cafe...\n";
$sampleCustomers = [
    ['Rahul Sharma', '9812345621', 'Local', 480.00, 1, '-25 minutes'],
    ['Aman Mishra', '9723456742', 'Outsider', 720.00, 1, '-55 minutes'],
    ['Priya Singh', '9634567890', 'Local', 350.00, 0, '-2 hours'],
    ['Vikram Gupta', '9545678901', 'Outsider', 1250.00, 1, '-3 hours'],
    ['Suresh Patel', '9456789012', 'Local', 220.00, 1, '-4 hours'],
    ['Neha Tiwari', '9367890123', 'Local', 640.00, 0, '-5 hours'],
    ['Deepak Verma', '9278901234', 'Outsider', 890.00, 1, '-7 hours'],
    ['Anjali Dubey', '9189012345', 'Local', 530.00, 1, '-1 day'],
    ['Mohit Srivastava', '9090123456', 'Outsider', 1100.00, 0, '-1 day'],
    ['Sunil Yadav', '9891234567', 'Local', 410.00, 1, '-2 days'],
    ['Kavita Pandey', '9782345678', 'Local', 320.00, 1, '-3 days'],
    ['Rajesh Jaiswal', '9673456789', 'Outsider', 1450.00, 0, '-4 days'],
    ['Alok Maurya', '9564567890', 'Local', 290.00, 1, '-5 days'],
    ['Pooja Chaurasia', '9455678901', 'Local', 680.00, 1, '-6 days'],
    ['Vivek Tripathi', '9346789012', 'Outsider', 950.00, 1, '-7 days'],
];

$stmtCust = $pdo->prepare("INSERT INTO customers (restaurant_id, name, mobile, customer_type, bill_amount, marketing_consent, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
foreach ($sampleCustomers as $c) {
    $createdAt = date('Y-m-d H:i:s', strtotime($c[5]));
    $stmtCust->execute([1, $c[0], $c[1], $c[2], $c[3], $c[4], $createdAt]);
}

// Seed Customers for Royal Cafe (Restaurant ID = 2)
$stmtCust->execute([2, 'Gaurav Sen', '9811122233', 'Local', 550.00, 1, date('Y-m-d H:i:s', strtotime('-1 hour'))]);
$stmtCust->execute([2, 'Manish Saxena', '9822233344', 'Outsider', 1300.00, 0, date('Y-m-d H:i:s', strtotime('-3 hours'))]);

echo "[+] Seeding complete!\n\n";
echo "--------------------------------------------------------\n";
echo "DEMO CREDENTIALS:\n";
echo "Outlet: Chaudhary Cafe & Family Restaurant\n";
echo "Slug:   chaudhary\n";
echo "URL:    pay.php?slug=chaudhary\n";
echo "Admin:  chaudhary@smartqr.local\n";
echo "Pass:   admin123\n";
echo "--------------------------------------------------------\n";

if (!$isCli) {
    echo "<p style='color:green; font-weight:bold;'>Database setup successfully completed! <a href='admin/login.php'>Go to Admin Login</a> | <a href='pay.php?slug=chaudhary'>Go to Pay Page</a></p>";
}
