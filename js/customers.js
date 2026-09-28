/**
 * SMART QR - Customers Filter & Privacy JavaScript
 * Developed by BCA Students | Siddharth Shikshan Avam Prashikshan Sansthan
 * Mentored by Abhishek Chaudhary | ALAG INNOVATIVE SOLUTIONS
 */

document.addEventListener('DOMContentLoaded', function () {
    const dateRangeSelect = document.getElementById('date_range');
    const customDateContainer = document.getElementById('customDateContainer');
    const exportBtn = document.getElementById('exportCsvBtn');
    const filterForm = document.getElementById('filterForm');
    const togglePrivacyBtn = document.getElementById('togglePrivacyBtn');

    // Toggle custom date range fields visibility
    if (dateRangeSelect && customDateContainer) {
        function checkDateRange() {
            if (dateRangeSelect.value === 'custom') {
                customDateContainer.classList.remove('hidden');
            } else {
                customDateContainer.classList.add('hidden');
            }
        }
        dateRangeSelect.addEventListener('change', checkDateRange);
        checkDateRange();
    }

    // CSV Export Handler
    if (exportBtn && filterForm) {
        exportBtn.addEventListener('click', function (e) {
            e.preventDefault();
            const formData = new FormData(filterForm);
            const params = new URLSearchParams(formData);
            window.location.href = `../api/export.php?${params.toString()}`;
        });
    }

    // Privacy Masking Toggle for Phone Numbers
    if (togglePrivacyBtn) {
        let isMasked = true;
        togglePrivacyBtn.addEventListener('click', function () {
            isMasked = !isMasked;
            const phoneSpans = document.querySelectorAll('.customer-phone');
            phoneSpans.forEach(span => {
                const fullPhone = span.getAttribute('data-full');
                const maskedPhone = span.getAttribute('data-masked');
                span.textContent = isMasked ? maskedPhone : fullPhone;
            });

            const btnText = document.getElementById('privacyBtnText');
            if (btnText) {
                btnText.textContent = isMasked ? 'Reveal Numbers' : 'Mask Numbers';
            }
        });
    }
});
