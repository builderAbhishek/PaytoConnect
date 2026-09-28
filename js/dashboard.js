/**
 * SMART QR - Admin Dashboard JavaScript
 * Developed by BCA Students | Siddharth Shikshan Avam Prashikshan Sansthan
 * Mentored by Abhishek Chaudhary | ALAG INNOVATIVE SOLUTIONS
 */

document.addEventListener('DOMContentLoaded', function () {
    const refreshBtn = document.getElementById('refreshMetricsBtn');

    async function loadMetrics() {
        if (!refreshBtn) return;
        const icon = refreshBtn.querySelector('svg');
        if (icon) icon.classList.add('animate-spin');

        try {
            const res = await fetch('../api/dashboard.php', {
                headers: { 'Accept': 'application/json' }
            });
            if (!res.ok) return;
            const data = await res.json();
            if (data.success && data.metrics) {
                const m = data.metrics;
                const setEl = (id, val) => {
                    const el = document.getElementById(id);
                    if (el) el.textContent = val;
                };

                setEl('metricTodayCustomers', m.today_customers);
                setEl('metricTodayLocal', m.today_local);
                setEl('metricTodayOutsiders', m.today_outsiders);
                setEl('metricTodayBill', m.today_bill_currency);
                setEl('metricTotalCustomers', m.total_customers);
                setEl('metricConsentRate', `${m.consent_percentage}% (${m.consent_count})`);
            }
        } catch (err) {
            console.error('Failed to refresh metrics:', err);
        } finally {
            if (icon) icon.classList.remove('animate-spin');
        }
    }

    if (refreshBtn) {
        refreshBtn.addEventListener('click', loadMetrics);
    }

    // Auto-poll metrics every 60 seconds
    setInterval(loadMetrics, 60000);
});
