<?php
// contact.php (FINAL ENHANCED VERSION 3.0)
require_once 'admin/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch user's name and email from the server session
$userName = $_SESSION['user_name'] ?? '';
$userEmail = '';
$stmt = $conn->prepare("SELECT email FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
if($user = $result->fetch_assoc()) {
    $userEmail = $user['email'];
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Saadat Notebook</title>

    <script> (function () { try { const theme = localStorage.getItem('saadatNotesTheme'); const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches; if (theme === 'dark' || (!theme && systemPrefersDark)) { document.documentElement.classList.add('dark-mode'); } } catch (e) { } })(); </script>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/register.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* THE FIRST FIX: Correctly style the textarea */
        textarea#message {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            font-size: 1rem;
            font-family: inherit;
            background-color: transparent;
            color: var(--text-primary);
            resize: vertical;
        }
        /* Style for the link in the "Thank You" message */
        .form-switch-link {
             color: var(--text-primary);
             font-weight: bold;
             text-decoration: none;
        }
        /* Style for the thank you message container */
        #thank-you-message {
            text-align: center;
            padding: 2rem 1rem;
        }
        #thank-you-message i {
            font-size: 3rem;
            color: var(--success-color);
            margin-bottom: 1rem;
        }
        #thank-you-message h3 {
            font-size: 1.5rem;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }
        #thank-you-message p {
            color: var(--text-secondary);
            margin-bottom: 2rem;
        }
    </style>
</head>
<body class="register-body" style="align-items: center; padding: 2rem 1rem;">
    <div id="notification" class="notification-bar"><span id="notification-message"></span></div>

    <div class="register-container" style="max-width: 500px;">
        <header class="register-header">
            <h2>Get in Touch</h2>
            <p>Have a question or feedback? Fill out the form below.</p>
        </header>

        <main class="form-card" id="contact-card">
            <!-- Form will be inside here -->
            <form id="contact-form" novalidate>
                <input type="text" name="website_url" style="display:none;" tabindex="-1" autocomplete="off">
                
                <div class="form-group">
                    <label for="name">Your Name</label>
                    <input type="text" id="name" value="<?php echo htmlspecialchars($userName); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Your Email</label>
                    <input type="email" id="email" value="<?php echo htmlspecialchars($userEmail); ?>" required>
                </div>
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" rows="5" required></textarea>
                </div>
                <button type="submit" class="submit-btn">Send Message</button>
            </form>
            <!-- Thank you message is initially hidden -->
            <div id="thank-you-message" style="display: none;">
                 <i class="fas fa-check-circle"></i>
                 <h3>Thank You!</h3>
                 <p>Your message has been sent successfully. We'll get back to you as soon as possible.</p>
                 <a href="index.php" class="submit-btn" style="text-decoration: none;">Back to Dashboard</a>
            </div>
        </main>
    </div>
    <script src="js/config.js"></script>
    <script src="js/user-session.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('contact-form');
        const contactCard = document.getElementById('contact-card');
        const thankYouMessage = document.getElementById('thank-you-message');
        const notification = document.getElementById('notification');
        const notificationMessage = document.getElementById('notification-message');
        let notificationTimeout;

        function showNotification(message, isSuccess = false) {
            clearTimeout(notificationTimeout);
            notification.style.backgroundColor = isSuccess ? '#28a745' : '#d32f2f';
            notificationMessage.textContent = message;
            notification.classList.add('show');
            notificationTimeout = setTimeout(() => notification.classList.remove('show'), 4000);
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const button = form.querySelector('.submit-btn');
            const payload = {
                name: document.getElementById('name').value,
                email: document.getElementById('email').value,
                message: document.getElementById('message').value,
                website_url: form.querySelector('[name="website_url"]').value
            };

            if (!payload.name || !payload.email || !payload.message) {
                showNotification('Please fill out all fields.');
                return;
            }

            button.disabled = true;
            button.textContent = 'Sending...';

            try {
                const response = await fetch('api/contact.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(payload)
                });
                const data = await response.json();
                if (data.success) {
                    // THE SECOND FIX: Show the thank you message
                    form.style.display = 'none'; // Hide the form
                    thankYouMessage.style.display = 'block'; // Show the message
                } else {
                    showNotification(data.message || 'An error occurred.');
                    button.disabled = false;
                    button.textContent = 'Send Message';
                }
            } catch (error) {
                showNotification('A network error occurred. Please try again.');
                button.disabled = false;
                button.textContent = 'Send Message';
            }
        });
    });
    </script>
</body>
</html>