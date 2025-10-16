<?php
require_once 'admin/config.php';

// --- THE FIRST FIX: Add the security check at the top ---
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get the requested year from the URL. Default to '1' for safety.
$year_to_show = isset($_GET['year']) ? intval($_GET['year']) : 1;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recent Updates (Year <?php echo $year_to_show; ?>) - Saadat Notebook</title>

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
    <header class="header"><!-- Header HTML is unchanged --><a href="index.php" class="logo-link">
            <div class="logo-container"><img src="images/logo.png" alt="Saadat Notebook Logo" class="site-logo">
                <div class="logo-text">
                    <h1>Saadat Notebook</h1>
                    <p>Department of Mathematics</p>
                </div>
            </div>
        </a>
        <div class="header-actions"><button class="search-icon-trigger" aria-label="Open Search"><i
                    class="fas fa-search"></i></button><button class="menu-icon" aria-label="Open Menu"><i
                    class="fas fa-bars"></i></button></div>
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

    <div class="page-wrapper animate__animated animate__fadeIn">
        <main>
            <h1 class="page-title">Recent Updates - Honours
                <?php echo $year_to_show;
                echo ($year_to_show == 1) ? 'st' : 'nd'; ?> Year
            </h1>

            <div class="upload-list">
                <?php
                // UPDATED QUERY: Fetch ALL data needed for bookmarking
$sql = "SELECT id, title, file_link, preview_link, category, description, video_links_json 
        FROM uploads WHERE academic_year = $year_to_show ORDER BY upload_date DESC";
                $result = $conn->query($sql);

                if ($result->num_rows > 0):
                    while ($row = $result->fetch_assoc()):
                        ?>
                        <div class="upload-item"
    data-id="<?php echo $row['id']; ?>"
    data-title="<?php echo htmlspecialchars($row['title']); ?>"
    data-preview-link="<?php echo htmlspecialchars($row['preview_link']); ?>"
    data-file-link="<?php echo htmlspecialchars($row['file_link']); ?>"
    data-description="<?php echo htmlspecialchars($row['description']); ?>"
    data-videos-json='<?php echo htmlspecialchars($row['video_links_json'] ?? '[]', ENT_QUOTES, 'UTF-8'); ?>'>
                            <div class="upload-icon"><i class="fas fa-file-pdf"></i></div>
                            <div class="upload-details">
                                <h4><a href="#" class="preview-link"
                                        data-preview-url="<?php echo htmlspecialchars($row['preview_link']); ?>"><?php echo htmlspecialchars($row['title']); ?></a>
                                </h4>
                                <p class="upload-category">Category: <?php echo htmlspecialchars($row['category']); ?></p>
                            </div>                                <!-- ===== NEW: BOOKMARK BUTTON ===== -->
                                <button class="bookmark-btn" aria-label="Bookmark" data-item-id="<?php echo $row['id']; ?>">
                                    <i class="far fa-bookmark"></i>
                                </button>
                                <!-- ===== END: BOOKMARK BUTTON ===== -->

                                <a href="<?php echo htmlspecialchars($row['file_link']); ?>" class="download-btn"
                                    aria-label="Download" download>
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        <?php
                    endwhile;
                else:
                    echo '<p>No updates have been posted for this year yet.</p>';
                endif;
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
    <script src="js/user-session.js?v=<?= filemtime('js/user-session.js'); ?>"></script>
    <script src="js/global.js?v=<?= filemtime('js/global.js'); ?>"></script>
    <script src="js/bookmarks.js?v=<?= filemtime('js/bookmarks.js'); ?>"></script>
</body>

</html>
<?php
if (isset($conn)) {
    $conn->close();
}
?>