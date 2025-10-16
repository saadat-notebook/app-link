<?php
// index.php (NEW GATEWAY VERSION)
require_once 'admin/config.php'; // This runs our "Remember Me" logic automatically

// After config.php has run, we check if a session now exists.
if (isset($_SESSION['user_id'])) {
    // If logged in, redirect to the correct homepage.
    $homepage = ($_SESSION['academic_year'] == 2) ? 'hons-2nd-year.php' : 'hons-1st-year.php';
    header('Location: ' . $homepage);
    exit();
} else {
    // If not logged in, even after the cookie check, redirect to the login page.
    // This is a failsafe.
    header('Location: login.php');
    exit();
}

// THE PHP SCRIPT ENDS HERE. The content below is a fallback loading screen,
// but users will almost never see it because the redirects are instant.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Loading Saadat Notebook...</title>
    <style>
        body { background-color: #f5f5f5; font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .loader { border: 8px solid #e0e0e0; border-top: 8px solid #0a0a0a; border-radius: 50%; width: 60px; height: 60px; animation: spin 1s linear infinite; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
    <script src="js/config.js"></script>
    <script>
        (function() {
            try {
                const theme = localStorage.getItem('saadatNotesTheme');
                if (theme === 'dark') { document.body.style.backgroundColor = '#121212'; }
            } catch (e) {}
        })();
    </script>
</head>
<body>
    <div class="loader"></div>
</body>
</html>