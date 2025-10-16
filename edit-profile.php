<?php
// edit-profile.php
require_once 'admin/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$stmt = $conn->prepare("SELECT name, academic_year FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Saadat Notebook</title>
    
    <script> (function () { try { const theme = localStorage.getItem('saadatNotesTheme'); const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches; if (theme === 'dark' || (!theme && systemPrefersDark)) { document.documentElement.classList.add('dark-mode'); } } catch (e) {} })(); </script>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/register.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style> .password-wrapper { position: relative; } .password-toggle-icon { position: absolute; top: 50%; right: 15px; transform: translateY(-50%); cursor: pointer; color: var(--text-secondary); } 
    /* --- Styles for Form Page Links (Login, Register, etc.) --- */
.form-switch-link {
    color: var(--text-primary);
    font-weight: bold;
    text-decoration: none;
    transition: opacity 0.2s;
}
.form-switch-link:hover {
    opacity: 0.8;
}
    </style>
</head>
<body class="register-body">
    <div id="notification" class="notification-bar"><span id="notification-message"></span></div>
    
    <div class="register-container" style="max-width: 450px;">
        <header class="register-header">
             <h2>Edit Your Profile</h2>
             <p>Update your information below.</p>
        </header>

        <main class="form-card">
             <form id="edit-profile-form" novalidate>
                <!-- Profile Picture Uploader (Identical to original register page) -->
                <div class="profile-pic-uploader">
                    <input type="file" id="profile-pic-input" accept="image/*" hidden>
                    <label for="profile-pic-input" class="profile-pic-label">
                        <img src="images/avatar.jpg" alt="Profile Preview" id="profile-pic-preview">
                        <span id="upload-icon-container" class="hidden">
                            <i class="fas fa-camera"></i>
                            <span>Change Photo</span>
                        </span>
                    </label>
                </div>
                 
                <div class="form-group">
                    <label for="user-name">Your Full Name</label>
                    <input type="text" id="user-name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                </div>
                 
                <div class="form-group">
                    <label for="academic-year">Academic Year</label>
                    <select id="academic-year" required>
                        <option value="1" <?php echo ($user['academic_year'] == 1) ? 'selected' : ''; ?>>Honours 1st Year</option>
                        <option value="2" <?php echo ($user['academic_year'] == 2) ? 'selected' : ''; ?>>Honours 2nd Year</option>
                    </select>
                </div>
                 
                <div class="form-group">
                    <label for="new-password">New Password (optional)</label>
                     <div class="password-wrapper">
                        <input type="password" id="new-password" placeholder="Leave blank to keep current">
                        <i class="fas fa-eye password-toggle-icon"></i>
                    </div>
                </div>

                <button type="submit" class="submit-btn">Save Changes</button>
            </form>
             <p style="text-align:center; margin-top:1.5rem;"><a href="profile.php" class="form-switch-link">Back to Profile</a></p>
        </main>
    </div>
    <script src="js/config.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('edit-profile-form');
            const picInput = document.getElementById('profile-pic-input');
            const picPreview = document.getElementById('profile-pic-preview');
            let customPicDataUrl = localStorage.getItem('saadatNotesCustomUserPic') || '';
            
            // --- Image Compression and Preview Logic ---
            if (customPicDataUrl) {
                picPreview.src = customPicDataUrl;
            }
            
            picInput.addEventListener('change', () => {
                const file = picInput.files[0];
                if (!file) return;

                const reader = new FileReader();
                reader.onload = (e) => {
                    const img = new Image();
                    img.onload = () => {
                        const canvas = document.createElement('canvas');
                        const MAX_WIDTH = 256;
                        const MAX_HEIGHT = 256;
                        let width = img.width;
                        let height = img.height;

                        if (width > height) {
                            if (width > MAX_WIDTH) { height *= MAX_WIDTH / width; width = MAX_WIDTH; }
                        } else {
                            if (height > MAX_HEIGHT) { width *= MAX_HEIGHT / height; height = MAX_HEIGHT; }
                        }
                        canvas.width = width;
                        canvas.height = height;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);
                        
                        // Get compressed image as Base64, with 80% quality
                        customPicDataUrl = canvas.toDataURL('image/jpeg', 0.8);
                        picPreview.src = customPicDataUrl;
                    };
                    img.src = e.target.result;
                };
                reader.readAsDataURL(file);
            });
            
            // --- Form Submission Logic ---
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
                button.disabled = true; button.textContent = 'Saving...';
                
                // Save the image to localStorage FIRST
                if (customPicDataUrl) {
                    localStorage.setItem('saadatNotesCustomUserPic', customPicDataUrl);
                }

                // Prepare data for the server
                const payload = {
                    name: document.getElementById('user-name').value.trim(),
                    academic_year: document.getElementById('academic-year').value
                };
                const newPassword = document.getElementById('new-password').value;
                if (newPassword) {
                    payload.new_password = newPassword;
                }

                try {
                    const response = await fetch('${window.API_BASE_URL}/api/update-profile.php', {
                        method: 'POST',
                        credentials: 'include',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify(payload)
                    });
                    const data = await response.json();
                    if (data.success) {
                        showNotification('Profile updated successfully!', false);
                        if (data.session_updated) {
                            // If year was changed, redirect to the new homepage after a short delay
                            showNotification('Profile updated successfully!', false);
                            setTimeout(() => window.location.href = 'index.php', 2000);
                        } else {
                             button.disabled = false; button.textContent = 'Save Changes';
                        }
                    } else {
                        showNotification(data.message || 'An error occurred.');
                         button.disabled = false; button.textContent = 'Save Changes';
                    }
                } catch(error) {
                    showNotification('A network error occurred.');
                     button.disabled = false; button.textContent = 'Save Changes';
                }
            });

            // Password visibility toggle
            document.querySelectorAll('.password-toggle-icon').forEach(icon => {
                icon.addEventListener('click', () => {
                    const input = icon.previousElementSibling;
                    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                    input.setAttribute('type', type);
                    icon.classList.toggle('fa-eye'); icon.classList.toggle('fa-eye-slash');
                });
            });
        });
    </script>
</body>
</html>