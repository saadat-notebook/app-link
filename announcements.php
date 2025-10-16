<?php
require_once 'admin/config.php';
$year_to_show = isset($_GET['year']) ? intval($_GET['year']) : 1;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements (Year <?php echo $year_to_show; ?>) - Saadat Notebook</title>

    <script>
        (function () {
            try {
                const theme = localStorage.getItem('saadatNotesTheme');
                const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (theme === 'dark' || (!theme && systemPrefersDark)) {
                    document.documentElement.classList.add('dark-mode');
                }
                const ua = navigator.userAgent || "";
                if (ua.includes("wv") || ua.includes("WrapperApp") || ua.includes("saadatnotebook")) {
                    document.documentElement.classList.add("wrapper-app");
                }
            } catch (e) { }
        })();
    </script>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
</head>

<body>
<script>
    (function() {
        try {
            // Check if user is logged in by looking for a session-related item (we use homepage for this check)
            if (document.cookie.includes("PHPSESSID")) { // A better check for a server session
                const bottomNavHTML = `
                <nav class="bottom-nav">
                    <a href="index.php" class="nav-link nav-home-link"><i class="fas fa-home"></i><span>Dashboard</span></a>
                    <a href="profile.php" class="nav-link"><i class="fas fa-user-circle"></i><span>Profile</span></a>
                    <a href="bcs-math.html" class="nav-link"><i class="fas fa-square-root-alt"></i><span>BCS Maths</span></a>
                </nav>`;
                document.write(bottomNavHTML);
            }
        } catch(e) { console.error("Bottom nav injection failed:", e); }
    })();
</script>

    <!-- ===== START OF CORRECT HEADER & SEARCH ===== -->
    <header class="header">
        <a href="index.php" class="logo-link">
            <div class="logo-container">
                <img src="images/logo.png" alt="Saadat Notebook Logo" class="site-logo">
                <div class="logo-text">
                    <h1>Saadat Notebook</h1>
                    <p>Department of Mathematics</p>
                </div>
            </div>
        </a>
        <div class="header-actions">
            <button class="search-icon-trigger" aria-label="Open Search"><i class="fas fa-search"></i></button>
            <button class="menu-icon" aria-label="Open Menu"><i class="fas fa-bars"></i></button>
        </div>
    </header>
    <!-- Your Universal Search View -->
    <div id="search-view">
        <div class="search-view-header">
            <button id="search-back-btn" aria-label="Go Back"><i class="fas fa-arrow-left"></i></button>
            <div class="search-input-container">
                <i class="fas fa-search"></i>
                <input type="text" id="search-view-input" placeholder="Search by title or subject name...">
            </div>
        </div>
        <div class="search-view-body">
            <div class="search-placeholder">
                <p>Start typing to search...</p>
            </div>
        </div>
    </div>
    <!-- ===== END OF CORRECT HEADER & SEARCH ===== -->

    <div class="page-wrapper animate__animated animate__fadeIn">
        <main>
            <h1 class="page-title">Announcements - Honours
                <?php echo $year_to_show;
                echo ($year_to_show == 1) ? 'st' : 'nd'; ?> Year
            </h1>

            <div class="announcements-container">
                <?php
                // Fetch announcements for the correct year, newest first
                $sql = "SELECT title, description, image_url, created_at FROM announcements WHERE academic_year = $year_to_show ORDER BY created_at DESC";
                $result = $conn->query($sql);

                if ($result->num_rows > 0):
                    while ($row = $result->fetch_assoc()):
?>
    <div class="announcement-card">
        
        <?php // Only show the image block if an image_url exists
        if (!empty($row['image_url'])): ?>
            <div class="announcement-image">
                <img src="<?php echo htmlspecialchars($row['image_url']); ?>"
                     alt="<?php echo htmlspecialchars($row['title']); ?>" loading="lazy">
            </div>
        <?php endif; ?>

        <div class="announcement-content">
            <h2><?php echo htmlspecialchars($row['title']); ?></h2>
            
            <p class="announcement-meta">
                <i class="fas fa-calendar-alt"></i> Posted on:
                <?php echo date('F j, Y', strtotime($row['created_at'])); ?>
            </p>
            
            <div class="announcement-description">
                <?php // nl2br converts newlines in your description to <br> tags
                echo nl2br(htmlspecialchars($row['description'])); ?>
            </div>
        </div>
    </div>
<?php
endwhile;
                else:
                    echo '<p>No announcements have been posted for this year yet.</p>';
                endif;
                $conn->close();
                ?>
            </div>
        </main>
    </div>

    <!-- PREVIEW MODAL -->
    <div class="modal-overlay" id="preview-modal">
        <div class="modal-content">
            <div class="preview-header">
                <button class="preview-action-btn" id="preview-back-btn" aria-label="Close Preview">
                    <i class="fas fa-arrow-left"></i>
                </button>
                <h3 id="preview-title"></h3>
                <div class="preview-actions">
                    <a href="#" id="preview-download-btn" class="preview-action-btn" aria-label="Download File"
                        download>
                        <i class="fas fa-download"></i>
                    </a>
                </div>
            </div>
            <iframe src="" frameborder="0"></iframe>
        </div>
    </div>
    <script src="js/config.js"></script>
    <script src="js/global.js?v=<?= filemtime('js/global.js'); ?>"></script>
</body>

</html>