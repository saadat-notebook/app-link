// js/register.js (REVISED VERSION 2)
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('register-form');
    const notification = document.getElementById('notification');
    const notificationMessage = document.getElementById('notification-message');
    let notificationTimeout;

    function showNotification(message, isError = true) {
        clearTimeout(notificationTimeout);
        notification.style.backgroundColor = isError ? '#d32f2f' : '#28a745';
        notificationMessage.textContent = message;
        notification.classList.add('show');
        notificationTimeout = setTimeout(() => {
            notification.classList.remove('show');
        }, 4000);
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const button = form.querySelector('.submit-btn');
        const name = document.getElementById('user-name').value.trim();
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm-password').value; // Get confirm password value
        const yearSelect = document.getElementById('academic-year');
        const academicYear = yearSelect.value;
        
        if (!name || !email || !password || !academicYear) {
            showNotification('Please fill in all fields.');
            return;
        }
        if (password.length < 6) {
            showNotification('Password must be at least 6 characters long.');
            return;
        }
        // --- NEW: Check if passwords match ---
        if (password !== confirmPassword) {
            showNotification('Passwords do not match. Please re-enter them.');
            return;
        }
        
        const termsCheckbox = document.getElementById('terms-agree');
if (!termsCheckbox.checked) {
    showNotification('You must agree to the Terms and Conditions to continue.');
    return;
}

        button.disabled = true;
        button.textContent = 'Creating Account...';

        try {
            const response = await fetch('api/register.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ name, email, password, year: academicYear })
            });
            const data = await response.json();
            if (data.success) {
                showNotification('Registration successful! Please log in.', false);
                setTimeout(() => {
                    window.location.href = 'login.php';
                }, 2000);
            } else {
                showNotification(data.message || 'An unknown error occurred.');
                button.disabled = false;
                button.textContent = 'Create Account';
            }
        } catch (error) {
            showNotification('A network error occurred. Please try again.');
            button.disabled = false;
            button.textContent = 'Create Account';
        }
    });

    // --- NEW: Password Visibility Toggle Logic for BOTH fields ---
    document.querySelectorAll('.password-toggle-icon').forEach(icon => {
        icon.addEventListener('click', () => {
            const input = icon.previousElementSibling;
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            // Toggle the icon
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    });
});