<?php
/**
 * SMART QR - Database Connection Layer (PDO)
 * Developed by BCA Students | Siddharth Shikshan Avam Prashikshan Sansthan
 * Mentored by Abhishek Chaudhary | ALAG INNOVATIVE SOLUTIONS
 *
 * Implements secure PDO connection with prepared statements,
 * error handling, and support for MySQL (XAMPP default) with
 * automatic SQLite fallback for zero-configuration testing.
 */

require_once __DIR__ . '/config.php';

class Database {
    private static ?PDO $instance = null;
    private static string $activeDriver = 'mysql';

    /**
     * Get the active PDO Database Connection
     */
    public static function getConnection(): PDO {
        if (self::$instance === null) {
            self::connect();
        }
        return self::$instance;
    }

    /**
     * Check which driver is currently active ('mysql' or 'sqlite')
     */
    public static function getActiveDriver(): string {
        return self::$activeDriver;
    }

    /**
     * Establish the database connection
     */
    private static function connect(): void {
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        // 1. Primary Attempt: MySQL (Production & XAMPP)
        if (DB_DRIVER === 'mysql') {
            try {
                $dsn = sprintf(
                    'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
                    DB_HOST,
                    DB_PORT,
                    DB_NAME
                );
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
                self::$activeDriver = 'mysql';
                return;
            } catch (PDOException $e) {
                error_log("SMART QR: MySQL connection failed: " . $e->getMessage());

                // If SQLite fallback file exists, use it so the app remains testable
                if (file_exists(DB_SQLITE_PATH)) {
                    try {
                        self::$instance = new PDO('sqlite:' . DB_SQLITE_PATH, null, null, $options);
                        self::$instance->exec('PRAGMA foreign_keys = ON;');
                        self::$activeDriver = 'sqlite';
                        return;
                    } catch (PDOException $sqle) {
                        error_log("SMART QR: SQLite fallback failed: " . $sqle->getMessage());
                    }
                }

                // If no fallback was possible, show clean friendly error
                self::renderConnectionError($e->getMessage());
                exit;
            }
        }

        // 2. Direct SQLite Mode (if explicitly configured)
        if (DB_DRIVER === 'sqlite') {
            try {
                self::$instance = new PDO('sqlite:' . DB_SQLITE_PATH, null, null, $options);
                self::$instance->exec('PRAGMA foreign_keys = ON;');
                self::$activeDriver = 'sqlite';
                return;
            } catch (PDOException $e) {
                error_log("SMART QR: SQLite direct connection failed: " . $e->getMessage());
                self::renderConnectionError($e->getMessage());
                exit;
            }
        }
    }

    /**
     * Friendly error page when DB cannot be reached
     */
    private static function renderConnectionError(string $internalMsg): void {
        // Log the detailed error on the server
        error_log("Database Connection Error: " . $internalMsg);

        // If this is an API call, return JSON
        if (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')) {
            http_response_code(500);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false,
                'error'   => 'Database connection could not be established. Please check database settings or start MySQL in XAMPP.',
            ]);
            exit;
        }

        // Otherwise return a friendly HTML page
        http_response_code(500);
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Database Connection Notice - SMART QR</title>
            <script src="https://cdn.tailwindcss.com"></script>
        </head>
        <body class="bg-gray-50 flex items-center justify-center min-h-screen p-4 font-sans text-gray-800">
            <div class="max-w-md w-full bg-white rounded-2xl shadow-lg border border-gray-100 p-6 md:p-8 text-center">
                <div class="w-14 h-14 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h1 class="text-xl font-bold text-gray-900 mb-2">Database Connection Required</h1>
                <p class="text-sm text-gray-600 mb-6 leading-relaxed">
                    SMART QR is unable to connect to the MySQL database. If you are running locally via <strong>XAMPP</strong>, please start Apache &amp; MySQL in the XAMPP Control Panel and import <code class="bg-gray-100 px-1 py-0.5 rounded text-orange-600 font-mono">database.sql</code>.
                </p>
                <div class="bg-gray-50 rounded-xl p-4 text-left text-xs text-gray-600 border border-gray-200 mb-6 space-y-1">
                    <p class="font-semibold text-gray-700">Quick Setup Instructions:</p>
                    <p>1. Open <strong>XAMPP Control Panel</strong> and click <strong>Start</strong> next to MySQL.</p>
                    <p>2. Open <a href="http://localhost/phpmyadmin" class="text-orange-600 underline" target="_blank">phpMyAdmin</a> and create a database named <span class="font-mono font-bold">smartqr</span>.</p>
                    <p>3. Import the file <span class="font-mono font-bold">database.sql</span>.</p>
                    <p>4. Or run <code class="font-mono text-gray-800">php setup_db.php</code> in terminal for instant initialization.</p>
                </div>
                <a href="javascript:location.reload()" class="inline-block w-full py-2.5 px-4 bg-orange-600 hover:bg-orange-700 text-white font-medium rounded-xl transition shadow">
                    Retry Connection
                </a>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}

/**
 * Convenience procedural helper for clean student-friendly code
 */
function get_db(): PDO {
    return Database::getConnection();
}
