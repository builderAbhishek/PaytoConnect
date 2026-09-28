<?php
/**
 * SMART QR - Public Product Landing Page
 * Developed by BCA Students | Siddharth Shikshan Avam Prashikshan Sansthan
 * Mentored by Abhishek Chaudhary | ALAG INNOVATIVE SOLUTIONS
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';

$demoRestaurant = get_restaurant_by_slug('chaudhary');
$demoSlug = $demoRestaurant ? $demoRestaurant['slug'] : 'chaudhary';
$demoPayUrl = BASE_URL . '/pay.php?slug=' . urlencode($demoSlug);
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth bg-white text-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= APP_NAME ?> - Scan. Pay. Connect. | <?= APP_ORG ?></title>
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
                            950: '#020617'
                        }
                    }
                }
            }
        }
    </script>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="antialiased selection:bg-brand-500 selection:text-white">

    <!-- GLOBAL NAVBAR -->
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-18 flex items-center justify-between">
            
            <!-- Logo & Brand -->
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-600 to-amber-500 flex items-center justify-center text-white shadow-sm font-black text-lg">
                    SQ
                </div>
                <div>
                    <span class="text-lg font-black tracking-tight text-slate-900 block leading-tight">SMART QR</span>
                    <span class="text-[10px] font-bold tracking-widest text-slate-400 uppercase">ALAG INNOVATIVE</span>
                </div>
            </div>

            <!-- Desktop Links -->
            <nav class="hidden md:flex items-center space-x-7 text-xs font-bold text-slate-600 uppercase tracking-wider">
                <a href="#problem" class="hover:text-brand-600 transition">The Problem</a>
                <a href="#solution" class="hover:text-brand-600 transition">The Solution</a>
                <a href="#features" class="hover:text-brand-600 transition">Features</a>
                <a href="#how-it-works" class="hover:text-brand-600 transition">How It Works</a>
                <a href="#benefits" class="hover:text-brand-600 transition">For Restaurants</a>
                <a href="#students" class="hover:text-brand-600 transition">BCA Team</a>
            </nav>

            <!-- Action CTAs -->
            <div class="flex items-center gap-2.5">
                <a href="<?= BASE_URL ?>/admin/login.php" class="px-3.5 py-2 text-xs font-bold text-slate-700 hover:text-brand-600 transition">
                    Admin Portal
                </a>
                <a href="<?= e($demoPayUrl) ?>" class="px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-bold shadow-xs hover:shadow transition flex items-center gap-1.5">
                    <span>Try Live Demo</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>

        </div>
    </header>

    <!-- HERO SECTION -->
    <section class="relative pt-12 pb-16 md:pt-20 md:pb-28 overflow-hidden bg-gradient-to-b from-orange-50/50 via-white to-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                
                <!-- Hero Left: Typography & CTAs -->
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-orange-100/80 border border-orange-200/60 text-brand-700 text-xs font-extrabold uppercase tracking-wide">
                        <span class="w-2 h-2 rounded-full bg-brand-600 animate-pulse"></span>
                        <span>SMART QR for Restaurants</span>
                    </div>

                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-slate-900 tracking-tight leading-[1.08]">
                        Scan. Pay. <span class="text-brand-600">Connect.</span>
                    </h1>

                    <p class="text-lg sm:text-xl font-bold text-slate-700">
                        "Your QR Does More Than Take Payments."
                    </p>

                    <p class="text-sm sm:text-base text-slate-500 max-w-xl mx-auto lg:mx-0 leading-relaxed font-normal">
                        A simple QR-based system that helps restaurants capture customer information before payment, understand their audience, and build long-term customer relationships.
                    </p>

                    <div class="pt-2 flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-3">
                        <a href="<?= e($demoPayUrl) ?>" class="w-full sm:w-auto px-7 py-4 bg-brand-600 hover:bg-brand-700 active:scale-[0.99] text-white font-extrabold rounded-2xl text-sm shadow-md shadow-brand-500/20 transition flex items-center justify-center gap-2">
                            <span>Try Live Demo</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </a>
                        <a href="#benefits" class="w-full sm:w-auto px-6 py-4 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold rounded-2xl text-sm transition text-center">
                            For Restaurants
                        </a>
                    </div>

                    <!-- Trust Points -->
                    <div class="pt-4 flex flex-wrap items-center justify-center lg:justify-start gap-y-2 gap-x-6 text-xs text-slate-500 font-medium">
                        <div class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>No payment gateway fees</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Uses existing UPI ID</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>100% Consent compliant</span>
                        </div>
                    </div>

                </div>

                <!-- Hero Right: Realistic Mobile Mockup -->
                <div class="lg:col-span-5 flex justify-center">
                    <div class="relative w-full max-w-[340px]">
                        
                        <!-- Phone Frame Outline -->
                        <div class="bg-slate-900 p-3 rounded-[42px] shadow-2xl border-4 border-slate-800">
                            <!-- Speaker & Camera Notch -->
                            <div class="w-32 h-4 bg-slate-900 mx-auto rounded-b-xl flex items-center justify-center mb-1">
                                <div class="w-12 h-1 bg-slate-700 rounded-full"></div>
                                <div class="w-2.5 h-2.5 rounded-full bg-slate-800 ml-2"></div>
                            </div>

                            <!-- Mobile Screen Content (Chaudhary Cafe Payment Page Preview) -->
                            <div class="bg-white rounded-[32px] overflow-hidden p-4 space-y-3.5 border border-slate-100 text-left">
                                
                                <!-- Cafe Header -->
                                <div class="text-center pt-2 pb-1 border-b border-slate-100">
                                    <div class="w-12 h-12 rounded-xl bg-brand-600 text-white flex items-center justify-center font-black mx-auto mb-2 text-base shadow-xs">
                                        CC
                                    </div>
                                    <div class="font-extrabold text-sm text-slate-900 leading-tight">Chaudhary Cafe &amp; Family Restaurant</div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">Gadakul, Shohratgarh, Siddharthnagar</div>
                                    <div class="mt-1 text-[9px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full inline-block">
                                        Pay Your Bill Securely
                                    </div>
                                </div>

                                <!-- Form Preview -->
                                <div class="space-y-2.5 text-xs">
                                    <div>
                                        <div class="text-[10px] font-bold text-slate-500 uppercase">Your Name</div>
                                        <div class="mt-1 p-2 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 font-semibold text-[11px]">
                                            Rahul Sharma
                                        </div>
                                    </div>

                                    <div>
                                        <div class="text-[10px] font-bold text-slate-500 uppercase">Mobile Number</div>
                                        <div class="mt-1 p-2 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 font-mono text-[11px]">
                                            +91 98123 45621
                                        </div>
                                    </div>

                                    <div>
                                        <div class="text-[10px] font-bold text-slate-500 uppercase">You are</div>
                                        <div class="grid grid-cols-2 gap-2 mt-1">
                                            <div class="p-1.5 text-center rounded-xl bg-orange-50 border border-brand-500 text-brand-700 font-bold text-[10px]">
                                                ● Local Resident
                                            </div>
                                            <div class="p-1.5 text-center rounded-xl border border-slate-200 text-slate-400 text-[10px]">
                                                ○ Outsider
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <div class="text-[10px] font-bold text-slate-500 uppercase">Bill Amount</div>
                                        <div class="mt-1 p-2 rounded-xl border border-slate-200 bg-slate-50 text-slate-900 font-black text-sm">
                                            ₹ 480.00
                                        </div>
                                    </div>

                                    <div class="flex items-start gap-1.5 pt-1">
                                        <div class="w-3.5 h-3.5 rounded bg-brand-600 text-white flex items-center justify-center text-[9px] font-bold shrink-0 mt-0.5">✓</div>
                                        <div class="text-[9px] text-slate-500 leading-tight">
                                            I agree to receive offers and updates from this restaurant.
                                        </div>
                                    </div>

                                    <!-- Pay Button Mock -->
                                    <div class="pt-1">
                                        <a href="<?= e($demoPayUrl) ?>" class="block w-full py-2.5 text-center bg-brand-600 text-white rounded-xl font-black text-xs shadow-sm hover:bg-brand-700 transition">
                                            PAY NOW &rarr;
                                        </a>
                                    </div>
                                    <div class="text-center text-[9px] text-slate-400">
                                        Redirects to Google Pay / PhonePe / Paytm
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- PROBLEM SECTION -->
    <section id="problem" class="py-16 md:py-24 bg-slate-50 border-y border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="max-w-3xl mx-auto text-center space-y-3 mb-12">
                <span class="text-xs font-black tracking-widest text-red-600 uppercase">The Reality Today</span>
                <h2 class="text-3xl sm:text-4xl font-black tracking-tight text-slate-900">
                    Traditional QR only collects payment.
                </h2>
                <p class="text-sm sm:text-base text-slate-500">
                    Millions of diners visit local restaurants every day, but owners remain completely blind to who their customers are.
                </p>
            </div>

            <!-- Traditional Flow Breakdown -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center max-w-4xl mx-auto">
                
                <!-- Flow Diagram -->
                <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-xs space-y-4 text-center">
                    <div class="text-xs font-bold uppercase tracking-wider text-slate-400">Traditional Silent Flow</div>
                    
                    <div class="space-y-3 font-bold text-sm">
                        <div class="p-3 bg-slate-100 text-slate-800 rounded-xl">Scan Static QR</div>
                        <div class="text-slate-400 text-lg">&darr;</div>
                        <div class="p-3 bg-slate-100 text-slate-800 rounded-xl">Pay in UPI App</div>
                        <div class="text-slate-400 text-lg">&darr;</div>
                        <div class="p-3 bg-slate-800 text-white rounded-xl">Transaction Complete</div>
                    </div>
                    <div class="text-[11px] text-red-600 font-bold pt-2">
                        Zero Customer Relationship Created
                    </div>
                </div>

                <!-- Missing Questions -->
                <div class="space-y-3">
                    <div class="font-extrabold text-base text-slate-800 mb-2">
                        What the restaurant never knows:
                    </div>

                    <div class="flex items-start gap-3 p-3.5 bg-white rounded-2xl border border-slate-200">
                        <div class="w-6 h-6 rounded-full bg-red-100 text-red-600 flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">&times;</div>
                        <div>
                            <div class="font-bold text-slate-900 text-xs">Who visited your restaurant?</div>
                            <div class="text-[11px] text-slate-500">No name, no verified contact record, no memory.</div>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-3.5 bg-white rounded-2xl border border-slate-200">
                        <div class="w-6 h-6 rounded-full bg-red-100 text-red-600 flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">&times;</div>
                        <div>
                            <div class="font-bold text-slate-900 text-xs">Are they Local or Highway Outsiders?</div>
                            <div class="text-[11px] text-slate-500">Cannot distinguish regular neighbors from one-time road travelers.</div>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-3.5 bg-white rounded-2xl border border-slate-200">
                        <div class="w-6 h-6 rounded-full bg-red-100 text-red-600 flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">&times;</div>
                        <div>
                            <div class="font-bold text-slate-900 text-xs">How many customers returned this month?</div>
                            <div class="text-[11px] text-slate-500">No database to track repeat dining trends or loyal patrons.</div>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-3.5 bg-white rounded-2xl border border-slate-200">
                        <div class="w-6 h-6 rounded-full bg-red-100 text-red-600 flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">&times;</div>
                        <div>
                            <div class="font-bold text-slate-900 text-xs">Who agreed to receive offers?</div>
                            <div class="text-[11px] text-slate-500">No permission recorded to notify them about festive specials.</div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- SOLUTION SECTION -->
    <section id="solution" class="py-16 md:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="max-w-3xl mx-auto text-center space-y-3 mb-14">
                <span class="text-xs font-black tracking-widest text-brand-600 uppercase">The Smart QR Upgrade</span>
                <h2 class="text-3xl sm:text-4xl font-black tracking-tight text-slate-900">
                    Turn Your Payment QR Into a Customer Connection.
                </h2>
                <p class="text-sm sm:text-base text-slate-500">
                    One simple step added between Scan and UPI redirect unlocks your most valuable asset: your own customer database.
                </p>
            </div>

            <!-- Solution Comparison Flow -->
            <div class="max-w-4xl mx-auto bg-gradient-to-r from-orange-500 to-amber-500 p-1 rounded-3xl shadow-xl">
                <div class="bg-white p-6 sm:p-10 rounded-[22px]">
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-center">
                        
                        <div class="p-4 rounded-2xl bg-orange-50/70 border border-orange-100">
                            <div class="w-10 h-10 rounded-xl bg-brand-600 text-white font-black text-sm flex items-center justify-center mx-auto mb-2">01</div>
                            <div class="font-black text-slate-900 text-sm">Scan QR</div>
                            <div class="text-[11px] text-slate-500 mt-1">Guest scans unique restaurant table QR</div>
                        </div>

                        <div class="p-4 rounded-2xl bg-orange-500 text-white shadow-md">
                            <div class="w-10 h-10 rounded-xl bg-white text-brand-600 font-black text-sm flex items-center justify-center mx-auto mb-2">02</div>
                            <div class="font-black text-white text-sm">Enter Details</div>
                            <div class="text-[11px] text-orange-100 mt-1">Name, 10-digit phone, local vs outsider</div>
                        </div>

                        <div class="p-4 rounded-2xl bg-orange-50/70 border border-orange-100">
                            <div class="w-10 h-10 rounded-xl bg-brand-600 text-white font-black text-sm flex items-center justify-center mx-auto mb-2">03</div>
                            <div class="font-black text-slate-900 text-sm">Pay via UPI</div>
                            <div class="text-[11px] text-slate-500 mt-1">One tap redirect to GPay, PhonePe, Paytm</div>
                        </div>

                        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200">
                            <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white font-black text-sm flex items-center justify-center mx-auto mb-2">04</div>
                            <div class="font-black text-emerald-900 text-sm">Data Saved</div>
                            <div class="text-[11px] text-emerald-700 mt-1">Saved securely in restaurant database</div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- 4 CORE FEATURES -->
    <section id="features" class="py-16 md:py-24 bg-slate-50 border-t border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="max-w-3xl mx-auto text-center space-y-3 mb-14">
                <span class="text-xs font-black tracking-widest text-slate-400 uppercase">Engineered for Bharat</span>
                <h2 class="text-3xl sm:text-4xl font-black tracking-tight text-slate-900">
                    Everything Local Outlets Need. Nothing They Don't.
                </h2>
                <p class="text-sm sm:text-base text-slate-500">
                    Designed to work with zero friction on any smartphone screen with zero hardware changes.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                
                <!-- Feature 1 -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-3 flex flex-col justify-between hover:border-brand-500 transition">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-orange-100 text-brand-600 flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <h3 class="text-base font-black text-slate-900">1. Know Your Customers</h3>
                        <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                            Capture basic verified customer details (name and phone number) before payment takes place.
                        </p>
                    </div>
                    <div class="text-[11px] font-bold text-brand-600 pt-2 border-t border-slate-100">
                        Zero friction form &rarr;
                    </div>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-3 flex flex-col justify-between hover:border-blue-500 transition">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>
                        <h3 class="text-base font-black text-slate-900">2. Understand Your Audience</h3>
                        <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                            See Local vs Outsider customer distribution in real-time. Know how much revenue comes from highway travelers vs regulars.
                        </p>
                    </div>
                    <div class="text-[11px] font-bold text-blue-600 pt-2 border-t border-slate-100">
                        Demographic clarity &rarr;
                    </div>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-3 flex flex-col justify-between hover:border-emerald-500 transition">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <h3 class="text-base font-black text-slate-900">3. Build Customer Relationships</h3>
                        <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                            Record explicit consent for marketing updates. Build a clean, privacy-conscious customer list for future announcements.
                        </p>
                    </div>
                    <div class="text-[11px] font-bold text-emerald-600 pt-2 border-t border-slate-100">
                        Consent-based records &rarr;
                    </div>
                </div>

                <!-- Feature 4 -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-3 flex flex-col justify-between hover:border-purple-500 transition">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-purple-100 text-purple-600 flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        </div>
                        <h3 class="text-base font-black text-slate-900">4. Simple UPI Payment</h3>
                        <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                            Customers continue using their favorite UPI apps without installing anything or paying convenience fees.
                        </p>
                    </div>
                    <div class="text-[11px] font-bold text-purple-600 pt-2 border-t border-slate-100">
                        Native UPI intent &rarr;
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- HOW IT WORKS (Visually clear on mobile) -->
    <section id="how-it-works" class="py-16 md:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="max-w-3xl mx-auto text-center space-y-3 mb-14">
                <span class="text-xs font-black tracking-widest text-brand-600 uppercase">Seamless Customer Experience</span>
                <h2 class="text-3xl sm:text-4xl font-black tracking-tight text-slate-900">
                    How It Works in 4 Steps
                </h2>
                <p class="text-sm sm:text-base text-slate-500">
                    Completed comfortably with one hand in under 15 seconds.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                
                <div class="bg-slate-50 p-6 rounded-3xl border border-slate-200/80 text-center space-y-3">
                    <span class="text-4xl font-black text-brand-600">01</span>
                    <h3 class="text-base font-black text-slate-900">Scan QR</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Customer scans the counter standee QR code with phone camera or QR scanner.
                    </p>
                </div>

                <div class="bg-slate-50 p-6 rounded-3xl border border-slate-200/80 text-center space-y-3">
                    <span class="text-4xl font-black text-brand-600">02</span>
                    <h3 class="text-base font-black text-slate-900">Fill Details</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Customer enters Name, 10-digit mobile number, selects Local/Outsider and bill amount.
                    </p>
                </div>

                <div class="bg-slate-50 p-6 rounded-3xl border border-slate-200/80 text-center space-y-3">
                    <span class="text-4xl font-black text-brand-600">03</span>
                    <h3 class="text-base font-black text-slate-900">Pay via UPI</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Taps "PAY NOW". Browser automatically opens GPay, PhonePe, or Paytm with exact bill amount.
                    </p>
                </div>

                <div class="bg-slate-50 p-6 rounded-3xl border border-slate-200/80 text-center space-y-3">
                    <span class="text-4xl font-black text-emerald-600">04</span>
                    <h3 class="text-base font-black text-slate-900">Data Saved</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Customer details are recorded in the restaurant's private MySQL database instantly.
                    </p>
                </div>

            </div>

        </div>
    </section>

    <!-- RESTAURANT BENEFITS SECTION -->
    <section id="benefits" class="py-16 md:py-24 bg-slate-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="max-w-3xl mx-auto text-center space-y-3 mb-14">
                <span class="text-xs font-black tracking-widest text-brand-500 uppercase">For Restaurant Owners</span>
                <h2 class="text-3xl sm:text-4xl font-black tracking-tight text-white">
                    Built for Real-World Indian Hospitality
                </h2>
                <p class="text-sm sm:text-base text-slate-400">
                    Keep your money flowing directly to your existing UPI while creating a powerful data asset.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                
                <div class="p-5 rounded-2xl bg-white/5 border border-white/10 space-y-1.5">
                    <div class="text-brand-400 font-bold text-sm">Customer Database</div>
                    <div class="text-xs text-slate-400">Build your own private patron directory without third-party aggregator lock-in.</div>
                </div>

                <div class="p-5 rounded-2xl bg-white/5 border border-white/10 space-y-1.5">
                    <div class="text-brand-400 font-bold text-sm">Local vs Outsider Insights</div>
                    <div class="text-xs text-slate-400">Understand your customer demographic ratio to refine pricing and special combos.</div>
                </div>

                <div class="p-5 rounded-2xl bg-white/5 border border-white/10 space-y-1.5">
                    <div class="text-brand-400 font-bold text-sm">Daily Customer Reports</div>
                    <div class="text-xs text-slate-400">Live dashboard tracking today's visitor footfall and total UPI bill amounts.</div>
                </div>

                <div class="p-5 rounded-2xl bg-white/5 border border-white/10 space-y-1.5">
                    <div class="text-brand-400 font-bold text-sm">Searchable Customer Records</div>
                    <div class="text-xs text-slate-400">Search past customers instantly by name or mobile number with date filters.</div>
                </div>

                <div class="p-5 rounded-2xl bg-white/5 border border-white/10 space-y-1.5">
                    <div class="text-brand-400 font-bold text-sm">1-Click CSV Export</div>
                    <div class="text-xs text-slate-400">Download formatted Excel-compatible CSV reports filtered by date and consent.</div>
                </div>

                <div class="p-5 rounded-2xl bg-white/5 border border-white/10 space-y-1.5">
                    <div class="text-brand-400 font-bold text-sm">Consent Tracking</div>
                    <div class="text-xs text-slate-400">Strictly track which diners have opted in for festive promotions and discounts.</div>
                </div>

                <div class="p-5 rounded-2xl bg-white/5 border border-white/10 space-y-1.5">
                    <div class="text-brand-400 font-bold text-sm">Restaurant-Specific QR</div>
                    <div class="text-xs text-slate-400">Unique branded slug and custom printable counter standee card for each outlet.</div>
                </div>

                <div class="p-5 rounded-2xl bg-white/5 border border-white/10 space-y-1.5">
                    <div class="text-brand-400 font-bold text-sm">Existing UPI Workflow</div>
                    <div class="text-xs text-slate-400">No bank changes, no waiting for payouts; money arrives instantly into your UPI ID.</div>
                </div>

            </div>

        </div>
    </section>

    <!-- DASHBOARD PREVIEW SECTION -->
    <section class="py-16 md:py-24 bg-slate-50 border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="max-w-3xl mx-auto text-center space-y-3 mb-12">
                <span class="text-xs font-black tracking-widest text-slate-500 uppercase">Live Admin Intelligence</span>
                <h2 class="text-3xl sm:text-4xl font-black tracking-tight text-slate-900">
                    Clean, Actionable Dashboard
                </h2>
                <p class="text-sm sm:text-base text-slate-500">
                    Restaurant owners and managers get real-time metrics without complex enterprise software.
                </p>
                <div class="inline-block px-3 py-1 bg-amber-100 text-amber-900 rounded-full text-xs font-bold">
                    Demo Snapshot: Chaudhary Cafe &amp; Family Restaurant
                </div>
            </div>

            <!-- Realistic Dashboard Preview Container -->
            <div class="max-w-5xl mx-auto bg-white rounded-3xl border border-slate-200 shadow-md p-6 sm:p-8 space-y-6">
                
                <!-- Outlet Title -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 gap-2">
                    <div>
                        <div class="text-xs font-bold uppercase text-slate-400">Outlet Overview</div>
                        <div class="text-xl font-black text-slate-900">CHAUDHARY CAFE &amp; FAMILY RESTAURANT</div>
                        <div class="text-xs text-slate-500">Gadakul, Shohratgarh, Siddharthnagar &bull; UPI: 9137470661@superyes</div>
                    </div>
                    <span class="self-start sm:self-center px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold">
                        Live System
                    </span>
                </div>

                <!-- 4 Metrics Row -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="p-4 rounded-2xl bg-orange-50/60 border border-orange-100">
                        <div class="text-[11px] font-bold text-slate-500 uppercase">Today's Customers</div>
                        <div class="text-2xl sm:text-3xl font-black text-brand-600 mt-1">87</div>
                        <div class="text-[10px] text-slate-400">Patrons recorded</div>
                    </div>
                    <div class="p-4 rounded-2xl bg-blue-50/60 border border-blue-100">
                        <div class="text-[11px] font-bold text-slate-500 uppercase">Local</div>
                        <div class="text-2xl sm:text-3xl font-black text-blue-600 mt-1">52</div>
                        <div class="text-[10px] text-slate-400">Area residents</div>
                    </div>
                    <div class="p-4 rounded-2xl bg-purple-50/60 border border-purple-100">
                        <div class="text-[11px] font-bold text-slate-500 uppercase">Outsider</div>
                        <div class="text-2xl sm:text-3xl font-black text-purple-600 mt-1">35</div>
                        <div class="text-[10px] text-slate-400">Highway visitors</div>
                    </div>
                    <div class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-100">
                        <div class="text-[11px] font-bold text-slate-500 uppercase">Total Bill Amount</div>
                        <div class="text-2xl sm:text-3xl font-black text-emerald-600 mt-1">₹14,280</div>
                        <div class="text-[10px] text-slate-400">Routed via UPI</div>
                    </div>
                </div>

                <!-- Recent Customers Snippet -->
                <div>
                    <div class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Recent Captured Transactions</div>
                    <div class="space-y-2">
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-3">
                                <span class="font-bold text-slate-900">Rahul Sharma</span>
                                <span class="font-mono text-slate-500 text-[11px]">98XXXXXX21</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">Local</span>
                            </div>
                            <div class="font-black text-slate-900 text-sm">₹ 480.00</div>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-3">
                                <span class="font-bold text-slate-900">Aman Mishra</span>
                                <span class="font-mono text-slate-500 text-[11px]">97XXXXXX42</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-800">Outsider</span>
                            </div>
                            <div class="font-black text-slate-900 text-sm">₹ 720.00</div>
                        </div>
                    </div>
                </div>

                <div class="pt-2 text-center">
                    <a href="<?= BASE_URL ?>/admin/login.php" class="inline-flex items-center gap-2 text-xs font-bold text-brand-600 hover:text-brand-700">
                        <span>Access Full Admin Portal &rarr;</span>
                    </a>
                </div>

            </div>

        </div>
    </section>

    <!-- TECHNOLOGY SECTION -->
    <section class="py-16 md:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="max-w-3xl mx-auto text-center space-y-3 mb-14">
                <span class="text-xs font-black tracking-widest text-slate-400 uppercase">Architecture</span>
                <h2 class="text-3xl sm:text-4xl font-black tracking-tight text-slate-900">
                    Simple, Reliable Technology Stack
                </h2>
                <p class="text-sm sm:text-base text-slate-500">
                    Built without bloated frameworks so BCA students can maintain, debug, and host it anywhere from XAMPP to basic shared hosting.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                
                <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200/80 space-y-2">
                    <div class="text-xs font-bold uppercase tracking-wider text-brand-600">Frontend</div>
                    <div class="text-lg font-black text-slate-900">HTML5 &bull; Tailwind &bull; JS</div>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Pure semantic HTML5, modern Tailwind CSS via CDN, and lightweight Vanilla JavaScript. No heavy npm bundles.
                    </p>
                </div>

                <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200/80 space-y-2">
                    <div class="text-xs font-bold uppercase tracking-wider text-brand-600">Backend</div>
                    <div class="text-lg font-black text-slate-900">PHP 8+ &bull; MySQL &bull; REST</div>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        PDO prepared statements, clean session management, bcrypt authentication, and REST-style JSON APIs.
                    </p>
                </div>

                <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200/80 space-y-2">
                    <div class="text-xs font-bold uppercase tracking-wider text-brand-600">Dashboard</div>
                    <div class="text-lg font-black text-slate-900">Data &bull; UI &bull; Analytics</div>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Live metrics, demographic segmentation, CSV data streaming with UTF-8 BOM, and responsive mobile card views.
                    </p>
                </div>

                <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200/80 space-y-2">
                    <div class="text-xs font-bold uppercase tracking-wider text-brand-600">Deployment &amp; Tools</div>
                    <div class="text-lg font-black text-slate-900">Git &bull; GitHub &bull; Apache</div>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Standard XAMPP local environment, Git version control, Apache `.htaccess` clean routing support, zero-build deploy.
                    </p>
                </div>

            </div>

        </div>
    </section>

    <!-- BCA STUDENT PROJECT SECTION -->
    <section id="students" class="py-16 md:py-24 bg-gradient-to-b from-orange-50/60 to-white border-t border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="max-w-3xl mx-auto text-center space-y-3 mb-14">
                <span class="text-xs font-black tracking-widest text-brand-600 uppercase">Academic Innovation</span>
                <h2 class="text-3xl sm:text-4xl font-black tracking-tight text-slate-900">
                    Built by BCA Students
                </h2>
                <p class="text-base sm:text-lg font-extrabold text-brand-700">
                    Real Project. Real Skills. Real Learning.
                </p>
                <p class="text-xs sm:text-sm text-slate-500 max-w-xl mx-auto leading-relaxed">
                    Developed as an end-to-end real-world industry learning initiative to bridge classroom theory with practical production engineering.
                </p>
            </div>

            <!-- 4 Student Roles Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
                
                <!-- Student 1 -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs space-y-3">
                    <div class="w-10 h-10 rounded-xl bg-orange-100 text-brand-700 font-black text-sm flex items-center justify-center">S1</div>
                    <div>
                        <div class="text-xs font-bold text-slate-400 uppercase">Student 1</div>
                        <div class="text-base font-black text-slate-900">Frontend Developer</div>
                    </div>
                    <ul class="text-xs text-slate-600 space-y-1.5 pt-2 border-t border-slate-100">
                        <li>&bull; Customer payment page</li>
                        <li>&bull; Responsive mobile UI</li>
                        <li>&bull; Form validation</li>
                        <li>&bull; UPI intent integration</li>
                    </ul>
                </div>

                <!-- Student 2 -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs space-y-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 font-black text-sm flex items-center justify-center">S2</div>
                    <div>
                        <div class="text-xs font-bold text-slate-400 uppercase">Student 2</div>
                        <div class="text-base font-black text-slate-900">Backend Developer</div>
                    </div>
                    <ul class="text-xs text-slate-600 space-y-1.5 pt-2 border-t border-slate-100">
                        <li>&bull; PHP 8+ REST APIs</li>
                        <li>&bull; MySQL database architecture</li>
                        <li>&bull; Authentication &amp; hashing</li>
                        <li>&bull; CRUD operations</li>
                    </ul>
                </div>

                <!-- Student 3 -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs space-y-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 font-black text-sm flex items-center justify-center">S3</div>
                    <div>
                        <div class="text-xs font-bold text-slate-400 uppercase">Student 3</div>
                        <div class="text-base font-black text-slate-900">Dashboard Developer</div>
                    </div>
                    <ul class="text-xs text-slate-600 space-y-1.5 pt-2 border-t border-slate-100">
                        <li>&bull; Admin UI &amp; responsive cards</li>
                        <li>&bull; Data visualization</li>
                        <li>&bull; Demographic filters</li>
                        <li>&bull; CSV export streaming</li>
                    </ul>
                </div>

                <!-- Student 4 -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs space-y-3">
                    <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-700 font-black text-sm flex items-center justify-center">S4</div>
                    <div>
                        <div class="text-xs font-bold text-slate-400 uppercase">Student 4</div>
                        <div class="text-base font-black text-slate-900">QA &amp; Deployment</div>
                    </div>
                    <ul class="text-xs text-slate-600 space-y-1.5 pt-2 border-t border-slate-100">
                        <li>&bull; End-to-end testing</li>
                        <li>&bull; Git &amp; GitHub version control</li>
                        <li>&bull; Multi-device verification</li>
                        <li>&bull; Apache &amp; hosting deployment</li>
                    </ul>
                </div>

            </div>

            <!-- Mentor & College Attribution Box -->
            <div class="max-w-3xl mx-auto bg-white p-8 rounded-3xl border border-slate-200 shadow-xs text-center space-y-4">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-bold">
                    <span>Project Leadership &amp; Institutional Mentorship</span>
                </div>
                <div>
                    <h3 class="text-xl font-black text-slate-900">Team Lead &amp; Project Mentor</h3>
                    <p class="text-base font-bold text-brand-600 mt-0.5">Abhishek Chaudhary</p>
                    <p class="text-xs text-slate-500">ALAG INNOVATIVE SOLUTIONS</p>
                </div>
                <div class="pt-3 border-t border-slate-100 text-xs text-slate-600 leading-relaxed">
                    <div class="font-bold text-slate-800">Siddharth Shikshan Avam Prashikshan Sansthan</div>
                    <div>Karaunda-Masina, Siddharth Nagar, Uttar Pradesh</div>
                </div>
            </div>

        </div>
    </section>

    <!-- CALL TO ACTION (CTA) -->
    <section class="py-16 md:py-24 bg-slate-950 text-white text-center">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <h2 class="text-3xl sm:text-5xl font-black tracking-tight leading-tight">
                Make Your Payment QR <span class="text-brand-500">More Useful.</span>
            </h2>
            <p class="text-sm sm:text-base text-slate-400 max-w-xl mx-auto leading-relaxed">
                Start capturing customer information before payment. Give your local cafe the marketing power of top restaurant chains today.
            </p>
            <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="<?= e($demoPayUrl) ?>" class="w-full sm:w-auto px-8 py-4 bg-brand-600 hover:bg-brand-700 active:scale-[0.99] text-white font-extrabold rounded-2xl text-sm shadow-lg shadow-brand-500/25 transition">
                    Try Live Demo &rarr;
                </a>
                <a href="<?= BASE_URL ?>/admin/login.php" class="w-full sm:w-auto px-7 py-4 bg-white/10 hover:bg-white/15 text-white font-bold rounded-2xl text-sm transition border border-white/10">
                    Restaurant Admin Portal
                </a>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-white border-t border-slate-200 py-12 text-slate-600 text-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6 text-center md:text-left">
                
                <div class="space-y-1">
                    <div class="text-base font-black text-slate-900 tracking-tight flex items-center justify-center md:justify-start gap-2">
                        <span class="w-6 h-6 rounded-lg bg-brand-600 text-white flex items-center justify-center text-xs font-black">SQ</span>
                        <span>ALAG INNOVATIVE SOLUTIONS</span>
                    </div>
                    <div class="text-slate-400 font-medium">Ideas &bull; Build &bull; Solve &bull; Grow</div>
                    <div class="text-slate-500 pt-1 font-semibold">Smart QR for Restaurants &bull; Scan. Pay. Connect.</div>
                </div>

                <div class="text-center md:text-right space-y-1 text-slate-500">
                    <div class="font-bold text-slate-800">Built by BCA Students</div>
                    <div>Siddharth Shikshan Avam Prashikshan Sansthan</div>
                    <div class="text-slate-400">Karaunda-Masina, Siddharth Nagar</div>
                </div>

            </div>

            <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-slate-400 text-[11px]">
                <div>
                    &copy; <?= date('Y') ?> ALAG INNOVATIVE SOLUTIONS. Mentored by Abhishek Chaudhary.
                </div>
                <div class="flex items-center gap-4 font-semibold text-slate-500">
                    <a href="<?= e($demoPayUrl) ?>" class="hover:text-brand-600 transition">Customer Pay Demo</a>
                    <span>&bull;</span>
                    <a href="<?= BASE_URL ?>/admin/login.php" class="hover:text-brand-600 transition">Admin Portal</a>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
