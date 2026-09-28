        <!-- Admin Page Global Footer Info -->
        <footer class="mt-auto p-4 border-t border-slate-200 text-center text-xs text-slate-400 bg-white">
            <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2">
                <div>
                    <span class="font-bold text-slate-700"><?= APP_NAME ?></span> &bull; <?= APP_ORG ?>
                </div>
                <div class="text-[11px] text-slate-400">
                    BCA Student Project &bull; Siddharth Shikshan Avam Prashikshan Sansthan
                </div>
            </div>
        </footer>
    </main>

    <!-- MOBILE BOTTOM NAVIGATION (Optimized for one-hand operation) -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-white border-t border-slate-200 flex items-center justify-around py-2 shadow-lg">
        <a href="<?= BASE_URL ?>/admin/dashboard.php" class="flex flex-col items-center justify-center flex-1 py-1 <?= $currentPage === 'dashboard.php' ? 'text-brand-600 font-bold' : 'text-slate-500' ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
            <span class="text-[10px] mt-0.5">Overview</span>
        </a>
        <a href="<?= BASE_URL ?>/admin/customers.php" class="flex flex-col items-center justify-center flex-1 py-1 <?= $currentPage === 'customers.php' ? 'text-brand-600 font-bold' : 'text-slate-500' ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            <span class="text-[10px] mt-0.5">Customers</span>
        </a>
        <a href="<?= BASE_URL ?>/admin/settings.php" class="flex flex-col items-center justify-center flex-1 py-1 <?= $currentPage === 'settings.php' ? 'text-brand-600 font-bold' : 'text-slate-500' ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span class="text-[10px] mt-0.5">Settings &amp; QR</span>
        </a>
        <a href="<?= BASE_URL ?>/pay.php?slug=<?= urlencode($currentRestaurant['slug'] ?? 'chaudhary') ?>" target="_blank" class="flex flex-col items-center justify-center flex-1 py-1 text-slate-500 hover:text-brand-600">
            <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
            <span class="text-[10px] mt-0.5 font-medium text-brand-600">Live QR</span>
        </a>
    </nav>
</body>
</html>
