<?php
/**
 * SMART QR - Customer Records & Filter Management
 * Developed by BCA Students | Siddharth Shikshan Avam Prashikshan Sansthan
 * Mentored by Abhishek Chaudhary | ALAG INNOVATIVE SOLUTIONS
 */

$pageTitle = 'Customer Directory';
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/header.php';

$restaurantId = (int)$currentRestaurant['id'];
$db = get_db();

// Filter & Search Inputs
$search    = sanitize($_GET['search'] ?? '');
$type      = sanitize($_GET['type'] ?? '');
$consent   = sanitize($_GET['consent'] ?? '');
$dateRange = sanitize($_GET['date_range'] ?? 'all');
$startDate = sanitize($_GET['start_date'] ?? '');
$endDate   = sanitize($_GET['end_date'] ?? '');
$page      = max(1, (int)($_GET['page'] ?? 1));
$perPage   = 12;

// Build Filter SQL
$where = ["restaurant_id = ?"];
$params = [$restaurantId];

if (!empty($search)) {
    $where[] = "(name LIKE ? OR mobile LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

if (!empty($type) && in_array(strtolower($type), ['local', 'outsider'])) {
    $where[] = "customer_type = ?";
    $params[] = ucfirst(strtolower($type));
}

if ($consent !== '' && in_array($consent, ['0', '1'], true)) {
    $where[] = "marketing_consent = ?";
    $params[] = (int)$consent;
}

// Date Range Filtering
switch ($dateRange) {
    case 'today':
        $where[] = "created_at BETWEEN ? AND ?";
        $params[] = date('Y-m-d 00:00:00');
        $params[] = date('Y-m-d 23:59:59');
        break;
    case '7days':
        $where[] = "created_at >= ?";
        $params[] = date('Y-m-d 00:00:00', strtotime('-7 days'));
        break;
    case '30days':
        $where[] = "created_at >= ?";
        $params[] = date('Y-m-d 00:00:00', strtotime('-30 days'));
        break;
    case 'custom':
        if (!empty($startDate)) {
            $where[] = "created_at >= ?";
            $params[] = date('Y-m-d 00:00:00', strtotime($startDate));
        }
        if (!empty($endDate)) {
            $where[] = "created_at <= ?";
            $params[] = date('Y-m-d 23:59:59', strtotime($endDate));
        }
        break;
    default:
        // 'all'
        break;
}

$whereClause = implode(" AND ", $where);

// Count Total Matching Records
$countStmt = $db->prepare("SELECT COUNT(*) AS total FROM customers WHERE {$whereClause}");
$countStmt->execute($params);
$totalRecords = (int)($countStmt->fetch()['total'] ?? 0);
$totalPages = max(1, (int)ceil($totalRecords / $perPage));

// Adjust current page if out of bounds
if ($page > $totalPages) {
    $page = $totalPages;
}
$offset = ($page - 1) * $perPage;

// Fetch Paginated Customers
$dataSql = "SELECT * FROM customers WHERE {$whereClause} ORDER BY created_at DESC LIMIT {$perPage} OFFSET {$offset}";
$dataStmt = $db->prepare($dataSql);
$dataStmt->execute($params);
$customers = $dataStmt->fetchAll();

// Construct base query string for pagination links
$queryParams = $_GET;
unset($queryParams['page']);
$baseQueryString = http_build_query($queryParams);
?>

<div class="p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto space-y-6">

    <!-- Page Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Customer Database</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                Total <span class="font-bold text-slate-800"><?= $totalRecords ?></span> customer profiles captured before UPI payment
            </p>
        </div>

        <div class="flex items-center gap-2">
            <!-- Privacy Toggle -->
            <button type="button" id="togglePrivacyBtn" class="inline-flex items-center gap-1.5 px-3 py-2 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-semibold shadow-xs transition">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                <span id="privacyBtnText">Reveal Numbers</span>
            </button>

            <!-- CSV Export Button -->
            <button type="button" id="exportCsvBtn" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span>Export CSV</span>
            </button>
        </div>
    </div>

    <!-- Search & Filter Bar Card -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
        <form id="filterForm" method="GET" action="customers.php" class="space-y-4">
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                
                <!-- Search Input -->
                <div class="sm:col-span-2 lg:col-span-1">
                    <label for="search" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Search</label>
                    <div class="relative">
                        <input type="text" id="search" name="search" value="<?= e($search) ?>" placeholder="Search name or mobile..."
                               class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand-500">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                </div>

                <!-- Audience Filter -->
                <div>
                    <label for="type" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Audience Type</label>
                    <select id="type" name="type" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand-500">
                        <option value="">All Audiences</option>
                        <option value="Local" <?= strtolower($type) === 'local' ? 'selected' : '' ?>>Local Guests</option>
                        <option value="Outsider" <?= strtolower($type) === 'outsider' ? 'selected' : '' ?>>Outsiders / Visitors</option>
                    </select>
                </div>

                <!-- Marketing Consent Filter -->
                <div>
                    <label for="consent" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Marketing Consent</label>
                    <select id="consent" name="consent" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand-500">
                        <option value="">All Consent States</option>
                        <option value="1" <?= $consent === '1' ? 'selected' : '' ?>>Opted In (Consent Given)</option>
                        <option value="0" <?= $consent === '0' ? 'selected' : '' ?>>Declined / Not Given</option>
                    </select>
                </div>

                <!-- Date Range Dropdown -->
                <div>
                    <label for="date_range" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Date Period</label>
                    <select id="date_range" name="date_range" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand-500">
                        <option value="all" <?= $dateRange === 'all' ? 'selected' : '' ?>>All Time</option>
                        <option value="today" <?= $dateRange === 'today' ? 'selected' : '' ?>>Today Only</option>
                        <option value="7days" <?= $dateRange === '7days' ? 'selected' : '' ?>>Last 7 Days</option>
                        <option value="30days" <?= $dateRange === '30days' ? 'selected' : '' ?>>Last 30 Days</option>
                        <option value="custom" <?= $dateRange === 'custom' ? 'selected' : '' ?>>Custom Range...</option>
                    </select>
                </div>

            </div>

            <!-- Custom Date Inputs (Conditional) -->
            <div id="customDateContainer" class="<?= $dateRange === 'custom' ? '' : 'hidden' ?> pt-2 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-3 max-w-md">
                <div>
                    <label for="start_date" class="block text-[10px] font-bold text-slate-400 uppercase mb-1">From Date</label>
                    <input type="date" id="start_date" name="start_date" value="<?= e($startDate) ?>"
                           class="w-full px-3 py-1.5 text-xs rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>
                <div>
                    <label for="end_date" class="block text-[10px] font-bold text-slate-400 uppercase mb-1">To Date</label>
                    <input type="date" id="end_date" name="end_date" value="<?= e($endDate) ?>"
                           class="w-full px-3 py-1.5 text-xs rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>
            </div>

            <!-- Filter Action Buttons -->
            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <?php if (!empty($search) || !empty($type) || $consent !== '' || $dateRange !== 'all'): ?>
                <a href="customers.php" class="px-3 py-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 transition">
                    Clear Filters
                </a>
                <?php endif; ?>
                <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-black text-white text-xs font-bold rounded-xl transition shadow-xs">
                    Apply Filters
                </button>
            </div>

        </form>
    </div>

    <!-- Customers List (Desktop Table / Mobile Cards) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <?php if (empty($customers)): ?>
        <div class="p-12 text-center text-slate-400">
            <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            <h3 class="text-sm font-bold text-slate-700 mb-1">No customers match your criteria</h3>
            <p class="text-xs text-slate-500 mb-4">Try clearing or adjusting your search filters.</p>
            <a href="customers.php" class="text-xs font-bold text-brand-600 hover:underline">Reset all filters</a>
        </div>
        <?php else: ?>

        <!-- DESKTOP TABLE -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-5">#</th>
                        <th class="py-3 px-5">Customer Name</th>
                        <th class="py-3 px-5">Mobile</th>
                        <th class="py-3 px-5">Audience</th>
                        <th class="py-3 px-5">Bill Amount</th>
                        <th class="py-3 px-5">Offers Consent</th>
                        <th class="py-3 px-5 text-right">Date &amp; Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($customers as $idx => $c): ?>
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="py-3.5 px-5 text-xs text-slate-400 font-mono">
                            <?= $offset + $idx + 1 ?>
                        </td>
                        <td class="py-3.5 px-5 font-bold text-slate-900">
                            <?= e($c['name']) ?>
                        </td>
                        <td class="py-3.5 px-5 font-mono text-xs text-slate-700">
                            <span class="customer-phone" data-masked="<?= e(mask_mobile($c['mobile'])) ?>" data-full="<?= e($c['mobile']) ?>">
                                <?= e(mask_mobile($c['mobile'])) ?>
                            </span>
                        </td>
                        <td class="py-3.5 px-5">
                            <?php if ($c['customer_type'] === 'Local'): ?>
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
                            <?= format_currency($c['bill_amount']) ?>
                        </td>
                        <td class="py-3.5 px-5 text-xs">
                            <?php if ($c['marketing_consent']): ?>
                            <span class="inline-flex items-center gap-1 text-emerald-700 font-bold bg-emerald-50 px-2 py-0.5 rounded-md">
                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                <span>Consent Given</span>
                            </span>
                            <?php else: ?>
                            <span class="text-slate-400 text-xs">Declined</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3.5 px-5 text-right text-xs text-slate-500 font-medium">
                            <div><?= date('d M Y', strtotime($c['created_at'])) ?></div>
                            <div class="text-[11px] text-slate-400"><?= date('h:i A', strtotime($c['created_at'])) ?></div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- MOBILE RESPONSIVE CARDS -->
        <div class="md:hidden divide-y divide-slate-100">
            <?php foreach ($customers as $c): ?>
            <div class="p-4 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-slate-900 text-sm"><?= e($c['name']) ?></span>
                    <span class="text-base font-black text-slate-900"><?= format_currency($c['bill_amount']) ?></span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span class="font-mono text-slate-600 customer-phone" data-masked="<?= e(mask_mobile($c['mobile'])) ?>" data-full="<?= e($c['mobile']) ?>">
                        <?= e(mask_mobile($c['mobile'])) ?>
                    </span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold <?= $c['customer_type'] === 'Local' ? 'bg-blue-50 text-blue-700' : 'bg-purple-50 text-purple-700' ?>">
                        <?= e($c['customer_type']) ?>
                    </span>
                </div>
                <div class="flex items-center justify-between text-[11px] text-slate-400 pt-1 border-t border-slate-50">
                    <span><?= format_relative_time($c['created_at']) ?></span>
                    <span class="<?= $c['marketing_consent'] ? 'text-emerald-600 font-bold' : 'text-slate-400' ?>">
                        <?= $c['marketing_consent'] ? '✓ Opted In for Offers' : 'No Offers' ?>
                    </span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination Controls -->
        <?php if ($totalPages > 1): ?>
        <div class="p-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
            <div>
                Showing page <span class="font-bold text-slate-800"><?= $page ?></span> of <span class="font-bold text-slate-800"><?= $totalPages ?></span>
            </div>
            <div class="flex items-center gap-1.5">
                <?php if ($page > 1): ?>
                <a href="customers.php?<?= $baseQueryString ?>&page=<?= $page - 1 ?>" class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold transition">
                    &larr; Previous
                </a>
                <?php endif; ?>

                <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                <a href="customers.php?<?= $baseQueryString ?>&page=<?= $i ?>" class="px-3 py-1.5 rounded-lg border <?= $i === $page ? 'bg-brand-600 text-white border-brand-600 font-bold' : 'border-slate-200 text-slate-700 hover:bg-slate-50' ?> transition">
                    <?= $i ?>
                </a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                <a href="customers.php?<?= $baseQueryString ?>&page=<?= $page + 1 ?>" class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold transition">
                    Next &rarr;
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php endif; ?>

    </div>

</div>

<!-- Customers JS for CSV export & privacy toggle -->
<script src="../js/customers.js"></script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
