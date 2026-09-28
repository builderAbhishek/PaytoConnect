<?php
/**
 * SMART QR - Restaurant Admin Dashboard
 * Developed by BCA Students | Siddharth Shikshan Avam Prashikshan Sansthan
 * Mentored by Abhishek Chaudhary | ALAG INNOVATIVE SOLUTIONS
 */

$pageTitle = 'Dashboard Overview';
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/header.php';

// Scoped directly to current logged-in restaurant
$restaurantId = (int)$currentRestaurant['id'];

// Date range for today in IST
$todayStart = date('Y-m-d 00:00:00');
$todayEnd   = date('Y-m-d 23:59:59');

$db = get_db();

// Metrics queries
// 1. Today's Total Customers
$stmt = $db->prepare("SELECT COUNT(*) AS total_today FROM customers WHERE restaurant_id = ? AND created_at BETWEEN ? AND ?");
$stmt->execute([$restaurantId, $todayStart, $todayEnd]);
$todayCustomers = (int)($stmt->fetch()['total_today'] ?? 0);

// 2. Today's Local Customers
$stmt = $db->prepare("SELECT COUNT(*) AS local_today FROM customers WHERE restaurant_id = ? AND customer_type = 'Local' AND created_at BETWEEN ? AND ?");
$stmt->execute([$restaurantId, $todayStart, $todayEnd]);
$todayLocal = (int)($stmt->fetch()['local_today'] ?? 0);

// 3. Today's Outsiders
$stmt = $db->prepare("SELECT COUNT(*) AS outsider_today FROM customers WHERE restaurant_id = ? AND customer_type = 'Outsider' AND created_at BETWEEN ? AND ?");
$stmt->execute([$restaurantId, $todayStart, $todayEnd]);
$todayOutsiders = (int)($stmt->fetch()['outsider_today'] ?? 0);

// 4. Today's Total Bill Amount
$stmt = $db->prepare("SELECT COALESCE(SUM(bill_amount), 0) AS bill_today FROM customers WHERE restaurant_id = ? AND created_at BETWEEN ? AND ?");
$stmt->execute([$restaurantId, $todayStart, $todayEnd]);
$todayBillAmount = (float)($stmt->fetch()['bill_today'] ?? 0);

// 5. Total Customers All Time
$stmt = $db->prepare("SELECT COUNT(*) AS all_time FROM customers WHERE restaurant_id = ?");
$stmt->execute([$restaurantId]);
$totalCustomers = (int)($stmt->fetch()['all_time'] ?? 0);

// 6. Marketing Consent
$stmt = $db->prepare("SELECT COUNT(*) AS consent_count FROM customers WHERE restaurant_id = ? AND marketing_consent = 1");
$stmt->execute([$restaurantId]);
$consentCount = (int)($stmt->fetch()['consent_count'] ?? 0);
$consentPct = $totalCustomers > 0 ? round(($consentCount / $totalCustomers) * 100, 1) : 0;

// 7. Recent Customers (Last 10)
$stmt = $db->prepare("SELECT * FROM customers WHERE restaurant_id = ? ORDER BY created_at DESC LIMIT 10");
$stmt->execute([$restaurantId]);
$recentCustomers = $stmt->fetchAll();

// Public pay page URL for this restaurant
$payUrl = BASE_URL . '/pay.php?slug=' . urlencode($currentRestaurant['slug']);
?>

<div class="p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto space-y-6">

    <!-- Top Bar: Welcome & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                    <?= e($currentRestaurant['name']) ?>
                </h1>
            </div>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span><?= e($currentRestaurant['address']) ?></span>
                <span class="text-slate-300">&bull;</span>
                <span class="font-medium text-slate-700">UPI: <?= e($currentRestaurant['upi_id']) ?></span>
            </p>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <!-- Manual Refresh Button -->
            <button id="refreshMetricsBtn" title="Refresh Live Numbers" class="p-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </button>

            <!-- Quick Link to Pay Page -->
            <a href="<?= e($payUrl) ?>" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs sm:text-sm font-bold shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                <span>Open Pay Page</span>
            </a>
        </div>
    </div>

    <!-- KPI Metric Cards Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3 sm:gap-4">
        
        <!-- Card 1: Today's Customers -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Today's Guests</span>
                <span class="p-1.5 rounded-lg bg-orange-50 text-brand-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </span>
            </div>
            <div>
                <div id="metricTodayCustomers" class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                    <?= $todayCustomers ?>
                </div>
                <div class="text-[11px] text-slate-400 mt-0.5">Scanned &amp; recorded</div>
            </div>
        </div>

        <!-- Card 2: Today's Local -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Local Guests</span>
                <span class="p-1.5 rounded-lg bg-blue-50 text-blue-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                </span>
            </div>
            <div>
                <div id="metricTodayLocal" class="text-2xl sm:text-3xl font-black text-blue-600 tracking-tight">
                    <?= $todayLocal ?>
                </div>
                <div class="text-[11px] text-slate-400 mt-0.5">From nearby area</div>
            </div>
        </div>

        <!-- Card 3: Today's Outsiders -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Outsiders</span>
                <span class="p-1.5 rounded-lg bg-purple-50 text-purple-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </span>
            </div>
            <div>
                <div id="metricTodayOutsiders" class="text-2xl sm:text-3xl font-black text-purple-600 tracking-tight">
                    <?= $todayOutsiders ?>
                </div>
                <div class="text-[11px] text-slate-400 mt-0.5">Highway / Visitors</div>
            </div>
        </div>

        <!-- Card 4: Today's Bill Amount -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between col-span-2 sm:col-span-1">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Today's Bill</span>
                <span class="p-1.5 rounded-lg bg-emerald-50 text-emerald-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 8h6m-5 0a3 3 0 110 6H9l3 3m-3-6h6m6 1a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div>
                <div id="metricTodayBill" class="text-2xl sm:text-3xl font-black text-emerald-600 tracking-tight truncate">
                    <?= format_currency($todayBillAmount) ?>
                </div>
                <div class="text-[11px] text-slate-400 mt-0.5">Initiated via UPI</div>
            </div>
        </div>

        <!-- Card 5: All Time Customers -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Total Database</span>
                <span class="p-1.5 rounded-lg bg-slate-100 text-slate-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1.5 3 3.5 3h9c2 0 3.5-1 3.5-3V7c0-2-1.5-3-3.5-3h-9C5.5 4 4 5 4 7z"/></svg>
                </span>
            </div>
            <div>
                <div id="metricTotalCustomers" class="text-2xl sm:text-3xl font-black text-slate-800 tracking-tight">
                    <?= $totalCustomers ?>
                </div>
                <div class="text-[11px] text-slate-400 mt-0.5">All-time patrons</div>
            </div>
        </div>

        <!-- Card 6: Marketing Consent -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Consent Rate</span>
                <span class="p-1.5 rounded-lg bg-amber-50 text-amber-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div>
                <div id="metricConsentRate" class="text-2xl sm:text-3xl font-black text-amber-600 tracking-tight">
                    <?= $consentPct ?>%
                </div>
                <div class="text-[11px] text-slate-400 mt-0.5"><?= $consentCount ?> agreed to offers</div>
            </div>
        </div>

    </div>

    <!-- Middle Section: Quick Counter QR Card & Notice -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Standee / Counter QR Card -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col sm:flex-row lg:flex-col items-center gap-6">
            <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 flex flex-col items-center shrink-0">
                <div class="text-xs font-bold uppercase tracking-widest text-slate-500 mb-2 text-center">
                    Counter Display QR
                </div>
                <!-- Dynamic QR via high-speed CDN API -->
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=<?= urlencode($payUrl) ?>&margin=8"
                     alt="Customer Payment QR"
                     class="w-40 h-40 rounded-lg shadow-xs bg-white border border-slate-200"
                     loading="lazy">
                <div class="mt-2 text-[11px] font-bold text-slate-700 tracking-wider">
                    SCAN TO PAY
                </div>
            </div>

            <div class="space-y-3 text-center sm:text-left lg:text-center flex-1">
                <div>
                    <h3 class="font-bold text-slate-900 text-base">Your Restaurant Payment QR</h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        Place this QR on counter tables or billing point. It routes customers to capture details before their UPI payment.
                    </p>
                </div>
                <div class="pt-1 flex flex-col sm:flex-row lg:flex-col gap-2">
                    <a href="<?= BASE_URL ?>/admin/settings.php#printStandee" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-slate-900 hover:bg-black text-white text-xs font-bold rounded-xl transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Print Counter Standee</span>
                    </a>
                    <button onclick="navigator.clipboard.writeText('<?= e($payUrl) ?>'); alert('Payment URL copied to clipboard!');" class="inline-flex items-center justify-center gap-2 px-4 py-2 border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-xl transition">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                        <span>Copy Pay URL</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Real-World Workflow Operational Reminder -->
        <div class="lg:col-span-2 bg-gradient-to-br from-slate-900 to-slate-800 text-white p-6 rounded-2xl shadow-sm flex flex-col justify-between">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-amber-300 text-xs font-bold tracking-wide uppercase mb-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Counter Payment Verification Note</span>
                </div>
                <h3 class="text-lg sm:text-xl font-bold tracking-tight text-white mb-2">
                    How Payment Verification Works
                </h3>
                <p class="text-xs sm:text-sm text-slate-300 leading-relaxed space-y-2">
                    SMART QR captures customer names, mobile numbers, and audience demographic (Local vs Outsider) <strong>before</strong> forwarding them to their UPI app. 
                    Payment money goes directly to your existing UPI ID (<strong class="text-amber-300"><?= e($currentRestaurant['upi_id']) ?></strong>) without gateway fees.
                </p>
                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs text-slate-300">
                    <div class="bg-white/5 border border-white/10 rounded-xl p-3">
                        <strong class="text-white block mb-1">1. Customer Submits</strong>
                        Customer fills name &amp; phone, then taps "Pay Now". Customer record is saved in MySQL.
                    </div>
                    <div class="bg-white/5 border border-white/10 rounded-xl p-3">
                        <strong class="text-white block mb-1">2. Counter Verifies</strong>
                        Verify payment via your normal UPI soundbox notification or customer's phone screen.
                    </div>
                </div>
            </div>

            <div class="mt-5 pt-4 border-t border-white/10 flex flex-wrap items-center justify-between gap-2 text-[11px] text-slate-400">
                <span>Multi-Restaurant Tenant Isolation: Active</span>
                <span>Mentor: Abhishek Chaudhary &bull; ALAG INNOVATIVE</span>
            </div>
        </div>

    </div>

    <!-- Recent Customers Table / Mobile Cards -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-bold text-slate-900">Recent Customer Transactions</h2>
                <p class="text-xs text-slate-500">Latest visitors who scanned the QR and initiated UPI payment</p>
            </div>
            <a href="customers.php" class="inline-flex items-center gap-1.5 text-xs font-bold text-brand-600 hover:text-brand-700">
                <span>View All Records &amp; Export CSV</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        <?php if (empty($recentCustomers)): ?>
        <div class="p-12 text-center text-slate-400">
            <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            <h3 class="text-sm font-bold text-slate-700 mb-1">No customers recorded yet</h3>
            <p class="text-xs text-slate-500 max-w-sm mx-auto mb-4">
                Customers will appear here in real-time as they scan your counter QR code and enter their details.
            </p>
            <a href="<?= e($payUrl) ?>" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-brand-600 text-white rounded-xl text-xs font-bold shadow-xs">
                Test Customer Payment Flow &rarr;
            </a>
        </div>
        <?php else: ?>

        <!-- DESKTOP TABLE -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-5">Customer</th>
                        <th class="py-3 px-5">Mobile</th>
                        <th class="py-3 px-5">Audience Type</th>
                        <th class="py-3 px-5">Bill Amount</th>
                        <th class="py-3 px-5">Marketing</th>
                        <th class="py-3 px-5 text-right">Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($recentCustomers as $customer): ?>
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="py-3.5 px-5 font-bold text-slate-900">
                            <?= e($customer['name']) ?>
                        </td>
                        <td class="py-3.5 px-5 font-mono text-xs text-slate-600">
                            <?= e(mask_mobile($customer['mobile'])) ?>
                        </td>
                        <td class="py-3.5 px-5">
                            <?php if ($customer['customer_type'] === 'Local'): ?>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200/60">
                                Local
                            </span>
                            <?php else: ?>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200/60">
                                Outsider
                            </span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3.5 px-5 font-black text-slate-900">
                            <?= format_currency($customer['bill_amount']) ?>
                        </td>
                        <td class="py-3.5 px-5 text-xs">
                            <?php if ($customer['marketing_consent']): ?>
                            <span class="inline-flex items-center gap-1 text-emerald-700 font-semibold">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                <span>Opted In</span>
                            </span>
                            <?php else: ?>
                            <span class="text-slate-400">Declined</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3.5 px-5 text-right text-xs text-slate-400 font-medium">
                            <?= format_relative_time($customer['created_at']) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- MOBILE RESPONSIVE CARDS (Cards replace table on small screens) -->
        <div class="md:hidden divide-y divide-slate-100">
            <?php foreach ($recentCustomers as $customer): ?>
            <div class="p-4 flex items-center justify-between gap-3">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-slate-900 text-sm"><?= e($customer['name']) ?></span>
                        <span class="text-[10px] px-2 py-0.2 rounded-full font-semibold <?= $customer['customer_type'] === 'Local' ? 'bg-blue-50 text-blue-700' : 'bg-purple-50 text-purple-700' ?>">
                            <?= e($customer['customer_type']) ?>
                        </span>
                    </div>
                    <div class="text-xs text-slate-500 font-mono">
                        <?= e(mask_mobile($customer['mobile'])) ?>
                    </div>
                    <div class="text-[11px] text-slate-400">
                        <?= format_relative_time($customer['created_at']) ?>
                    </div>
                </div>

                <div class="text-right">
                    <div class="text-base font-black text-slate-900">
                        <?= format_currency($customer['bill_amount']) ?>
                    </div>
                    <div class="text-[10px] <?= $customer['marketing_consent'] ? 'text-emerald-600 font-bold' : 'text-slate-400' ?>">
                        <?= $customer['marketing_consent'] ? 'Offers: Yes' : 'Offers: No' ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php endif; ?>

    </div>

</div>

<!-- Realtime Polling JS Script -->
<script src="../js/dashboard.js"></script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
