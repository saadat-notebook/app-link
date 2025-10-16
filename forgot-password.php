<?php
// forgot-password.php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: index.php'); // Redirect logged-in users away
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Saadat Notebook</title>
    
    <script>
        (function () { try { const theme = localStorage.getItem('saadatNotesTheme'); const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches; if (theme === 'dark' || (!theme && systemPrefersDark)) { document.documentElement.classList.add('dark-mode'); } } catch (e) {} })();
    </script>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/register.css">
    <style>
        /* This style is now centrally defined in register.css, but including it here won't cause harm */
        .form-switch-link { color: var(--text-primary); font-weight: bold; text-decoration: none; }
    </style>
</head>
<body class="register-body" style="align-items: center; padding-top: 0;">
    <div id="notification" class="notification-bar"><span id="notification-message"></span></div>

    <div class="register-container">
        <header class="register-header">
            <!-- NEW: Added Logo and Site Name -->
            <div class="logo-container">
                <img src="images/logo.png" alt="Saadat Notebook Logo" class="site-logo">
                <div class="logo-text"><h1>Saadat Notebook</h1></div>
            </div>
            <h2>Reset Password</h2>
            <p>Enter your email and we'll send you a link to set a new password.</p>
        </header>

        <main class="form-card">
            <form id="reset-request-form" novalidate>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" placeholder="Enter your email" required>
                </div>
                <button type="submit" class="submit-btn">Send Reset Link</button>
            </form>
            <!-- NEW: Applied styling class to this link -->
            <p style="text-align:center; margin-top:1.5rem;"><a href="login.php" class="form-switch-link">Back to Login</a></p>
        </main>
    </div>
    
    <script src="js/config.js"></script>

    <script>
    // This script remains unchanged, it is correct as is.
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('reset-request-form');
        const notification = document.getElementById('notification');
        const notificationMessage = document.getElementById('notification-message');
        let notificationTimeout;
        function showNotification(message, isError = true) { clearTimeout(notificationTimeout); notification.style.backgroundColor = isError ? '#d32f2f' : '#28a745'; notificationMessage.textContent = message; notification.classList.add('show'); notificationTimeout = setTimeout(() => notification.classList.remove('show'), 5000); }
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const button = form.querySelector('.submit-btn');
            const email = document.getElementById('email').value.trim();
            if (!email) { showNotification('Please enter your email address.'); return; }
            button.disabled = true; button.textContent = 'Sending...';
            try {
                const response = await fetch('${window.API_BASE_URL}/api/request-reset.php', {
                    method: 'POST',
                    credentials: 'include',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email: email })
                });
                const data = await response.json();
                showNotification(data.message, !data.success);
                if (data.success) {
                    button.textContent = 'Link Sent';
                } else {
                     button.disabled = false; button.textContent = 'Send Reset Link';
                }
            } catch (error) {
                showNotification('A network error occurred. Please try again.');
                button.disabled = false; button.textContent = 'Send Reset Link';
            }
        });
    });
    </script>
</body>
</html>