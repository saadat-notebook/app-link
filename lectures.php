<?php
// lectures.php (CORRECTED & SECURED VERSION 2.0)
require_once 'admin/config.php';

// --- THE FIRST FIX: Add the security check at the top ---
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Helper function to parse video URLs and get details (unchanged)
function get_video_details($url) {
    $video_id = '';
    if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $url, $matches)) { $video_id = $matches[1]; } 
    else if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $matches)) { $video_id = $matches[1]; }
    else if (preg_match('/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/', $url, $matches)) { $video_id = $matches[1]; }
    if ($video_id) { return ['embed_url' => 'https://www.youtube.com/embed/' . $video_id . '?enablejsapi=1', 'thumbnail_url' => 'https://img.youtube.com/vi/' . $video_id . '/mqdefault.jpg']; }
    return ['embed_url' => $url, 'thumbnail_url' => 'https://i.postimg.cc/k47v9xNg/videoplaceholder.png'];
}

$year_to_show = isset($_GET['year']) ? intval($_GET['year']) : 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Lectures (Year <?php echo $year_to_show; ?>) - Saadat Notebook</title>

    <script>
        (function () { try { const theme = localStorage.getItem('saadatNotesTheme'); const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches; if (theme === 'dark' || (!theme && systemPrefersDark)) { document.documentElement.classList.add('dark-mode'); } const ua = navigator.userAgent || ""; if (ua.includes("wv") || ua.includes("WrapperApp") || ua.includes("saadatnotebook")) { document.documentElement.classList.add("wrapper-app"); } } catch (e) { } })();
    </script>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
</head>

<body>
    <script>
    (function() {
        try {
            if (document.cookie.includes("PHPSESSID")) {
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
    <header class="header">
        <a href="index.php" class="logo-link"><div class="logo-container"><img src="images/logo.png" alt="Saadat Notebook Logo" class="site-logo"><div class="logo-text"><h1>Saadat Notebook</h1><p>Department of Mathematics</p></div></div></a>
        <div class="header-actions"><button class="search-icon-trigger" aria-label="Open Search"><i class="fas fa-search"></i></button><button class="menu-icon" aria-label="Open Menu"><i class="fas fa-bars"></i></button></div>
    </header>
    <div id="search-view">
        <div class="search-view-header"><button id="search-back-btn" aria-label="Go Back"><i class="fas fa-arrow-left"></i></button><div class="search-input-container"><i class="fas fa-search"></i><input type="text" id="search-view-input" placeholder="Search by title or subject name..."></div></div>
        <div class="search-view-body"><div class="search-placeholder"><p>Start typing to search...</p></div></div>
    </div>

    <div class="page-wrapper animate__animated animate__fadeIn">
        <main>
            <h1 class="page-title">Class Lectures - Honours <?php echo $year_to_show . (($year_to_show == 1) ? 'st' : 'nd'); ?> Year</h1>

            <?php
            // Your entire PHP loop for fetching and displaying content is correct and preserved.
            $sql = "SELECT id, title, file_link, preview_link, description, video_links_json
                    FROM uploads 
                    WHERE category = 'Lectures' AND academic_year = $year_to_show 
                    ORDER BY upload_date DESC";

            $result = $conn->query($sql);
            
            echo '<div class="content-card-list">'; 

            if ($result && $result->num_rows > 0):
                while ($row = $result->fetch_assoc()):
                    $videos = json_decode($row['video_links_json'] ?? '[]', true);
                    if (!is_array($videos)) { $videos = []; }
                    $has_videos = !empty($videos);
                    $has_files = !empty($row['file_link']);
                    $has_description = !empty(trim($row['description']));
            ?>
            
            <div class="content-card"
    data-id="<?php echo $row['id']; ?>"
    data-title="<?php echo htmlspecialchars($row['title']); ?>"
    data-preview-link="<?php echo htmlspecialchars($row['preview_link']); ?>"
    data-file-link="<?php echo htmlspecialchars($row['file_link']); ?>"
    data-description="<?php echo htmlspecialchars($row['description']); ?>"
    data-videos-json='<?php echo htmlspecialchars($row['video_links_json'] ?? '[]', ENT_QUOTES, 'UTF-8'); ?>'>
                <div class="card-header">
                    <h4><?php echo htmlspecialchars($row['title']); ?></h4>
                    <i class="fas fa-chevron-down toggle-icon"></i>
                </div>
                <div class="card-body">
                    <div class="card-content-wrapper">
                        <?php if ($has_description): ?><p class="card-description"><?php echo nl2br(htmlspecialchars($row['description'])); ?></p><?php endif; ?>
                        
                        <?php if ($has_files): ?>
                            <div class="card-actions">
                                <a href="#" class="btn btn-preview preview-link" 
                                   data-preview-url="<?php echo htmlspecialchars($row['preview_link']); ?>"
                                   data-download-url="<?php echo htmlspecialchars($row['file_link']); ?>" 
                                   data-title="<?php echo htmlspecialchars($row['title']); ?>">
                                    <i class="fas fa-eye"></i> Preview
                                </a>
                                <a href="<?php echo htmlspecialchars($row['file_link']); ?>" class="btn btn-download download-btn" download>
                                    <i class="fas fa-download"></i> Download
                                </a>
                                <button class="btn btn-bookmark bookmark-btn" aria-label="Bookmark" data-item-id="<?php echo $row['id']; ?>">
                                    <i class="far fa-bookmark"></i> <span>Bookmark</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <?php if ($has_videos): ?>
                            <div class="video-playlist-section" data-videos='<?php echo htmlspecialchars(json_encode($videos), ENT_QUOTES, 'UTF-8'); ?>'>
                                <h5>Video Lectures</h5>
                                <?php $first_video_details = get_video_details($videos[0]['url']); ?>
                                <div class="video-player-wrapper">
                                    <iframe src="<?php echo htmlspecialchars($first_video_details['embed_url']); ?>" 
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; fullscreen" 
                                            allowfullscreen></iframe>
                                </div>
                                <div class="video-player-controls">
                                    <button class="prev-video-btn"><i class="fas fa-chevron-left"></i></button>
                                    <span class="video-current-title"><?php echo htmlspecialchars($videos[0]['title']); ?></span>
                                    <button class="next-video-btn"><i class="fas fa-chevron-right"></i></button>
                                </div>
                                <div class="video-thumbnail-reel">
                                    <?php foreach ($videos as $index => $video): $video_details = get_video_details($video['url']); ?>
                                        <div class="video-thumbnail <?php echo ($index === 0) ? 'active' : ''; ?>" data-index="<?php echo $index; ?>"><img src="<?php echo htmlspecialchars($video_details['thumbnail_url']); ?>" alt="<?php echo htmlspecialchars($video['title']); ?>" loading="lazy"></div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <?php
                endwhile;
            else:
                echo '<p>No lectures have been uploaded for this year yet.</p>';
            endif;
            echo '</div>'; 
            ?>
        </main>
    </div>

    <div class="modal-overlay" id="preview-modal">
        <div class="modal-content"><div class="preview-header"><button class="preview-action-btn" id="preview-back-btn" aria-label="Close Preview"><i class="fas fa-arrow-left"></i></button><h3 id="preview-title"></h3><div class="preview-actions"><a href="#" id="preview-download-btn" class="preview-action-btn" aria-label="Download File" download><i class="fas fa-download"></i></a></div></div><iframe src="" frameborder="0"></iframe></div>
    </div>
    <script src="js/config.js"></script>
    <script src="js/user-session.js"></script> 
    <script src="js/global.js"></script>
    <script src="js/bookmarks.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const mainContent = document.querySelector('main');
        if (!mainContent) return;

        mainContent.addEventListener('click', function(e) {
            const header = e.target.closest('.card-header');
            if (!header) return;
            const card = header.closest('.content-card');
            const body = card.querySelector('.card-body');
            
            card.classList.toggle('is-open');

            if (card.classList.contains('is-open')) { 
                body.style.maxHeight = body.scrollHeight + 'px';
            } else { 
                body.style.maxHeight = '0';
                const iframe = card.querySelector('.video-player-wrapper iframe');
                if (iframe && iframe.src.includes('youtube')) {
                    iframe.contentWindow.postMessage('{"event":"command","func":"stopVideo","args":""}', '*');
                }
            }
        });

        document.querySelectorAll('.video-playlist-section').forEach(playlist => {
            const videos = JSON.parse(playlist.dataset.videos);
            if (videos.length === 0) return;
            let currentIndex = 0;
            const iframe = playlist.querySelector('iframe');
            const currentTitleEl = playlist.querySelector('.video-current-title');
            const prevBtn = playlist.querySelector('.prev-video-btn');
            const nextBtn = playlist.querySelector('.next-video-btn');
            const thumbnails = playlist.querySelectorAll('.video-thumbnail');
            function updatePlayer(index) {
                currentIndex = index;
                const videoData = videos[index];
                const videoDetailsForJS = get_video_details_js(videoData.url);
                iframe.src = videoDetailsForJS.embed_url;
                currentTitleEl.textContent = videoData.title;
                thumbnails.forEach(thumb => thumb.classList.remove('active'));
                const activeThumb = playlist.querySelector(`.video-thumbnail[data-index="${index}"]`);
                if(activeThumb) activeThumb.classList.add('active');
                if(activeThumb) activeThumb.scrollIntoView({ behavior: 'smooth', block: 'nearest', 'inline': 'center' });
                prevBtn.disabled = (index === 0);
                nextBtn.disabled = (index === videos.length - 1);
            }
            if (videos.length > 0) {
                 updatePlayer(0);
            }
            prevBtn.addEventListener('click', () => { if (currentIndex > 0) updatePlayer(currentIndex - 1); });
            nextBtn.addEventListener('click', () => { if (currentIndex < videos.length - 1) updatePlayer(currentIndex + 1); });
            thumbnails.forEach(thumb => { thumb.addEventListener('click', () => { const index = parseInt(thumb.dataset.index, 10); updatePlayer(index); }); });
        });

        function get_video_details_js(url) {
            let video_id = '', matches;
            if (matches = url.match(/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/)) { video_id = matches[1]; } 
            else if (matches = url.match(/youtu\.be\/([a-zA-Z0-9_-]+)/)) { video_id = matches[1]; } 
            else if (matches = url.match(/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/)) { video_id = matches[1]; }
            if (video_id) { return { embed_url: 'https://www.youtube.com/embed/' + video_id + '?enablejsapi=1' }; }
            return { embed_url: url };
        }
        
        // --- THE SECOND FIX: Remove the conflicting listener call ---
        // This line is no longer needed because bookmarks.js handles its own initialization.
        // setupBookmarkListeners(mainContent); // DELETE THIS LINE
    });
    </script>
</body>
</html>
<?php
if (isset($conn)) { $conn->close(); }
?>