<?php
/**
 * SMART QR - Dynamic Customer Payment & Data Ingestion Page
 * Developed by BCA Students | Siddharth Shikshan Avam Prashikshan Sansthan
 * Mentored by Abhishek Chaudhary | ALAG INNOVATIVE SOLUTIONS
 *
 * URL: pay.php?slug=chaudhary
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';

// 1. Resolve Restaurant from URL query parameter (defaulting to 'chaudhary' for demo convenience)
$slug = sanitize($_GET['slug'] ?? ($_GET['restaurant'] ?? 'chaudhary'));
$restaurant = get_restaurant_by_slug($slug);
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= $restaurant ? e($restaurant['name']) . ' - Pay Your Bill' : 'Outlet Not Found' ?> | <?= APP_NAME ?></title>
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
                        charcoal: '#0f172a'
                    }
                }
            }
        }
    </script>
    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        /* Remove default spin buttons from number input */
        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        input[type="number"] {
            -moz-appearance: textfield;
        }
    </style>
</head>
<body class="h-full flex flex-col justify-between text-slate-800 antialiased selection:bg-brand-500 selection:text-white">

    <!-- MAIN CONTAINER -->
    <div class="w-full max-w-md mx-auto min-h-full flex flex-col justify-between p-4 sm:p-6">

        <?php if (!$restaurant): ?>
        <!-- ERROR STATE: RESTAURANT NOT FOUND -->
        <div class="my-auto bg-white rounded-3xl p-6 sm:p-8 text-center shadow-sm border border-slate-200">
            <div class="w-16 h-16 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h1 class="text-xl font-black text-slate-900 mb-2">Outlet Not Found</h1>
            <p class="text-xs sm:text-sm text-slate-500 mb-6 leading-relaxed">
                The restaurant QR code slug <strong class="text-slate-800 font-mono">"<?= e($slug) ?>"</strong> was not recognized. Please scan the QR code at your table again.
            </p>
            <div class="space-y-2">
                <a href="pay.php?slug=chaudhary" class="block w-full py-3 px-4 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-2xl text-xs sm:text-sm shadow-sm transition">
                    View Demo Restaurant (Chaudhary Cafe)
                </a>
                <a href="<?= BASE_URL ?>/" class="block w-full py-2.5 px-4 text-xs font-semibold text-slate-500 hover:text-slate-700 transition">
                    Return to SMART QR Homepage
                </a>
            </div>
        </div>

        <?php else: ?>
        <!-- VALID RESTAURANT PAYMENT FORM -->

        <div class="bg-white rounded-3xl shadow-sm border border-slate-200/90 overflow-hidden my-auto">

            <!-- RESTAURANT BRANDING HEADER -->
            <div class="p-6 pb-5 text-center bg-gradient-to-b from-orange-50/70 to-white border-b border-slate-100">
                
                <!-- Restaurant Logo / Emblem -->
                <div class="mx-auto mb-3 flex items-center justify-center">
                    <?php if (!empty($restaurant['logo']) && file_exists(__DIR__ . '/' . $restaurant['logo'])): ?>
                    <img src="<?= e($restaurant['logo']) ?>" alt="<?= e($restaurant['name']) ?>" class="w-16 h-16 rounded-2xl shadow-xs border border-orange-200/60 object-cover">
                    <?php else: ?>
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 text-white flex items-center justify-center shadow-xs font-black text-2xl">
                        <?= strtoupper(substr($restaurant['name'], 0, 1)) ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Restaurant Name & Location -->
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight leading-tight">
                    <?= e($restaurant['name']) ?>
                </h1>
                
                <p class="text-xs text-slate-500 mt-1 max-w-xs mx-auto leading-relaxed flex items-center justify-center gap-1">
                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span><?= e($restaurant['address']) ?></span>
                </p>

                <!-- Secure Reassurance Badge -->
                <div class="mt-3.5 inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 text-[11px] font-semibold border border-emerald-100/80">
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span>Direct UPI Payment &bull; Instant at Counter</span>
                </div>
            </div>

            <!-- PAYMENT FORM SECTION -->
            <div class="p-6 pt-5">

                <!-- Dynamic Error Alert Box -->
                <div id="errorAlert" class="hidden mb-4 p-3.5 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-xs flex items-start gap-2">
                    <svg class="w-4 h-4 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span id="errorMessage"></span>
                </div>

                <form id="paymentForm" class="space-y-4">
                    <!-- Hidden Restaurant Metadata -->
                    <input type="hidden" id="restaurant_slug" name="restaurant_slug" value="<?= e($restaurant['slug']) ?>">
                    <input type="hidden" id="restaurant_id" name="restaurant_id" value="<?= (int)$restaurant['id'] ?>">

                    <!-- Field 1: Customer Name -->
                    <div>
                        <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Your Name *
                        </label>
                        <div class="relative">
                            <input type="text" id="name" name="name" required placeholder="Enter your full name" autocomplete="name"
                                   class="w-full px-4 py-3.5 rounded-2xl border border-slate-300 text-sm font-semibold text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition shadow-2xs">
                        </div>
                    </div>

                    <!-- Field 2: Mobile Number (10-digit Indian format) -->
                    <div>
                        <label for="mobile" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Mobile Number *
                        </label>
                        <div class="relative flex">
                            <span class="inline-flex items-center px-3.5 rounded-l-2xl border border-r-0 border-slate-300 bg-slate-50 text-slate-500 text-xs font-bold font-mono">
                                +91
                            </span>
                            <input type="tel" id="mobile" name="mobile" required maxlength="10" inputmode="numeric" placeholder="10-digit mobile number" autocomplete="tel"
                                   class="w-full px-4 py-3.5 rounded-r-2xl border border-slate-300 text-sm font-bold font-mono text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition shadow-2xs">
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Used for billing records and order receipt.</p>
                    </div>

                    <!-- Field 3: Customer Demographic (Local vs Outsider) -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            You are visiting from:
                        </label>
                        <div class="grid grid-cols-2 gap-2.5">
                            <label class="cursor-pointer">
                                <input type="radio" name="customer_type" value="Local" checked class="peer sr-only">
                                <div class="p-3 text-center rounded-2xl border-2 border-slate-200 text-slate-700 text-xs font-bold peer-checked:border-brand-600 peer-checked:bg-orange-50/50 peer-checked:text-brand-700 transition flex items-center justify-center gap-1.5 shadow-2xs">
                                    <svg class="w-4 h-4 text-slate-400 peer-checked:text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                    <span>Local Resident</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="customer_type" value="Outsider" class="peer sr-only">
                                <div class="p-3 text-center rounded-2xl border-2 border-slate-200 text-slate-700 text-xs font-bold peer-checked:border-brand-600 peer-checked:bg-orange-50/50 peer-checked:text-brand-700 transition flex items-center justify-center gap-1.5 shadow-2xs">
                                    <svg class="w-4 h-4 text-slate-400 peer-checked:text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                    <span>Outsider / Traveler</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Field 4: Bill Amount -->
                    <div>
                        <label for="bill_amount" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Bill Amount (₹) *
                        </label>
                        <div class="relative flex">
                            <span class="inline-flex items-center px-4 rounded-l-2xl border border-r-0 border-slate-300 bg-slate-50 text-slate-700 text-base font-black">
                                ₹
                            </span>
                            <input type="number" step="0.01" min="1" max="500000" id="bill_amount" name="bill_amount" required inputmode="decimal" placeholder="Enter amount to pay"
                                   class="w-full px-4 py-3.5 rounded-r-2xl border border-slate-300 text-lg font-black text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition shadow-2xs">
                        </div>
                    </div>

                    <!-- Field 5: Marketing Consent Checkbox (Optional, Never Blocks Payment) -->
                    <div class="pt-1">
                        <label class="flex items-start gap-3 cursor-pointer group">
                            <input type="checkbox" id="marketing_consent" name="marketing_consent" value="1"
                                   class="mt-1 w-4 h-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500 cursor-pointer">
                            <span class="text-xs text-slate-600 leading-relaxed select-none group-hover:text-slate-900 transition">
                                I agree to receive special discounts, festive offers, and menu updates from <strong><?= e($restaurant['name']) ?></strong>.
                            </span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-3">
                        <button type="submit" id="payBtn"
                                class="w-full min-h-[52px] py-4 px-6 bg-brand-600 hover:bg-brand-700 active:scale-[0.99] text-white font-extrabold rounded-2xl text-base tracking-wide transition shadow-md shadow-brand-500/20 flex items-center justify-center gap-2">
                            <svg id="btnSpinner" class="hidden animate-spin w-5 h-5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span id="btnText">PAY NOW &rarr;</span>
                        </button>
                    </div>

                </form>

            </div>

            <!-- Footer Reassurance -->
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 text-center">
                <p class="text-[11px] text-slate-500 leading-normal">
                    You will be redirected to your UPI app (Google Pay, PhonePe, Paytm, BHIM, etc.).
                </p>
                <p class="text-[10px] text-slate-400 mt-1">
                    Paying to: <span class="font-mono font-semibold text-slate-600"><?= e($restaurant['upi_id']) ?></span>
                </p>
            </div>

        </div>

        <?php endif; ?>

        <!-- Bottom Attribution -->
        <footer class="mt-4 text-center text-xs text-slate-400 py-2">
            <div class="font-bold text-slate-500 text-[11px]"><?= APP_NAME ?> &bull; <?= APP_ORG ?></div>
            <div class="text-[10px] text-slate-400">Developed by BCA Students &bull; Siddharth Nagar</div>
        </footer>

    </div>

    <!-- Fallback / Redirect Modal (Shows if app doesn't switch automatically) -->
    <div id="upiModal" class="hidden fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-sm w-full text-center shadow-2xl border border-slate-100">
            <div class="w-14 h-14 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <h2 class="text-xl font-black text-slate-900 mb-1">Redirecting to UPI</h2>
            <p class="text-xs text-slate-500 mb-4 leading-relaxed">
                Your customer details have been saved. Opening your UPI application for:
            </p>
            <div class="bg-slate-50 rounded-2xl p-4 mb-5 border border-slate-200/80">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Amount Due</div>
                <div id="modalAmount" class="text-2xl font-black text-slate-900 mt-0.5">₹0.00</div>
                <div class="text-xs text-slate-600 font-medium mt-1"><?= e($restaurant['name'] ?? 'Restaurant') ?></div>
            </div>

            <div class="space-y-2">
                <a id="modalUpiLink" href="#" class="block w-full py-3.5 px-4 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-2xl text-sm transition shadow-md">
                    Open UPI App Now &rarr;
                </a>
                <button type="button" onclick="document.getElementById('upiModal').classList.add('hidden')" class="w-full py-2.5 px-4 text-xs font-semibold text-slate-500 hover:text-slate-800 transition">
                    Back to Form
                </button>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 text-[11px] text-slate-400 leading-tight">
                Please show your payment confirmation screen to the counter staff.
            </div>
        </div>
    </div>

    <!-- Payment JS Script -->
    <script src="js/payment.js"></script>
</body>
</html>
