<?php
// hons-2nd-year.php
require_once 'admin/config.php'; // We use our new API config file

// --- SECURITY CHECK ---
// If the user is not logged in, redirect them to the login page.
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get user data from the session to personalize the page
$userName = $_SESSION['user_name'];
$userYearInt = $_SESSION['academic_year'];
$userYearText = ($userYearInt == 2) ? 'Honours 2nd Year' : 'Honours 1st Year';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saadat Notebook</title>
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
</head>

<body>
    <script>
        (function () {
    try {
        // No localStorage check is needed here since we know the user is logged in
        const bottomNavHTML = `
        <nav class="bottom-nav">
            <a href="index.php" class="nav-link nav-home-link">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <a href="profile.php" class="nav-link"> <!-- THIS LINK IS NOW CORRECT -->
                <i class="fas fa-user-circle"></i>
                <span>Profile</span>
            </a>
            <a href="bcs-math.html" class="nav-link">
                <i class="fas fa-square-root-alt"></i>
                <span>BCS Maths</span>
            </a>
        </nav>`;
        document.write(bottomNavHTML);
    } catch (e) {
        console.error("Bottom nav injection failed:", e);
    }
})();
    </script>
    <!-- Your Fixed Header -->
    <header class="header">
        <a href="index.php" class="logo-link">
            <div class="logo-container">
                <!-- Replace the text-based block with your logo -->
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

    <!-- Your Page Wrapper -->
    <div class="page-wrapper animate__animated animate__fadeIn">
        <main>
            <!-- USER CARD IS NOW PERSONALIZED BY PHP -->
            <div class="user-card">
                <div class="welcome-header">
                    <h2>Welcome back!</h2>
                    <div class="user-info">
                        <div class="avatar-container">
                            <img src="images/avatar.jpg" alt="User Avatar" class="avatar" id="user-avatar-img">
                            <div class="active-status"></div>
                        </div>
                        <div class="user-details">
                            <!-- We echo the PHP variables here -->
                            <span class="user-name"><?php echo htmlspecialchars($userName); ?></span>
                            <span class="user-year"><?php echo htmlspecialchars($userYearText); ?></span>
                        </div>
                    </div>
                </div>
                <div class="user-meta">
                     <div class="meta-item"><i class="fas fa-graduation-cap"></i><span>Mathematics</span></div>
                     <div class="meta-item"><i class="fas fa-clock"></i><span>Active now</span></div>
                </div>
            </div>
            <div class="quick-access"><a href="#" class="main-resource-link"><i
                        class="fas fa-book-open"></i><span>Honours 2nd Year Resources</span><i
                        class="fas fa-chevron-down"></i></a></div>
            <div class="section-title">
                <h3>Quick Access</h3>
            </div>
            <div class="button-grid">
                <a href="lectures.php?year=2" class="grid-button"><i class="fas fa-chalkboard-teacher"></i><span>Class
                        Lectures</span></a>
                <a href="notes.php?year=2" class="grid-button"><i class="fas fa-pencil-alt"></i><span>Hand
                        Notes</span></a>
                <a href="question.php?year=2" class="grid-button"><i class="fas fa-question-circle"></i><span>NU
                        Questions</span></a>
                <a href="suggestions.php?year=2" class="grid-button"><i
                        class="fas fa-lightbulb"></i><span>Suggestions</span></a>
                <a href="routines.php?year=2" class="grid-button"><i
                        class="fas fa-calendar-alt"></i><span>Routines</span></a>
                <a href="books.php?year=2" class="grid-button"><i class="fas fa-book"></i><span>Explore Reads</span></a>
                <a href="department.php" class="grid-button"><i class="fas fa-university"></i><span>Our
                        Department</span></a>

                <a href="https://www.saadatcollege.gov.bd/noticeboardview" class="grid-button" target="_blank"
                    rel="noopener noreferrer"><i class="fas fa-bullhorn"></i><span>College Notice</span></a>
            </div>

                        <!-- ===== QUESTION OF THE DAY WIDGET (v3 with Image Support) ===== -->
            <?php
            date_default_timezone_set('UTC');
            $today_date = date('Y-m-d');
            $stmt_qotd = $conn->prepare("SELECT question_text, question_image_url FROM daily_questions WHERE show_date = ? LIMIT 1");
            $stmt_qotd->bind_param("s", $today_date);
            $stmt_qotd->execute();
            $result_qotd = $stmt_qotd->get_result();
            if ($qotd = $result_qotd->fetch_assoc()):
            ?>
            <div class="qotd-widget-card">
                <div class="widget-header"><i class="fas fa-brain"></i><h3>Today's Brain Teaser</h3></div>
                <div class="widget-body">
                    <?php if (!empty($qotd['question_image_url'])): ?>
                        <img src="<?php echo htmlspecialchars($qotd['question_image_url']); ?>" alt="Question Diagram" class="widget-image">
                    <?php endif; ?>
                    
                    <!-- ===== UPDATED: Display raw question text for KaTeX to render ===== -->
                    <p class="qotd-text"><?php echo $qotd['question_text']; ?></p>

                </div>
                <div class="widget-footer">
                    <a href="question-of-the-day.html" class="btn-primary">View & Solve Now</a>
                </div>
            </div>
            <?php
            endif;
            $stmt_qotd->close();
            ?>
            <!-- ===== END: QUESTION OF THE DAY WIDGET ===== -->

            <!-- ===== START: FINAL ANNOUNCEMENT CAROUSEL ===== -->
<?php
$year_for_page = 2; // Set for 2nd Year
$announcements_for_carousel = [];
$pinned_sql = "SELECT id, title, description, image_url FROM announcements WHERE academic_year = ? AND is_pinned = 1 LIMIT 1";
$stmt_pinned = $conn->prepare($pinned_sql);
$stmt_pinned->bind_param("i", $year_for_page);
$stmt_pinned->execute();
$result_pinned = $stmt_pinned->get_result();
$pinned_announcement = $result_pinned->fetch_assoc();
$stmt_pinned->close();

$limit = 4;
$exclude_id = null;

if ($pinned_announcement) {
    $announcements_for_carousel[] = $pinned_announcement;
    $limit = 3;
    $exclude_id = $pinned_announcement['id'];
}

if ($limit > 0) {
    $recent_sql = "SELECT id, title, description, image_url FROM announcements WHERE academic_year = ?";
    if ($exclude_id) { $recent_sql .= " AND id != ?"; }
    $recent_sql .= " ORDER BY created_at DESC LIMIT ?";
    $stmt_recent = $conn->prepare($recent_sql);
    if ($exclude_id) { $stmt_recent->bind_param("iii", $year_for_page, $exclude_id, $limit); } 
    else { $stmt_recent->bind_param("ii", $year_for_page, $limit); }
    $stmt_recent->execute();
    $result_recent = $stmt_recent->get_result();
    while ($row = $result_recent->fetch_assoc()) { $announcements_for_carousel[] = $row; }
    $stmt_recent->close();
}

if (!empty($announcements_for_carousel)):
?>
<div class="recent-announcement">
    <div class="section-title">
        <h3>Latest Announcements</h3>
        <a href="announcements.php?year=<?php echo $year_for_page; ?>" class="see-all">See all</a>
    </div>

    <div class="swiper announcement-swiper-v2">
        <div class="swiper-wrapper">
            <?php foreach ($announcements_for_carousel as $announcement): 
                $has_image = !empty($announcement['image_url']);
            ?>
            <div class="swiper-slide">
                <a href="#" class="announcement-slide-card <?php echo $has_image ? 'has-image' : 'no-image'; ?>"
                   data-full-title="<?php echo htmlspecialchars($announcement['title']); ?>"
                   data-full-description="<?php echo htmlspecialchars($announcement['description']); ?>"
                   data-full-image="<?php echo htmlspecialchars($announcement['image_url']); ?>">
                    
                    <?php if ($has_image): ?>
                        <img src="<?php echo htmlspecialchars($announcement['image_url']); ?>" alt="Announcement" class="card-background-image">
                        <div class="card-overlay"></div>
                        <div class="card-content">
                            <h4 class="card-title"><?php echo htmlspecialchars($announcement['title']); ?></h4>
                        </div>
                    <?php else: ?>
                        <div class="card-content">
                            <h4 class="card-title"><?php echo htmlspecialchars($announcement['title']); ?></h4>
                            <p class="card-description-snippet"><?php
                                $desc = $announcement['description'];
                                echo htmlspecialchars(substr($desc, 0, 120));
                                if (strlen($desc) > 120) echo '...';
                            ?></p>
                        </div>
                    <?php endif; ?>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="swiper-pagination"></div>
    </div>
</div>
<?php endif; ?>
<!-- ===== END: FINAL ANNOUNCEMENT CAROUSEL ===== -->

            <!-- ===== START OF SMART "RECENT UPLOADS" SECTION ===== -->
            <div class="recent-uploads">
                <div class="section-title">
                    <h3>Recently added</h3><a href="updates.php?year=2" class="see-all">See all</a>
                </div>
                <div class="upload-list">
                    <?php
                    // MODIFIED QUERY: We now also fetch the 'category' for YEAR 2
                    $sql = "SELECT id, title, file_link, preview_link, category FROM uploads WHERE academic_year = 2 ORDER BY upload_date DESC LIMIT 3";
                    $result = $conn->query($sql);

                    if ($result && $result->num_rows > 0):
                        while ($row = $result->fetch_assoc()):

                            // --- CONDITIONAL LOGIC STARTS HERE ---
                            if ($row['category'] == 'Routines' && !empty($row['preview_link'])):
                                // If it's a Routine, display it as an image card
                                $image_source = htmlspecialchars($row['preview_link']);
                                $download_link = htmlspecialchars($row['file_link']);
                                ?>
                    <div class="routine-card">
                        <img src="<?php echo $image_source; ?>" alt="<?php echo htmlspecialchars($row['title']); ?>"
                            class="routine-image-full">
                        <div class="routine-footer">
                            <h4 class="routine-title">
                                <?php echo htmlspecialchars($row['title']); ?>
                            </h4>
                            <a href="<?php echo $download_link; ?>" class="download-btn" download>
                                <i class="fas fa-download"></i> Download
                            </a>
                        </div>
                    </div>
                    <?php
                            // The else block from the file
else:
    // For ALL OTHER categories, display the standard list item
?>
<div class="upload-item"
    data-id="<?php echo $row['id']; ?>"
    data-title="<?php echo htmlspecialchars($row['title']); ?>"
    data-preview-link="<?php echo htmlspecialchars($row['preview_link']); ?>"
    data-file-link="<?php echo htmlspecialchars($row['file_link']); ?>"
    data-description="" 
    data-videos-json="[]">
    
    <div class="upload-icon"><i class="fas fa-file-pdf"></i></div>
    <div class="upload-details">
        <h4>
            <a href="#" class="preview-link"
                data-preview-url="<?php echo htmlspecialchars($row['preview_link']); ?>"
                data-download-url="<?php echo htmlspecialchars($row['file_link']); ?>"
                data-title="<?php echo htmlspecialchars($row['title']); ?>">
                <?php echo htmlspecialchars($row['title']); ?>
            </a>
        </h4>
        <p>Uploaded recently</p>
    </div>
    <button class="bookmark-btn" aria-label="Bookmark" data-item-id="<?php echo $row['id']; ?>">
        <i class="far fa-bookmark"></i>
    </button>
    <a href="<?php echo htmlspecialchars($row['file_link']); ?>" class="download-btn"
        aria-label="Download" download>
        <i class="fas fa-download"></i>
    </a>
</div>
<?php
    endif; // --- END OF CONDITIONAL LOGIC ---
                    
                        endwhile;
                    else:
                        echo '<p class="placeholder-text">No items have been uploaded for this year yet.</p>';
                    endif;
                    ?>
                </div>
            </div>
            <!-- ===== END OF SMART "RECENT UPLOADS" SECTION ===== -->
            <!-- ===== Helpful Links Section ===== -->
<div class="helpful-links-section">
    <h4 class="section-title-department">Helpful Links</h4>
    <div class="quick-links">
        <a href="https://www.saadatcollege.gov.bd/" class="quick-link-item">
            <span>Official College Website</span>
            <i class="fas fa-chevron-right"></i>
        </a>
        <a href="https://www.nu.ac.bd/" class="quick-link-item">
            <span>National University (NU) Portal</span>
            <i class="fas fa-chevron-right"></i>
        </a>
    </div>
</div>
        </main>
    </div>

    <!-- ===== NEW: FULLSCREEN ANNOUNCEMENT DETAIL MODAL ===== -->
<div class="fullscreen-modal-backdrop" id="announcement-detail-modal">
    <div class="fullscreen-modal-content">
        <div class="fullscreen-modal-header">
            <button class="modal-action-btn" id="announcement-modal-close-btn" aria-label="Close">
                <i class="fas fa-arrow-left"></i>
            </button>
            <h3 id="modal-announcement-title"></h3>
        </div>
        <div class="fullscreen-modal-body">
            <img src="" alt="Announcement Image" id="modal-announcement-image" style="display:none;">
            <p id="modal-announcement-description"></p>
        </div>
    </div>
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
    <script src="js/script.js?v=<?= filemtime('js/script.js'); ?>"></script>
    <!-- ===== ADDED: KaTeX JavaScript for Math Rendering ===== -->
    <script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/contrib/auto-render.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // NEW: Run the math renderer after the page loads
            renderMathInElement(document.body, {
                delimiters: [
                    {left: '$$', right: '$$', display: true},
                    {left: '$', right: '$', display: false}
                ]
            });
        });
    </script>

    <!-- Swiper.js for the carousel -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // --- Initialize Announcement Carousel ---
        const announcementSwiper = new Swiper('.announcement-swiper-v2', {
            loop: true,
            autoplay: {
                delay: 4000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            effect: 'fade', // Smooth cross-fade effect
            fadeEffect: {
                crossFade: true
            },
        });

        // --- Logic for the new Fullscreen Announcement Modal ---
        const announcementModal = document.getElementById('announcement-detail-modal');
        if (announcementModal) {
            const summaryCards = document.querySelectorAll('.announcement-slide-card');
            const modalImage = document.getElementById('modal-announcement-image');
            const modalTitle = document.getElementById('modal-announcement-title');
            const modalDescription = document.getElementById('modal-announcement-description');
            const closeModalBtn = document.getElementById('announcement-modal-close-btn');

            const openModal = (card) => {
                const title = card.dataset.fullTitle;
                const description = card.dataset.fullDescription;
                const imageUrl = card.dataset.fullImage;
                
                modalTitle.textContent = title;
                modalDescription.innerHTML = description.replace(/\n/g, '<br>');

                if (imageUrl) {
                    modalImage.src = imageUrl;
                    modalImage.style.display = 'block';
                } else {
                    modalImage.style.display = 'none';
                }
                announcementModal.classList.add('is-visible');
                document.body.style.overflow = 'hidden'; // Lock background scroll
            };

            const closeModal = () => {
                announcementModal.classList.remove('is-visible');
                document.body.style.overflow = ''; // Unlock background scroll
            };
            
            summaryCards.forEach(card => {
    card.addEventListener('click', (e) => { // 'e' is the event object
        e.preventDefault(); // <-- ADD THIS LINE
        openModal(card);
    });
});

            closeModalBtn.addEventListener('click', closeModal);
        }
    });
</script>
    
    <?php include 'footer.php'; ?>

</body>
</html>
<?php
if (isset($conn)) { $conn->close(); }
?>