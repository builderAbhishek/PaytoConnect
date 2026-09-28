<?php
/**
 * SMART QR - Admin Header Include
 * Developed by BCA Students | Siddharth Shikshan Avam Prashikshan Sansthan
 * Mentored by Abhishek Chaudhary | ALAG INNOVATIVE SOLUTIONS
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/functions.php';

// Active page identification for navigation highlighting
$currentPage = basename($_SERVER['PHP_SELF'] ?? '');
$pageTitle = $pageTitle ?? 'Admin Portal';
$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= e($pageTitle) ?> | <?= APP_NAME ?> - <?= APP_ORG ?></title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c',
                        },
                        charcoal: {
                            800: '#1e293b',
                            900: '#0f172a',
                        }
                    }
                }
            }
        }
    </script>
    <!-- Google Inter Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        /* Smooth transitions */
        .nav-item { transition: all 0.15s ease-in-out; }
    </style>
</head>
<body class="h-full text-slate-800 antialiased flex flex-col md:flex-row pb-16 md:pb-0">

    <!-- DESKTOP SIDEBAR -->
    <aside class="hidden md:flex md:w-64 lg:w-72 flex-col bg-white border-r border-slate-200 shrink-0 min-h-screen">
        <!-- Brand Header -->
        <div class="p-6 border-b border-slate-100">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-500 to-brand-600 flex items-center justify-center text-white shadow-sm font-black text-lg">
                    SQ
                </div>
                <div>
                    <h1 class="text-lg font-black tracking-tight text-slate-900 leading-tight">SMART QR</h1>
                    <p class="text-[11px] font-semibold tracking-wider text-slate-400 uppercase">ALAG INNOVATIVE</p>
                </div>
            </div>

            <!-- Active Restaurant Badge -->
            <?php if (!empty($currentRestaurant)): ?>
            <div class="mt-4 p-3 bg-slate-50 border border-slate-100 rounded-xl">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Current Outlet</span>
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-100 text-emerald-800">Active</span>
                </div>
                <div class="font-bold text-slate-900 text-sm truncate mt-1" title="<?= e($currentRestaurant['name']) ?>">
                    <?= e($currentRestaurant['name']) ?>
                </div>
                <div class="text-xs text-slate-500 truncate mt-0.5" title="<?= e($currentRestaurant['address']) ?>">
                    <?= e($currentRestaurant['address']) ?>
                </div>
                <a href="<?= BASE_URL ?>/pay.php?slug=<?= urlencode($currentRestaurant['slug']) ?>" target="_blank" class="mt-2 text-xs font-semibold text-brand-600 hover:text-brand-700 flex items-center gap-1">
                    <span>View Customer Pay Page</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
            </div>
            <?php endif; ?>
        </div>

        <!-- Navigation Links -->
        <nav class="p-4 space-y-1.5 flex-1">
            <a href="<?= BASE_URL ?>/admin/dashboard.php" class="nav-item flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm <?= $currentPage === 'dashboard.php' ? 'bg-brand-50 text-brand-600 border border-brand-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' ?>">
                <svg class="w-5 h-5 <?= $currentPage === 'dashboard.php' ? 'text-brand-600' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                <span>Dashboard</span>
            </a>

            <a href="<?= BASE_URL ?>/admin/customers.php" class="nav-item flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm <?= $currentPage === 'customers.php' ? 'bg-brand-50 text-brand-600 border border-brand-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' ?>">
                <svg class="w-5 h-5 <?= $currentPage === 'customers.php' ? 'text-brand-600' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <span>Customers</span>
            </a>

            <a href="<?= BASE_URL ?>/admin/settings.php" class="nav-item flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm <?= $currentPage === 'settings.php' ? 'bg-brand-50 text-brand-600 border border-brand-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' ?>">
                <svg class="w-5 h-5 <?= $currentPage === 'settings.php' ? 'text-brand-600' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span>Outlet Settings &amp; QR</span>
            </a>
        </nav>

        <!-- User / Logout Footer in Sidebar -->
        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2.5 truncate">
                    <div class="w-8 h-8 rounded-full bg-slate-800 text-white flex items-center justify-center font-bold text-xs shrink-0">
                        <?= strtoupper(substr($currentAdmin['name'] ?? 'A', 0, 1)) ?>
                    </div>
                    <div class="truncate">
                        <div class="text-xs font-bold text-slate-800 truncate"><?= e($currentAdmin['name'] ?? 'Admin') ?></div>
                        <div class="text-[11px] text-slate-400 truncate"><?= e($currentAdmin['email'] ?? '') ?></div>
                    </div>
                </div>
                <a href="<?= BASE_URL ?>/admin/logout.php" title="Sign Out" class="text-slate-400 hover:text-red-600 transition p-1.5 rounded-lg hover:bg-red-50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </a>
            </div>
        </div>
    </aside>

    <!-- MOBILE TOP BAR -->
    <header class="md:hidden sticky top-0 z-40 bg-white border-b border-slate-200 px-4 py-3 flex items-center justify-between shadow-xs">
        <div class="flex items-center space-x-2.5">
            <div class="w-8 h-8 rounded-lg bg-brand-600 text-white flex items-center justify-center font-black text-sm">
                SQ
            </div>
            <div>
                <h1 class="text-sm font-bold text-slate-900 leading-tight"><?= e($currentRestaurant['name'] ?? APP_NAME) ?></h1>
                <p class="text-[10px] text-slate-400 font-medium">Counter Admin Portal</p>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <a href="<?= BASE_URL ?>/pay.php?slug=<?= urlencode($currentRestaurant['slug'] ?? 'chaudhary') ?>" target="_blank" class="p-1.5 text-brand-600 bg-brand-50 rounded-lg hover:bg-brand-100" title="Open Pay Page">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
            </a>
            <a href="<?= BASE_URL ?>/admin/logout.php" class="p-1.5 text-slate-400 hover:text-red-600 rounded-lg" title="Logout">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            </a>
        </div>
    </header>

    <!-- MAIN CONTENT WRAPPER -->
    <main class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        <?php if ($flash): ?>
        <div class="p-4 max-w-7xl w-full mx-auto pb-0">
            <div class="flex items-center justify-between p-3.5 rounded-xl border text-sm <?= $flash['type'] === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : ($flash['type'] === 'error' ? 'bg-red-50 border-red-200 text-red-800' : 'bg-blue-50 border-blue-200 text-blue-800') ?>">
                <div class="flex items-center gap-2.5">
                    <?php if ($flash['type'] === 'success'): ?>
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <?php else: ?>
                    <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <?php endif; ?>
                    <span><?= e($flash['message']) ?></span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-600">&times;</button>
            </div>
        </div>
        <?php endif; ?>
