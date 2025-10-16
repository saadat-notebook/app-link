<?php
// We MUST include our config file to get the book categories and connect to the DB.
require_once 'admin/config.php';

// --- THE FIRST FIX: Add the security check at the top ---
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get the requested year from the URL. Default to '1' for safety.
$year_to_show = isset($_GET['year']) ? intval($_GET['year']) : 1;

// Get the centralized list of book categories from our config.
$book_categories = BOOK_CATEGORIES;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore Reads (Year <?php echo $year_to_show; ?>) - Saadat Notebook</title>

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
    <!-- Your Header, Search, etc. (Unchanged HTML) -->
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

    <div class="page-wrapper animate__animated animate__fadeIn">
        <main>
            <h1 class="page-title">Explore Reads
            </h1>

            <!-- NEW: A single grid for all books, no accordion -->
<div class="book-grid">
    <?php
    // A single, simple query to get ALL books for the current year
    $sql = "SELECT id, title, file_link, preview_link, cover_image_url 
            FROM uploads 
            WHERE category = 'Books' AND academic_year = $year_to_show 
            ORDER BY title ASC";
    
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0):
        while ($row = $result->fetch_assoc()):
            // Prepare data for the card
            $cover_url = !empty($row['cover_image_url']) ? htmlspecialchars($row['cover_image_url']) : 'https://via.placeholder.com/300x450.png?text=No+Cover';
            $book_title = htmlspecialchars($row['title']);
    ?>
    <!-- This is the new book card structure with buttons -->
    <div class="book-card"
        data-id="<?php echo $row['id']; ?>"
        data-title="<?php echo htmlspecialchars($row['title']); ?>"
        data-preview-link="<?php echo htmlspecialchars($row['preview_link']); ?>"
        data-file-link="<?php echo htmlspecialchars($row['file_link']); ?>"
        data-description="" 
        data-videos-json="[]">

        <a href="#" class="preview-link book-cover-link" 
           data-preview-url="<?php echo htmlspecialchars($row['preview_link']); ?>"
           data-download-url="<?php echo htmlspecialchars($row['file_link']); ?>" 
           data-title="<?php echo htmlspecialchars($row['title']); ?>">
             <img src="<?php echo $cover_url; ?>" alt="Cover for <?php echo $book_title; ?>" loading="lazy">
        </a>
        
        <h4><?php echo $book_title; ?></h4>
        
        <div class="book-card-actions">
    <!-- Icon-only Bookmark Button -->
    <button class="book-action-btn book-action-icon bookmark-btn" title="Bookmark" data-item-id="<?php echo $row['id']; ?>">
        <i class="far fa-bookmark"></i>
    </button>
</div>
    </div>
    <?php
        endwhile;
    else:
        echo '<p>No books have been added for this year yet.</p>';
    endif;
    ?>
</div> <!-- end .book-grid -->
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
                <!-- ===== MODIFIED PREVIEW ACTIONS ===== -->
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
    <script src="js/accordion.js?v=<?= filemtime('js/accordion.js'); ?>"></script>
    <script src="js/bookmarks.js?v=<?= filemtime('js/bookmarks.js'); ?>"></script>
    <script>
    });
</script>
</body>

</html>
<?php
if (isset($conn)) {
    $conn->close();
}
?>