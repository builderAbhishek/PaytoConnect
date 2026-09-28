<?php
/**
 * SMART QR - Restaurant Profile Settings & QR Standee Generator
 * Developed by BCA Students | Siddharth Shikshan Avam Prashikshan Sansthan
 * Mentored by Abhishek Chaudhary | ALAG INNOVATIVE SOLUTIONS
 */

$pageTitle = 'Outlet Settings & QR';
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/header.php';

$restaurantId = (int)$currentRestaurant['id'];
$db = get_db();
$error = '';
$success = '';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($csrfToken)) {
        $error = 'Security session expired. Please refresh and try again.';
    } else {
        $name        = sanitize($_POST['name'] ?? '');
        $phone       = clean_indian_mobile($_POST['phone'] ?? '');
        $upiId       = sanitize($_POST['upi_id'] ?? '');
        $address     = sanitize($_POST['address'] ?? '');
        $description = sanitize($_POST['description'] ?? '');
        $logo        = sanitize($_POST['logo'] ?? '');

        // Validations
        if (empty($name)) {
            $error = 'Restaurant name is required.';
        } elseif (empty($phone) || strlen($phone) < 10) {
            $error = 'Please enter a valid 10-digit contact phone number.';
        } elseif (empty($upiId) || !str_contains($upiId, '@')) {
            $error = 'Please enter a valid UPI ID (e.g. yourname@okhdfcbank or 9137470661@superyes).';
        } elseif (empty($address)) {
            $error = 'Restaurant address is required.';
        } else {
            try {
                $updateSql = "UPDATE restaurants SET 
                                name = ?, 
                                phone = ?, 
                                upi_id = ?, 
                                address = ?, 
                                description = ?, 
                                logo = ?, 
                                updated_at = ? 
                              WHERE id = ?";
                $updateStmt = $db->prepare($updateSql);
                $updateStmt->execute([
                    $name,
                    $phone,
                    $upiId,
                    $address,
                    $description,
                    $logo,
                    date('Y-m-d H:i:s'),
                    $restaurantId
                ]);

                // Refresh current restaurant info
                $currentRestaurant = get_restaurant_by_id($restaurantId);
                set_flash('success', 'Restaurant settings saved successfully.');
                header('Location: settings.php');
                exit;

            } catch (PDOException $e) {
                error_log("Settings update error: " . $e->getMessage());
                $error = 'Could not update settings. Please check database connection.';
            }
        }
    }
}

// Payment Page URL for counter QR
$payUrl = BASE_URL . '/pay.php?slug=' . urlencode($currentRestaurant['slug']);
$qrCodeApiUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($payUrl) . "&margin=10";
?>

<div class="p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto space-y-6">

    <!-- Page Header -->
    <div>
        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Outlet Settings &amp; QR Standee</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
            Configure your restaurant profile, UPI destination ID, and download your counter QR code
        </p>
    </div>

    <?php if (!empty($error)): ?>
    <div class="p-3.5 rounded-xl border border-red-200 bg-red-50 text-red-800 text-xs sm:text-sm flex items-center gap-2">
        <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span><?= e($error) ?></span>
    </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left Column: Settings Form -->
        <div class="lg:col-span-7 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
            <h2 class="text-base font-bold text-slate-900 mb-1">Restaurant Profile</h2>
            <p class="text-xs text-slate-500 mb-6">These details are shown dynamically to customers on the payment page.</p>

            <form method="POST" action="settings.php" class="space-y-4">
                <?= csrf_field() ?>

                <!-- Restaurant Name -->
                <div>
                    <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        Restaurant / Outlet Name *
                    </label>
                    <input type="text" id="name" name="name" required value="<?= e($currentRestaurant['name']) ?>"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>

                <!-- Slug (Readonly) -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                        Outlet URL Slug (Permanent Identifier)
                    </label>
                    <div class="flex items-center">
                        <span class="inline-flex items-center px-3 py-2.5 rounded-l-xl border border-r-0 border-slate-200 bg-slate-100 text-xs text-slate-500 font-mono">
                            <?= BASE_URL ?>/pay.php?slug=
                        </span>
                        <input type="text" readonly value="<?= e($currentRestaurant['slug']) ?>"
                               class="w-full px-3.5 py-2.5 rounded-r-xl border border-slate-200 bg-slate-50 text-xs text-slate-600 font-mono font-bold cursor-not-allowed">
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">Slug determines your unique QR routing.</p>
                </div>

                <!-- UPI ID -->
                <div>
                    <label for="upi_id" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        Restaurant UPI ID (VPA) *
                    </label>
                    <input type="text" id="upi_id" name="upi_id" required value="<?= e($currentRestaurant['upi_id']) ?>"
                           placeholder="e.g. 9137470661@superyes or merchant@okhdfcbank"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-mono font-bold text-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500">
                    <p class="text-[11px] text-slate-400 mt-1">All UPI payments are transferred directly to this account via your customers' UPI apps.</p>
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        Contact Mobile / Phone *
                    </label>
                    <input type="tel" id="phone" name="phone" required value="<?= e($currentRestaurant['phone']) ?>"
                           placeholder="10-digit mobile"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>

                <!-- Address -->
                <div>
                    <label for="address" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        Full Address / Location *
                    </label>
                    <textarea id="address" name="address" rows="2" required
                              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"><?= e($currentRestaurant['address']) ?></textarea>
                </div>

                <!-- Short Description -->
                <div>
                    <label for="description" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        Tagline / Short Description
                    </label>
                    <input type="text" id="description" name="description" value="<?= e($currentRestaurant['description'] ?? '') ?>"
                           placeholder="e.g. Delicious food, family dine-in & takeaway"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>

                <!-- Logo Path / URL -->
                <div>
                    <label for="logo" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        Logo File Path or Image URL
                    </label>
                    <input type="text" id="logo" name="logo" value="<?= e($currentRestaurant['logo'] ?? '') ?>"
                           placeholder="assets/images/chaudhary-logo.svg"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-mono focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end">
                    <button type="submit" class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition">
                        Save Profile Settings
                    </button>
                </div>
            </form>
        </div>

        <!-- Right Column: Counter QR Standee Generator Preview -->
        <div class="lg:col-span-5 space-y-6" id="printStandee">
            
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col items-center text-center">
                
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-brand-50 text-brand-700 text-xs font-bold mb-4">
                    <span class="w-2 h-2 rounded-full bg-brand-600"></span>
                    <span>Ready for Counter Standee</span>
                </div>

                <!-- Print Standee Box -->
                <div id="standeeCard" class="bg-slate-50 border-2 border-dashed border-slate-300 p-6 rounded-2xl w-full max-w-xs flex flex-col items-center">
                    
                    <!-- Outlet Name Header on Standee -->
                    <div class="text-sm font-black text-slate-900 leading-tight uppercase tracking-tight mb-1">
                        <?= e($currentRestaurant['name']) ?>
                    </div>
                    <div class="text-[11px] text-slate-500 mb-4 truncate max-w-[220px]">
                        <?= e($currentRestaurant['address']) ?>
                    </div>

                    <!-- QR Code -->
                    <div class="p-3 bg-white rounded-2xl shadow-sm border border-slate-200">
                        <img src="<?= e($qrCodeApiUrl) ?>" alt="Counter Payment QR" class="w-48 h-48 rounded-lg mx-auto" id="standeeQrImg">
                    </div>

                    <!-- Scan to Pay badge -->
                    <div class="mt-4 px-4 py-1.5 bg-slate-900 text-white rounded-full text-xs font-black tracking-widest uppercase shadow-xs">
                        SCAN TO PAY
                    </div>

                    <div class="mt-3 text-[10px] text-slate-500 font-medium">
                        Accepts Google Pay &bull; PhonePe &bull; Paytm &bull; BHIM
                    </div>

                    <div class="mt-2 pt-2 border-t border-slate-200/60 w-full text-[9px] text-slate-400 font-mono truncate">
                        UPI: <?= e($currentRestaurant['upi_id']) ?>
                    </div>
                </div>

                <!-- Standee Actions -->
                <div class="mt-5 flex flex-col w-full max-w-xs gap-2">
                    <button type="button" onclick="window.print()" class="w-full py-2.5 px-4 bg-slate-900 hover:bg-black text-white font-bold rounded-xl text-xs transition shadow-xs flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Print Standee Stand</span>
                    </button>

                    <a href="<?= e($payUrl) ?>" target="_blank" class="w-full py-2.5 px-4 border border-slate-300 hover:bg-slate-50 text-slate-700 font-bold rounded-xl text-xs transition flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        <span>Test Scan (Open Pay Page)</span>
                    </a>
                </div>

                <div class="mt-4 text-[11px] text-slate-400 max-w-xs leading-relaxed">
                    <strong>Important Note:</strong> This QR encodes your restaurant web URL (<code class="text-slate-600 font-mono"><?= e($payUrl) ?></code>). Customers scan this URL to register their details before UPI payment.
                </div>

            </div>

        </div>

    </div>

</div>

<!-- Print Stylesheet -->
<style>
@media print {
    body * { visibility: hidden; }
    #standeeCard, #standeeCard * { visibility: visible; }
    #standeeCard {
        position: fixed;
        left: 50%;
        top: 20%;
        transform: translateX(-50%);
        border: 2px solid #000 !important;
        box-shadow: none !important;
    }
}
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
