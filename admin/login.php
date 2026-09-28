<?php
/**
 * SMART QR - Admin Authentication Portal
 * Developed by BCA Students | Siddharth Shikshan Avam Prashikshan Sansthan
 * Mentored by Abhishek Chaudhary | ALAG INNOVATIVE SOLUTIONS
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

// If already logged in, redirect to dashboard
if (!empty($_SESSION['admin_logged_in']) && !empty($_SESSION['admin_restaurant_id'])) {
    header('Location: ' . BASE_URL . '/admin/dashboard.php');
    exit;
}

$error = '';
$flash = get_flash();

// Handle standard non-AJAX POST fallback
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($csrfToken)) {
        $error = 'Security session expired. Please try submitting again.';
    } else {
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $password = trim($_POST['password'] ?? '');

        if (!$email || empty($password)) {
            $error = 'Please enter both valid email and password.';
        } else {
            try {
                $db = get_db();
                $stmt = $db->prepare("SELECT * FROM admins WHERE email = ? LIMIT 1");
                $stmt->execute([$email]);
                $admin = $stmt->fetch();

                if ($admin && password_verify($password, $admin['password'])) {
                    session_regenerate_id(true);
                    $_SESSION['admin_logged_in']     = true;
                    $_SESSION['admin_id']            = (int)$admin['id'];
                    $_SESSION['admin_name']          = $admin['name'];
                    $_SESSION['admin_email']         = $admin['email'];
                    $_SESSION['admin_restaurant_id'] = (int)$admin['restaurant_id'];
                    $_SESSION['last_activity']       = time();

                    $dest = $_SESSION['redirect_after_login'] ?? (BASE_URL . '/admin/dashboard.php');
                    unset($_SESSION['redirect_after_login']);
                    header('Location: ' . $dest);
                    exit;
                } else {
                    $error = 'Invalid email or password. Please verify your credentials.';
                }
            } catch (PDOException $e) {
                error_log("Login error: " . $e->getMessage());
                $error = 'Unable to connect to database. Please check database connection.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Admin Login | <?= APP_NAME ?> - <?= APP_ORG ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#fff7ed',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c',
                        }
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="h-full flex flex-col justify-center py-10 px-4 sm:px-6 lg:px-8 text-slate-800">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <!-- Brand Emblem -->
        <div class="flex justify-center mb-3">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-brand-600 to-amber-500 flex items-center justify-center text-white shadow-md font-black text-2xl">
                SQ
            </div>
        </div>
        <h2 class="text-center text-2xl font-black tracking-tight text-slate-900">
            Restaurant Admin Portal
        </h2>
        <p class="mt-1 text-center text-xs font-semibold text-slate-500 uppercase tracking-widest">
            <?= APP_ORG ?>
        </p>
    </div>

    <div class="mt-6 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-6 shadow-sm border border-slate-200 rounded-2xl sm:px-10">

            <!-- Flash & Error Alert Container -->
            <div id="loginAlert" class="<?= empty($error) && empty($flash) ? 'hidden' : '' ?> mb-5 p-3.5 rounded-xl border text-sm <?= !empty($flash) && $flash['type'] === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-red-50 border-red-200 text-red-800' ?>">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span id="loginAlertMsg"><?= e($error ?: ($flash['message'] ?? '')) ?></span>
                </div>
            </div>

            <!-- Login Form -->
            <form id="loginForm" method="POST" action="login.php" class="space-y-4">
                <?= csrf_field() ?>

                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Admin Email
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"/></svg>
                        </div>
                        <input type="email" id="email" name="email" required autocomplete="email" placeholder="e.g. chaudhary@smartqr.local"
                               class="w-full pl-10 pr-3.5 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-sm font-medium transition">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <input type="password" id="password" name="password" required autocomplete="current-password" placeholder="••••••••"
                               class="w-full pl-10 pr-3.5 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-sm font-medium transition">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" id="loginBtn"
                            class="w-full py-3.5 px-4 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl text-sm transition shadow-sm flex items-center justify-center gap-2">
                        <svg id="loginSpinner" class="hidden animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span id="loginBtnText">Sign In to Dashboard &rarr;</span>
                    </button>
                </div>
            </form>

            <!-- Quick Demo Credentials Box for Testing -->
            <div class="mt-6 pt-5 border-t border-slate-100">
                <div class="rounded-xl bg-amber-50/80 border border-amber-200/80 p-3.5 text-xs text-amber-900">
                    <div class="flex items-center gap-1.5 font-bold mb-1 text-amber-950">
                        <svg class="w-4 h-4 text-amber-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Demo Admin Credentials (XAMPP / Local)</span>
                    </div>
                    <div class="space-y-1 font-mono text-[11px] text-amber-800">
                        <div>Outlet 1: <strong class="text-amber-950">chaudhary@smartqr.local</strong></div>
                        <div>Password: <strong class="text-amber-950">admin123</strong></div>
                    </div>
                    <button type="button" onclick="document.getElementById('email').value='chaudhary@smartqr.local';document.getElementById('password').value='admin123';"
                            class="mt-2 text-[11px] font-bold text-brand-700 hover:text-brand-900 underline block">
                        Auto-fill Chaudhary Cafe Credentials
                    </button>
                </div>
            </div>

            <!-- Back to Public Site -->
            <div class="mt-5 text-center">
                <a href="<?= BASE_URL ?>/" class="text-xs font-semibold text-slate-500 hover:text-slate-800 transition inline-flex items-center gap-1">
                    &larr; Return to Product Homepage
                </a>
            </div>

        </div>

        <!-- Student Attribution -->
        <p class="mt-6 text-center text-xs text-slate-400">
            Siddharth Shikshan Avam Prashikshan Sansthan &bull; BCA Project
        </p>
    </div>

    <!-- JS Helper -->
    <script src="../js/auth.js"></script>
</body>
</html>
