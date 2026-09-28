/**
 * SMART QR - Admin Authentication JavaScript
 * Developed by BCA Students | Siddharth Shikshan Avam Prashikshan Sansthan
 * Mentored by Abhishek Chaudhary | ALAG INNOVATIVE SOLUTIONS
 */

document.addEventListener('DOMContentLoaded', function () {
    const loginForm = document.getElementById('loginForm');
    const submitBtn = document.getElementById('loginBtn');
    const btnText = document.getElementById('loginBtnText');
    const btnSpinner = document.getElementById('loginSpinner');
    const alertBox = document.getElementById('loginAlert');
    const alertMsg = document.getElementById('loginAlertMsg');

    function showAlert(msg) {
        if (alertBox && alertMsg) {
            alertMsg.textContent = msg;
            alertBox.classList.remove('hidden');
        } else {
            alert(msg);
        }
    }

    function hideAlert() {
        if (alertBox) {
            alertBox.classList.add('hidden');
        }
    }

    if (loginForm) {
        loginForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            hideAlert();

            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const csrfToken = document.querySelector('input[name="csrf_token"]')?.value || '';

            if (!email || !password) {
                showAlert('Please enter both your email and password.');
                return;
            }

            // Show loading state
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
                if (btnText) btnText.textContent = 'Verifying...';
                if (btnSpinner) btnSpinner.classList.remove('hidden');
            }

            try {
                const response = await fetch('../api/auth.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        email: email,
                        password: password,
                        csrf_token: csrfToken
                    })
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    showAlert(data.error || 'Authentication failed. Please check credentials.');
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                        if (btnText) btnText.textContent = 'Sign In to Dashboard';
                        if (btnSpinner) btnSpinner.classList.add('hidden');
                    }
                    return;
                }

                // Redirect to dashboard
                if (btnText) btnText.textContent = 'Access Granted! Redirecting...';
                window.location.href = data.redirect || 'dashboard.php';

            } catch (err) {
                console.error('Login error:', err);
                showAlert('Server unreachable or network error. Please try again.');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                    if (btnText) btnText.textContent = 'Sign In to Dashboard';
                    if (btnSpinner) btnSpinner.classList.add('hidden');
                }
            }
        });
    }
});
