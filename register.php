<?php
// register.php
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
    <title>Register - Saadat Notebook</title>
    
    <!-- Automatic Dark Mode Script -->
    <script>
        (function () { try { const theme = localStorage.getItem('saadatNotesTheme'); const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches; if (theme === 'dark' || (!theme && systemPrefersDark)) { document.documentElement.classList.add('dark-mode'); } } catch (e) {} })();
    </script>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/register.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .password-wrapper { position: relative; }
        .password-toggle-icon { position: absolute; top: 50%; right: 15px; transform: translateY(-50%); cursor: pointer; color: var(--text-secondary); }
        .form-switch-link { color: var(--text-primary); font-weight: bold; text-decoration: none; }
    </style>
</head>
<body class="register-body">
    <div id="notification" class="notification-bar"><span id="notification-message"></span></div>

    <div class="register-container">
        <header class="register-header">
            <div class="logo-container">
                <img src="images/logo.png" alt="Saadat Notebook Logo" class="site-logo">
                <div class="logo-text"><h1>Saadat Notebook</h1></div>
            </div>
            <h2>Create Your Account</h2>
            <p>Get started by setting up your profile.</p>
        </header>

        <main class="form-card">
            <form id="register-form" novalidate>
                <div class="form-group">
                    <label for="user-name">Your Full Name</label>
                    <input type="text" id="user-name" placeholder="Full name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" placeholder="Enter your email" required>
                </div>
                <div class="form-group">
                    <label for="password">Create Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" placeholder="Minimum 6 characters" required>
                        <i class="fas fa-eye password-toggle-icon"></i>
                    </div>
                </div>
                <div class="form-group">
                    <label for="confirm-password">Confirm Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="confirm-password" placeholder="Confirm password" required>
                        <i class="fas fa-eye password-toggle-icon"></i>
                    </div>
                </div>
                <div class="form-group">
                    <label for="academic-year">Your Academic Year</label>
                    <select id="academic-year" required>
                        <option value="1">Honours 1st Year</option>
                        <option value="2">Honours 2nd Year</option>
                    </select>
                </div>
                
                <div class="form-group" style="margin-top: 1.5rem; display: flex; align-items: flex-start;">
                    <input type="checkbox" id="terms-agree" name="terms-agree" style="width: auto; margin-right: 10px; margin-top: 4px; cursor: pointer;">
                    <label for="terms-agree" style="margin-bottom: 0; font-size: 0.85rem; color: var(--text-secondary); line-height: 1.5; cursor: pointer;">
                        I have read and agree to the <a href="terms.php" target="_blank" style="color:var(--primary-color); text-decoration: none; font-weight: 500;">Terms and Conditions</a> and <a href="privacy-policy.php" target="_blank" style="color:var(--primary-color); text-decoration: none; font-weight: 500;">Privacy Policy</a>.
                    </label>
                </div>
                
                <button type="submit" class="submit-btn">Create Account</button>
            </form>
            <p style="text-align:center; margin-top:1.5rem; font-size: 0.9rem;">
                Already have an account? <a href="login.php" class="form-switch-link">Log In</a>
            </p>
        </main>
    </div>
    <script src="js/config.js"></script>
    <script src="js/register.js"></script>
</body>
</html>