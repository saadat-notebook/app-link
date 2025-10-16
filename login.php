<?php
// login.php
session_start();
if (isset($_SESSION['user_id'])) {
    $homepage = ($_SESSION['academic_year'] == 2) ? 'hons-2nd-year.php' : 'hons-1st-year.php';
    header('Location: ' . $homepage);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Saadat Notebook</title>

    <!-- Automatic Dark Mode Script -->
    <script>
        (function () { try { const theme = localStorage.getItem('saadatNotesTheme'); const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches; if (theme === 'dark' || (!theme && systemPrefersDark)) { document.documentElement.classList.add('dark-mode'); } } catch (e) { } })();
    </script>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/register.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* This style block ONLY applies to the login page */
        .register-body {
            align-items: center;
            /* Vertically centers the content */
            padding-top: 0;
            /* Removes the top padding */
        }
    </style>
    <style>
        .password-wrapper {
            position: relative;
        }

        .password-toggle-icon {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--text-secondary);
        }

        .form-switch-link {
            color: var(--text-primary);
            font-weight: bold;
            text-decoration: none;
        }

        .forgot-password-link {
            display: block;
            text-align: right;
            font-size: 0.85rem;
            margin-top: -1rem;
            margin-bottom: 1.5rem;
        }
    </style>
</head>

<body class="register-body">
    <div id="notification" class="notification-bar"><span id="notification-message"></span></div>

    <div class="register-container">
        <header class="register-header">
            <div class="logo-container">
                <img src="images/logo.png" alt="Saadat Notebook Logo" class="site-logo">
                <div class="logo-text">
                    <h1>Saadat Notebook</h1>
                </div>
            </div>
            <h2>Welcome Back!</h2>
            <p>Please log in to access your dashboard.</p>
        </header>

        <main class="form-card">
            <form id="login-form" novalidate>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" placeholder="Enter your email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" placeholder="Password" required>
                        <i class="fas fa-eye password-toggle-icon"></i>
                    </div>
                </div>
                <!-- REPLACE WITH THIS BLOCK -->

<!-- New Flex Container -->
<div class="form-group" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <!-- Remember Me Checkbox -->
    <div style="display: flex; align-items: center;">
        <input type="checkbox" id="remember-me" style="width: auto; margin-right: 8px;">
        <label for="remember-me" style="margin-bottom: 0; font-weight: 500; color: var(--text-secondary);">Remember Me</label>
    </div>
    <!-- Forgot Password Link -->
    <a href="forgot-password.php" class="form-switch-link" style="font-size: 0.9rem;">Forgot Password?</a>
</div>

<button type="submit" class="submit-btn">Log In</button>
            </form>
            <p style="text-align:center; margin-top:1.5rem; font-size: 0.9rem;">
                Don't have an account? <a href="register.php" class="form-switch-link">Sign Up</a>
            </p>
            <?php include 'footer.php'; ?>
        </main>
    </div>
    
    <script src="js/config.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Reusing login script, just adding the password toggle logic
            const form = document.getElementById('login-form');
            const notification = document.getElementById('notification');
            const notificationMessage = document.getElementById('notification-message');
            let notificationTimeout;

            function showNotification(message, isError = true) {
                clearTimeout(notificationTimeout);
                notification.style.backgroundColor = isError ? '#d32f2f' : '#28a745';
                notificationMessage.textContent = message;
                notification.classList.add('show');
                notificationTimeout = setTimeout(() => notification.classList.remove('show'), 4000);
            }

            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const button = form.querySelector('.submit-btn');
                const email = document.getElementById('email').value.trim();
                const password = document.getElementById('password').value;
                const rememberMe = document.getElementById('remember-me').checked;

                if (!email || !password) {
                    showNotification('Please enter both email and password.'); return;
                }
                button.disabled = true; button.textContent = 'Logging In...';
                try {
                    const response = await fetch('${window.API_BASE_URL}/api/login.php', {
                        method: 'POST',
                        credentials: 'include',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ email, password, rememberMe })
                    });
                    const data = await response.json();

                    if (data.success) {
                        showNotification('Login successful! Redirecting...', false);
                        window.location.href = data.redirectUrl;
                    } else {
                        showNotification(data.message || 'An unknown error occurred.');
                        button.disabled = false; button.textContent = 'Log In';
                    }
                } catch (error) {
                    showNotification('A network error occurred. Please try again.');
                    button.disabled = false; button.textContent = 'Log In';
                }
            });

            // --- Password Visibility Toggle Logic ---
            document.querySelectorAll('.password-toggle-icon').forEach(icon => {
                icon.addEventListener('click', () => {
                    const input = icon.previousElementSibling;
                    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                    input.setAttribute('type', type);
                    icon.classList.toggle('fa-eye');
                    icon.classList.toggle('fa-eye-slash');
                });
            });
        });
    </script>
</body>

</html>