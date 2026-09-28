/**
 * SMART QR - Customer Payment Page JavaScript
 * Developed by BCA Students | Siddharth Shikshan Avam Prashikshan Sansthan
 * Mentored by Abhishek Chaudhary | ALAG INNOVATIVE SOLUTIONS
 */

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('paymentForm');
    const payBtn = document.getElementById('payBtn');
    const btnText = document.getElementById('btnText');
    const btnSpinner = document.getElementById('btnSpinner');
    const errorAlert = document.getElementById('errorAlert');
    const errorMessage = document.getElementById('errorMessage');
    const mobileInput = document.getElementById('mobile');
    const amountInput = document.getElementById('bill_amount');
    const upiModal = document.getElementById('upiModal');
    const modalUpiLink = document.getElementById('modalUpiLink');
    const modalAmount = document.getElementById('modalAmount');

    // Auto-clean mobile input: allow only digits and limit to 10 digits
    if (mobileInput) {
        mobileInput.addEventListener('input', function (e) {
            let val = e.target.value.replace(/\D/g, '');
            if (val.length > 10) {
                val = val.substring(0, 10);
            }
            e.target.value = val;
        });
    }

    // Auto-format amount on blur
    if (amountInput) {
        amountInput.addEventListener('blur', function (e) {
            const val = parseFloat(e.target.value);
            if (!isNaN(val) && val > 0) {
                e.target.value = val.toFixed(2);
            }
        });
    }

    function showError(msg) {
        if (errorMessage && errorAlert) {
            errorMessage.textContent = msg;
            errorAlert.classList.remove('hidden');
            errorAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else {
            alert(msg);
        }
    }

    function clearError() {
        if (errorAlert) {
            errorAlert.classList.add('hidden');
        }
    }

    function setSubmitting(isSubmitting) {
        if (payBtn) {
            payBtn.disabled = isSubmitting;
            if (isSubmitting) {
                payBtn.classList.add('opacity-75', 'cursor-not-allowed');
                btnText.textContent = 'Generating UPI Link...';
                if (btnSpinner) btnSpinner.classList.remove('hidden');
            } else {
                payBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                btnText.textContent = 'PAY NOW →';
                if (btnSpinner) btnSpinner.classList.add('hidden');
            }
        }
    }

    if (form) {
        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            clearError();

            const name = document.getElementById('name').value.trim();
            const mobile = document.getElementById('mobile').value.trim();
            const customerType = document.querySelector('input[name="customer_type"]:checked')?.value || 'Local';
            const amount = parseFloat(document.getElementById('bill_amount').value);
            const consent = document.getElementById('marketing_consent')?.checked ? 1 : 0;
            const restaurantSlug = document.getElementById('restaurant_slug')?.value || '';
            const restaurantId = document.getElementById('restaurant_id')?.value || 0;

            // 1. Client-Side Validation
            if (!name) {
                showError('Please enter your full name.');
                document.getElementById('name').focus();
                return;
            }

            const mobilePattern = /^[6-9]\d{9}$/;
            if (!mobilePattern.test(mobile)) {
                showError('Please enter a valid 10-digit Indian mobile number (e.g. 9812345678).');
                document.getElementById('mobile').focus();
                return;
            }

            if (isNaN(amount) || amount <= 0) {
                showError('Please enter a valid bill amount greater than ₹0.');
                document.getElementById('bill_amount').focus();
                return;
            }

            if (amount > 500000) {
                showError('Amount exceeds UPI transaction limit of ₹5,00,000.');
                document.getElementById('bill_amount').focus();
                return;
            }

            // 2. Submit to Server API
            setSubmitting(true);

            try {
                const response = await fetch('api/customer.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        restaurant_slug: restaurantSlug,
                        restaurant_id: restaurantId,
                        name: name,
                        mobile: mobile,
                        customer_type: customerType,
                        bill_amount: amount,
                        marketing_consent: consent
                    })
                });

                const result = await response.json();

                if (!response.ok || !result.success) {
                    showError(result.error || 'Unable to record payment request. Please try again.');
                    setSubmitting(false);
                    return;
                }

                // 3. Success: Customer Data Saved in MySQL!
                // Trigger UPI Intent Deep Link
                const upiUrl = result.upi_intent;

                // Update fallback modal data
                if (modalAmount) modalAmount.textContent = '₹' + amount.toFixed(2);
                if (modalUpiLink) modalUpiLink.href = upiUrl;

                // Show modal to give visual feedback and handle desktop or app fallbacks
                if (upiModal) {
                    upiModal.classList.remove('hidden');
                }

                // Attempt to launch user's UPI app automatically
                window.location.href = upiUrl;

            } catch (err) {
                console.error('Payment Error:', err);
                showError('Network error or server unavailable. Please try again.');
                setSubmitting(false);
            }
        });
    }
});
