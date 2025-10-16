<?php
// reset-password.php
$token = $_GET['token'] ?? '';
if (empty($token) || !ctype_xdigit($token) || strlen($token) !== 64) {
    die("Invalid password reset link.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set New Password - Saadat Notebook</title>

    <script>
        (function () { try { const theme = localStorage.getItem('saadatNotesTheme'); const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches; if (theme === 'dark' || (!theme && systemPrefersDark)) { document.documentElement.classList.add('dark-mode'); } } catch (e) {} })();
    </script>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/register.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .password-wrapper { position: relative; }
        .password-toggle-icon { position: absolute; top: 50%; right: 15px; transform: translateY(-50%); cursor: pointer; color: var(--text-secondary); }
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
            <h2>Create a New Password</h2>
            <p>Your new password must be at least 6 characters long.</p>
        </header>

        <main class="form-card">
            <form id="new-password-form" novalidate>
                <input type="hidden" id="reset-token" value="<?php echo htmlspecialchars($token); ?>">
                <div class="form-group">
                    <label for="new-password">New Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="new-password" required>
                        <i class="fas fa-eye password-toggle-icon"></i>
                    </div>
                </div>
                <div class="form-group">
                    <label for="confirm-password">Confirm New Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="confirm-password" required>
                        <i class="fas fa-eye password-toggle-icon"></i>
                    </div>
                </div>
                <button type="submit" class="submit-btn">Reset Password</button>
            </form>
        </main>
    </div>
    <script src="js/config.js"></script>
    <script>
    // This script remains unchanged and is correct as is.
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('new-password-form');
        const notification = document.getElementById('notification');
        const notificationMessage = document.getElementById('notification-message');
        let notificationTimeout;
        function showNotification(message, isError = true) { clearTimeout(notificationTimeout); notification.style.backgroundColor = isError ? '#d32f2f' : '#28a745'; notificationMessage.textContent = message; notification.classList.add('show'); notificationTimeout = setTimeout(() => notification.classList.remove('show'), 5000); }
        document.querySelectorAll('.password-toggle-icon').forEach(icon => { icon.addEventListener('click', () => { const input = icon.previousElementSibling; const type = input.getAttribute('type') === 'password' ? 'text' : 'password'; input.setAttribute('type', type); icon.classList.toggle('fa-eye'); icon.classList.toggle('fa-eye-slash'); }); });
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const button = form.querySelector('.submit-btn');
            const newPassword = document.getElementById('new-password').value;
            const confirmPassword = document.getElementById('confirm-password').value;
            const token = document.getElementById('reset-token').value;
            if (newPassword.length < 6) { showNotification('Password must be at least 6 characters.'); return; }
            if (newPassword !== confirmPassword) { showNotification('Passwords do not match.'); return; }
            button.disabled = true; button.textContent = 'Saving...';
            try {
                const response = await fetch('api/perform-reset.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ token: token, password: newPassword })
                });
                const data = await response.json();
                if(data.success) {
                    showNotification(data.message, false);
                    button.textContent = 'Password Changed!';
                    setTimeout(() => window.location.href = 'login.php', 3000);
                } else {
                    showNotification(data.message || 'An error occurred.');
                    button.disabled = false; button.textContent = 'Reset Password';
                }
            } catch (error) {
                showNotification('A network error occurred.');
                button.disabled = false; button.textContent = 'Reset Password';
            }
        });
    });
    </script>
</body>
</html>