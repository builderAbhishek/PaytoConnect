/**
 * PaytoConnect - Application Layout & Navigation Controller
 * Manages Sidebar & Navbar loading, responsive drawer, desktop collapse, and role-based UI.
 */

const App = (function() {
  let currentRole = 'admin'; // 'admin' or 'restaurant'
  let currentUser = null;

  function initLayout(options = {}) {
    currentRole = options.role || (window.location.pathname.includes('/restaurant/') ? 'restaurant' : 'admin');
    
    // Check session or fallback
    const session = Store.getSession();
    if (session && session.role) {
      currentRole = session.role;
      currentUser = session;
    } else {
      currentUser = currentRole === 'admin' 
        ? { name: 'Admin', title: 'Administrator', role: 'admin' }
        : { name: 'Chaudhary Cafe', title: 'Owner', role: 'restaurant', restaurant_slug: 'chaudhary' };
    }

    loadSidebar(options);
    loadNavbar(options);
  }

  function loadSidebar(options) {
    const container = document.getElementById('sidebar-container');
    if (!container) return;

    // Check if components/sidebar.html can be fetched, or use fallback
    fetch('../components/sidebar.html')
      .then(res => {
        if (!res.ok) throw new Error('Fetch failed');
        return res.text();
      })
      .catch(() => {
        // Direct relative fallback if running from root or file protocol
        return getSidebarTemplate();
      })
      .then(html => {
        container.innerHTML = html;
        setupSidebarInteractions(options);
      });
  }

  function loadNavbar(options) {
    const container = document.getElementById('navbar-container');
    if (!container) return;

    fetch('../components/navbar.html')
      .then(res => {
        if (!res.ok) throw new Error('Fetch failed');
        return res.text();
      })
      .catch(() => {
        return getNavbarTemplate();
      })
      .then(html => {
        container.innerHTML = html;
        setupNavbarInteractions(options);
      });
  }

  function setupSidebarInteractions(options) {
    const sidebar = document.getElementById('main-sidebar');
    const backdrop = document.getElementById('sidebar-backdrop');
    const toggleCollapseBtn = document.getElementById('toggle-collapse-sidebar');
    const closeMobileBtn = document.getElementById('close-mobile-sidebar');
    const adminGroup = document.getElementById('nav-group-admin');
    const restGroup = document.getElementById('nav-group-restaurant');
    const mainContent = document.getElementById('main-content-wrapper');

    // Toggle Role-Specific Navigation Groups
    if (currentRole === 'restaurant') {
      if (adminGroup) adminGroup.classList.add('hidden');
      if (restGroup) restGroup.classList.remove('hidden');
      const nameEl = document.getElementById('user-name-label');
      const roleEl = document.getElementById('user-role-label');
      const avatarEl = document.getElementById('user-avatar-pill');
      if (nameEl) nameEl.textContent = currentUser.name || 'Chaudhary Cafe';
      if (roleEl) roleEl.textContent = 'Owner';
      if (avatarEl) avatarEl.textContent = (currentUser.name || 'C').charAt(0).toUpperCase();
    } else {
      if (adminGroup) adminGroup.classList.remove('hidden');
      if (restGroup) restGroup.classList.add('hidden');
      const nameEl = document.getElementById('user-name-label');
      const roleEl = document.getElementById('user-role-label');
      const avatarEl = document.getElementById('user-avatar-pill');
      if (nameEl) nameEl.textContent = 'Admin';
      if (roleEl) roleEl.textContent = 'Administrator';
      if (avatarEl) avatarEl.textContent = 'A';
    }

    // Set Active Link State
    const activeNavKey = options.activeNav;
    if (activeNavKey) {
      const activeLink = document.querySelector(`[data-nav="${activeNavKey}"]`);
      if (activeLink) {
        activeLink.classList.remove('text-slate-300', 'hover:bg-slate-800/80');
        activeLink.classList.add('bg-indigo-600', 'text-white', 'shadow-md', 'shadow-indigo-600/30');
        const icon = activeLink.querySelector('svg');
        if (icon) icon.classList.add('text-white');
      }
    }

    // Restore Desktop Collapsed State
    const isCollapsed = localStorage.getItem('paytoconnect_sidebar_collapsed') === 'true';
    if (isCollapsed && window.innerWidth >= 768) {
      applyCollapsedState(true);
    }

    // Desktop Collapse Button
    if (toggleCollapseBtn) {
      toggleCollapseBtn.addEventListener('click', () => {
        const currentlyCollapsed = document.body.classList.contains('sidebar-collapsed');
        applyCollapsedState(!currentlyCollapsed);
        localStorage.setItem('paytoconnect_sidebar_collapsed', !currentlyCollapsed);
      });
    }

    // Mobile Close Drawer
    if (closeMobileBtn) {
      closeMobileBtn.addEventListener('click', closeMobileSidebar);
    }
    if (backdrop) {
      backdrop.addEventListener('click', closeMobileSidebar);
    }

    // Logout Button
    const logoutBtn = document.getElementById('logout-sidebar-btn');
    const logoutBtnRest = document.getElementById('logout-sidebar-btn-rest');
    const handleLogout = (e) => {
      e.preventDefault();
      Store.clearSession();
      Toast.info('Logged out successfully.');
      setTimeout(() => {
        window.location.href = '../login.html';
      }, 400);
    };
    if (logoutBtn) logoutBtn.addEventListener('click', handleLogout);
    if (logoutBtnRest) logoutBtnRest.addEventListener('click', handleLogout);
  }

  function applyCollapsedState(collapse) {
    const sidebar = document.getElementById('main-sidebar');
    const mainWrapper = document.getElementById('main-content-wrapper');
    if (!sidebar) return;

    if (collapse) {
      document.body.classList.add('sidebar-collapsed');
      sidebar.classList.remove('md:w-64');
      sidebar.classList.add('md:w-[72px]');
      if (mainWrapper) {
        mainWrapper.classList.remove('md:pl-64');
        mainWrapper.classList.add('md:pl-[72px]');
      }
    } else {
      document.body.classList.remove('sidebar-collapsed');
      sidebar.classList.remove('md:w-[72px]');
      sidebar.classList.add('md:w-64');
      if (mainWrapper) {
        mainWrapper.classList.remove('md:pl-[72px]');
        mainWrapper.classList.add('md:pl-64');
      }
    }
  }

  function setupNavbarInteractions(options) {
    const mobileToggle = document.getElementById('mobile-sidebar-toggle');
    const userBtn = document.getElementById('navbar-user-dropdown-btn');
    const userDropdown = document.getElementById('navbar-user-dropdown');
    const notifBtn = document.getElementById('navbar-notification-btn');
    const notifDropdown = document.getElementById('notification-dropdown');
    const pageTitleEl = document.getElementById('navbar-page-title');
    const breadcrumbEl = document.getElementById('breadcrumb-current');

    if (options.title && pageTitleEl) pageTitleEl.textContent = options.title;
    if (options.breadcrumb && breadcrumbEl) breadcrumbEl.textContent = options.breadcrumb;

    // Populate navbar user info
    const navName = document.getElementById('navbar-user-name');
    const navRole = document.getElementById('navbar-user-role');
    const navAvatar = document.getElementById('navbar-avatar');
    if (currentUser) {
      if (navName) navName.textContent = currentUser.name;
      if (navRole) navRole.textContent = currentUser.title;
      if (navAvatar) navAvatar.textContent = (currentUser.name || 'U').charAt(0).toUpperCase();
    }

    // Mobile Hamburger Open Drawer
    if (mobileToggle) {
      mobileToggle.addEventListener('click', openMobileSidebar);
    }

    // User Dropdown Toggle
    if (userBtn && userDropdown) {
      userBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        userDropdown.classList.toggle('hidden');
        if (notifDropdown) notifDropdown.classList.add('hidden');
      });
    }

    // Notification Dropdown Toggle
    if (notifBtn && notifDropdown) {
      notifBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        notifDropdown.classList.toggle('hidden');
        if (userDropdown) userDropdown.classList.add('hidden');
      });
    }

    // Close dropdowns on outside click
    document.addEventListener('click', () => {
      if (userDropdown) userDropdown.classList.add('hidden');
      if (notifDropdown) notifDropdown.classList.add('hidden');
    });

    // Navbar Logout
    const navLogout = document.getElementById('navbar-logout-btn');
    if (navLogout) {
      navLogout.addEventListener('click', (e) => {
        e.preventDefault();
        Store.clearSession();
        Toast.info('Logged out successfully.');
        setTimeout(() => {
          window.location.href = '../login.html';
        }, 400);
      });
    }
  }

  function openMobileSidebar() {
    const sidebar = document.getElementById('main-sidebar');
    const backdrop = document.getElementById('sidebar-backdrop');
    if (sidebar) {
      sidebar.classList.remove('-translate-x-full');
      sidebar.classList.add('translate-x-0');
    }
    if (backdrop) {
      backdrop.classList.remove('hidden');
      requestAnimationFrame(() => backdrop.classList.remove('opacity-0'));
    }
    document.body.classList.add('overflow-hidden');
  }

  function closeMobileSidebar() {
    const sidebar = document.getElementById('main-sidebar');
    const backdrop = document.getElementById('sidebar-backdrop');
    if (sidebar) {
      sidebar.classList.remove('translate-x-0');
      sidebar.classList.add('-translate-x-full');
    }
    if (backdrop) {
      backdrop.classList.add('opacity-0');
      setTimeout(() => backdrop.classList.add('hidden'), 250);
    }
    document.body.classList.remove('overflow-hidden');
  }

  // Resilient Inline Template Fallbacks (if fetch is blocked by file:// CORS)
  function getSidebarTemplate() {
    return `
    <div id="sidebar-backdrop" class="sidebar-overlay fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-40 hidden md:hidden opacity-0"></div>
    <aside id="main-sidebar" class="sidebar-drawer fixed top-0 bottom-0 left-0 z-40 w-64 md:w-64 bg-slate-900 text-slate-300 flex flex-col border-r border-slate-800 transition-all duration-300 transform -translate-x-full md:translate-x-0">
      <div class="h-16 flex items-center justify-between px-5 border-b border-slate-800/80 shrink-0">
        <a href="../index.html" class="flex items-center gap-3 group">
          <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-sky-400 flex items-center justify-center text-white shadow-md shadow-indigo-500/20 group-hover:scale-105 transition-transform">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
          </div>
          <div class="brand-name flex flex-col">
            <span class="text-sm font-extrabold text-white tracking-tight flex items-center gap-1.5">PaytoConnect <span class="px-1.5 py-0.5 text-[9px] font-bold uppercase rounded bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">MVP</span></span>
            <span class="text-[10px] text-slate-400 font-medium">Scan. Pay. Connect.</span>
          </div>
        </a>
        <button id="close-mobile-sidebar" class="md:hidden text-slate-400 hover:text-white p-1 rounded-lg hover:bg-slate-800 transition-colors" aria-label="Close sidebar"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
      </div>
      <div class="flex-1 overflow-y-auto px-3 py-4 space-y-6">
        <div id="nav-group-admin" class="space-y-4">
          <div>
            <p class="nav-section-title px-3 text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-2">Overview</p>
            <div class="space-y-1">
              <a href="dashboard.html" data-nav="admin-dashboard" data-tooltip="Dashboard" class="nav-item relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-slate-300 hover:bg-slate-800/80 hover:text-white transition-all"><svg class="w-4 h-4 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg><span class="nav-text">Dashboard</span></a>
            </div>
          </div>
          <div>
            <p class="nav-section-title px-3 text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-2">Management</p>
            <div class="space-y-1">
              <a href="restaurants.html" data-nav="admin-restaurants" data-tooltip="Restaurants" class="nav-item relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-slate-300 hover:bg-slate-800/80 hover:text-white transition-all"><svg class="w-4 h-4 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg><span class="nav-text">Restaurants</span></a>
              <a href="customers.html" data-nav="admin-customers" data-tooltip="Customers" class="nav-item relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-slate-300 hover:bg-slate-800/80 hover:text-white transition-all"><svg class="w-4 h-4 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg><span class="nav-text">Customers</span></a>
            </div>
          </div>
          <div>
            <p class="nav-section-title px-3 text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-2">Account</p>
            <div class="space-y-1">
              <a href="dashboard.html" data-tooltip="Settings" class="nav-item relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-slate-300 hover:bg-slate-800/80 hover:text-white transition-all"><svg class="w-4 h-4 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg><span class="nav-text">Settings</span></a>
              <a href="../login.html" id="logout-sidebar-btn" data-tooltip="Logout" class="nav-item relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-rose-400 hover:bg-rose-500/10 hover:text-rose-300 transition-all"><svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg><span class="nav-text">Logout</span></a>
            </div>
          </div>
        </div>
        <div id="nav-group-restaurant" class="space-y-4 hidden">
          <div>
            <p class="nav-section-title px-3 text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-2">Overview</p>
            <div class="space-y-1">
              <a href="dashboard.html" data-nav="rest-dashboard" data-tooltip="Dashboard" class="nav-item relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-slate-300 hover:bg-slate-800/80 hover:text-white transition-all"><svg class="w-4 h-4 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg><span class="nav-text">Dashboard</span></a>
            </div>
          </div>
          <div>
            <p class="nav-section-title px-3 text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-2">Customers</p>
            <div class="space-y-1">
              <a href="customers.html" data-nav="rest-customers" data-tooltip="Customers" class="nav-item relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-slate-300 hover:bg-slate-800/80 hover:text-white transition-all"><svg class="w-4 h-4 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg><span class="nav-text">Customers</span></a>
              <a href="analytics.html" data-nav="rest-analytics" data-tooltip="Analytics" class="nav-item relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-slate-300 hover:bg-slate-800/80 hover:text-white transition-all"><svg class="w-4 h-4 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg><span class="nav-text">Analytics</span></a>
            </div>
          </div>
          <div>
            <p class="nav-section-title px-3 text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-2">QR & Standee</p>
            <div class="space-y-1">
              <a href="qr.html" data-nav="rest-qr" data-tooltip="My QR" class="nav-item relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-slate-300 hover:bg-slate-800/80 hover:text-white transition-all"><svg class="w-4 h-4 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg><span class="nav-text">My QR</span></a>
            </div>
          </div>
          <div>
            <p class="nav-section-title px-3 text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-2">Settings</p>
            <div class="space-y-1">
              <a href="settings.html" data-nav="rest-settings" data-tooltip="Restaurant Settings" class="nav-item relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-slate-300 hover:bg-slate-800/80 hover:text-white transition-all"><svg class="w-4 h-4 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg><span class="nav-text">Restaurant Settings</span></a>
              <a href="../login.html" id="logout-sidebar-btn-rest" data-tooltip="Logout" class="nav-item relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-rose-400 hover:bg-rose-500/10 hover:text-rose-300 transition-all"><svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg><span class="nav-text">Logout</span></a>
            </div>
          </div>
        </div>
      </div>
      <div class="p-3 border-t border-slate-800 bg-slate-950/40">
        <div class="flex items-center justify-between p-2 rounded-xl bg-slate-800/40 border border-slate-700/50">
          <div class="flex items-center gap-3 min-w-0">
            <div id="user-avatar-pill" class="w-8 h-8 rounded-lg bg-indigo-600 text-white font-bold flex items-center justify-center text-xs shrink-0">A</div>
            <div class="user-info truncate">
              <p id="user-name-label" class="text-xs font-bold text-white truncate">Admin</p>
              <p id="user-role-label" class="text-[10px] text-slate-400 truncate">Administrator</p>
            </div>
          </div>
          <button id="toggle-collapse-sidebar" class="hidden md:flex text-slate-400 hover:text-white p-1 rounded-lg hover:bg-slate-700 transition-colors" title="Collapse sidebar" aria-label="Toggle sidebar collapse"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" /></svg></button>
        </div>
      </div>
    </aside>`;
  }

  function getNavbarTemplate() {
    return `
    <header id="main-navbar" class="h-16 bg-white border-b border-slate-200/80 sticky top-0 z-30 flex items-center justify-between px-4 sm:px-6">
      <div class="flex items-center gap-3">
        <button id="mobile-sidebar-toggle" class="md:hidden text-slate-600 hover:text-slate-900 p-2 rounded-xl hover:bg-slate-100 transition-colors" aria-label="Open sidebar menu"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg></button>
        <div>
          <div class="flex items-center gap-1.5 text-[11px] font-semibold text-slate-600 mb-0.5" id="nav-breadcrumb"><span>Home</span><span>/</span><span class="text-indigo-600 font-bold" id="breadcrumb-current">Overview</span></div>
          <h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight" id="navbar-page-title">Overview</h1>
        </div>
      </div>
      <div class="flex items-center gap-2 sm:gap-3">
        <div class="relative hidden sm:block">
          <input type="text" id="navbar-quick-search" placeholder="Quick search..." class="w-44 lg:w-56 pl-9 pr-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all">
          <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
        </div>
        <div class="relative">
          <button id="navbar-notification-btn" class="p-2 text-slate-500 hover:text-slate-800 rounded-xl hover:bg-slate-100 transition-colors relative" aria-label="View notifications">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
            <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-indigo-600 ring-2 ring-white"></span>
          </button>
          <div id="notification-dropdown" class="hidden absolute right-0 mt-2 w-72 bg-white rounded-2xl shadow-xl border border-slate-100 p-3 z-50">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100 mb-2">
              <span class="text-xs font-bold text-slate-900">Notifications</span>
              <span class="text-[10px] font-semibold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full">2 New</span>
            </div>
            <div class="space-y-2 text-xs">
              <div class="p-2 rounded-lg bg-slate-50 border border-slate-100">
                <p class="font-semibold text-slate-800">New customer check-in</p>
                <p class="text-[11px] text-slate-500">Rahul Verma paid ₹480 via UPI.</p>
                <span class="text-[10px] text-slate-400 mt-1 block">5 mins ago</span>
              </div>
              <div class="p-2 rounded-lg hover:bg-slate-50 transition-colors">
                <p class="font-semibold text-slate-800">Standee QR generated</p>
                <p class="text-[11px] text-slate-500">Counter QR ready for high-res printing.</p>
                <span class="text-[10px] text-slate-400 mt-1 block">1 hour ago</span>
              </div>
            </div>
          </div>
        </div>
        <div class="relative">
          <button id="navbar-user-dropdown-btn" class="flex items-center gap-2.5 p-1.5 rounded-xl hover:bg-slate-100 transition-colors">
            <div id="navbar-avatar" class="w-8 h-8 rounded-xl bg-indigo-600 text-white font-bold flex items-center justify-center text-xs">A</div>
            <div class="hidden sm:block text-left">
              <p id="navbar-user-name" class="text-xs font-bold text-slate-800 leading-tight">Admin</p>
              <p id="navbar-user-role" class="text-[10px] text-slate-400 font-medium">Administrator</p>
            </div>
            <svg class="w-4 h-4 text-slate-400 hidden sm:block" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
          </button>
          <div id="navbar-user-dropdown" class="hidden absolute right-0 mt-2 w-52 bg-white rounded-2xl shadow-xl border border-slate-100 py-1.5 z-50">
            <a href="#" class="flex items-center gap-2 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors"><svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>Account Settings</a>
            <a href="../scan.html?restaurant=chaudhary" target="_blank" class="flex items-center gap-2 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors"><svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>Customer Pay Page</a>
            <div class="my-1 border-t border-slate-100"></div>
            <a href="../login.html" id="navbar-logout-btn" class="flex items-center gap-2 px-4 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition-colors"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>Logout</a>
          </div>
        </div>
      </div>
    </header>`;
  }

  return {
    init: initLayout,
    openMobileSidebar: openMobileSidebar,
    closeMobileSidebar: closeMobileSidebar
  };
})();
