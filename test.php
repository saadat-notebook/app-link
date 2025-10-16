<?php
// assignments.php
require_once 'admin/config.php';

// Security check: Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Determine the academic year from the URL parameter, default to 1
$year_to_show = isset($_GET['year']) ? intval($_GET['year']) : 1;
$year_suffix = ($year_to_show == 1) ? 'st' : 'nd';

// Helper function to parse video URLs (if you need videos for practicals)
function get_video_details($url) {
    $video_id = '';
    if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $url, $matches)) { $video_id = $matches[1]; }
    else if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $matches)) { $video_id = $matches[1]; }
    else if (preg_match('/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/', $url, $matches)) { $video_id = $matches[1]; }
    if ($video_id) { return ['embed_url' => 'https://www.youtube.com/embed/' . $video_id . '?enablejsapi=1', 'thumbnail_url' => 'https://img.youtube.com/vi/' . $video_id . '/mqdefault.jpg']; }
    return ['embed_url' => $url, 'thumbnail_url' => 'https://i.postimg.cc/k47v9xNg/videoplaceholder.png']; // A default placeholder
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignments & Practicals (Year <?php echo $year_to_show; ?>) - Saadat Notebook</title>

    <script>
        (function () { try { const theme = localStorage.getItem('saadatNotesTheme'); const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches; if (theme === 'dark' || (!theme && systemPrefersDark)) { document.documentElement.classList.add('dark-mode'); } const ua = navigator.userAgent || ""; if (ua.includes("wv") || ua.includes("WrapperApp") || ua.includes("saadatnotebook")) { document.documentElement.classList.add("wrapper-app"); } } catch (e) { } })();
    </script>

    <!-- PDF Generation Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <!-- Base Website Styles & Icons -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
        <!-- ===== CSS FOR GENERATOR FULLSCREEN VIEW & RESPONSIVE FORM ===== -->
    <style>
        .generator-promo-card {
            background-color: var(--card-background); border: 1px solid var(--border-color); border-radius: 12px;
            padding: 1.5rem; margin-bottom: 2rem; box-shadow: var(--shadow-sm); text-align: center;
        }
        .generator-promo-card h2 { font-size: 1.3rem; margin-bottom: 0.5rem; color: var(--text-primary); }
        .generator-promo-card p { color: var(--text-secondary); margin-bottom: 1.5rem; max-width: 450px; margin-left: auto; margin-right: auto;}
        .generator-promo-card .btn-primary { 
            padding: 12px 24px; font-size: 1rem; font-weight: 600; cursor: pointer; border: none; border-radius: 8px; 
            background-color: var(--primary-color); color: #fff; transition: background-color 0.2s;
        }
        html.dark-mode .generator-promo-card .btn-primary { color: #0a0a0a; }
        .generator-promo-card .btn-primary:hover { background-color: #003366; }
        
        #generator-view {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background-color: var(--card-background); z-index: 2000; display: flex; flex-direction: column;
            opacity: 0; pointer-events: none; transform: translateY(20px); transition: opacity 0.3s ease, transform 0.3s ease;
        }
        #generator-view.is-visible { opacity: 1; pointer-events: auto; transform: translateY(0); }
        .generator-header {
            display: flex; align-items: center; padding: 0.8rem 1rem; border-bottom: 1px solid var(--border-color); flex-shrink: 0;
        }
        .generator-back-btn { background: none; border: none; font-size: 1.3rem; color: var(--text-secondary); cursor: pointer; padding: 0.5rem; border-radius: 50%; }
        .generator-header h2 { font-size: 1.2rem; margin: 0; margin-left: 1rem; font-weight: 600; }
        .generator-body { flex-grow: 1; overflow-y: auto; padding: 1.5rem; }
        
        /* --- RESPONSIVE FORM LAYOUT --- */
        .generator-form-view .form-card { max-width: 800px; margin: 0 auto; border: none; box-shadow: none; padding: 0; }
        .form-grid-group { margin-bottom: 1.25rem; }
        .form-grid-group label {
            display: block; font-weight: 500; font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 0.5rem;
        }
        .form-grid-group input { 
            width: 100%; background-color: var(--background-color); border: 1px solid var(--border-color);
            border-radius: 8px; padding: 12px; font-size: 1rem; color: var(--text-primary);
        }
        .form-grid-group input:focus { border-color: var(--primary-color); box-shadow: 0 0 0 3px rgba(0,68,136, 0.1); }
        
        /* Desktop grid layout */
        @media (min-width: 576px) {
            .form-grid-group { display: grid; grid-template-columns: 160px 1fr; gap: 1rem; align-items: center; }
            .form-grid-group label { text-align: right; margin-bottom: 0; }
        }

        .form-actions { max-width: 800px; margin: 1.5rem auto 0 auto; padding-bottom: 2rem; /* Bottom spacing */ }
        hr.form-divider { margin: 2rem 0; border: 0; border-top: 1px solid var(--border-color); }
        .hidden { display: none !important; }

        /* --- OTHER STYLES (UNCHANGED) --- */
        .preview-header-text { text-align: center; color: var(--text-secondary); margin-bottom: 1rem; font-weight: 500; }
        .preview-actions { display: flex; flex-direction: column; gap: 0.75rem; margin-top: 1.5rem; max-width: 800px; margin-left:auto; margin-right: auto;}
        @media (min-width: 576px) { .preview-actions { flex-direction: row; } }
        .main-action-btn { font-size: 1rem; font-weight: 600; padding: 14px; margin-top: 0; flex-grow: 1; }
        
        #preview-wrapper { max-width: 800px; margin: 0 auto; position: relative; width: 100%; height: 0; padding-bottom: 141.42%; overflow: hidden; background-color: #ddd; border-radius: 4px; box-shadow: var(--shadow-sm); }
        #cover-preview { position: absolute; top: 0; left: 0; width: 210mm; height: 297mm; background: #fff; color: #000; font-family: 'Times New Roman', Times, serif; display: flex; transform-origin: top left; }
        .cover-outer-border { padding: 5mm; border: 1px solid black; width: 100%; height: 100%; }
        .cover-inner-border { border: 2px solid black; width: 100%; height: 100%; position: relative; overflow: hidden; }
        .cover-watermark { position: absolute; top: 50%; left: 50%; width: 140mm; height: 140mm; transform: translate(-50%, -50%); object-fit: contain; opacity: 0.15; z-index: 1; }
        .cover-content { position: relative; z-index: 2; width: 100%; height: 100%; display: flex; flex-direction: column; padding: 10mm; }
        .cover-header { display: flex; justify-content: space-between; align-items: flex-start; }
        .header-text h1 { font-family: sans-serif; font-size: 22pt; font-weight: bold; margin: 10mm 1mm 1mm 5mm; letter-spacing: 1px; }
        .header-text p { font-family: sans-serif; font-size: 22pt; margin: 1mm 1mm 1mm 5mm; }
        .header-logo { width: 37mm; height: auto; margin: 10mm 5mm 1mm 1mm; }
        .cover-body { flex-grow: 1; display: flex; flex-direction: column; margin: 1mm; }
        .notebook-title { font-size: 22pt; font-weight: bold; margin: 15mm 1mm 1mm 5mm; position: relative; }
        .details-table, .submitted-by-table { border-collapse: collapse; font-size: 18pt; width: 100%; max-width: 140mm; margin: 5mm 1mm 1mm 5mm; }
        .details-table td, .submitted-by-table td { padding: 1px 1px 1px 10px; }
        .details-table td:first-child, .submitted-by-table td:first-child { width: 45%; padding-right: 1mm; }
        .details-table td:nth-child(2), .submitted-by-table td:nth-child(2) { width: 12%; }
        .submitted-by-section { margin-top: 35mm; }
        .submitted-by-title { font-size: 18pt; font-style: italic; margin-bottom: 5mm; }
        .loader-spinner { width: 20px; height: 20px; border: 3px solid rgba(255, 255, 255, 0.3); border-top-color: #fff; border-radius: 50%; animation: spin 1s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>

<body>
    <script>
    (function() {
        try {
            // Check for a session cookie to determine if the user is likely logged in
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
            <h1 class="page-title">Assignments & Practicals - Honours <?php echo $year_to_show . $year_suffix; ?> Year</h1>
            <div class="generator-promo-card">
                <h2>Cover Page Generator</h2>
                <p>Cover page for your Practical Notebook</p>
                <button id="open-generator-btn" class="btn-primary"><i class="fas fa-file-invoice"></i> Generate Now</button>
            </div>

            <div class="section-title" style="margin-bottom: 1.5rem;">
                <h3>Uploaded Assignments & Practicals</h3>
            </div>
            <?php
            // Fetch assignments from the database for the specified year and new category
            $sql = "SELECT id, title, file_link, preview_link, description, video_links_json
                    FROM uploads 
                    WHERE category = 'Assignments & Practicals' AND academic_year = $year_to_show 
                    ORDER BY upload_date DESC";

            $result = $conn->query($sql);
            
            echo '<div class="content-card-list">'; 

            if ($result && $result->num_rows > 0):
                while ($row = $result->fetch_assoc()):
                    // Decode video JSON data
                    $videos = json_decode($row['video_links_json'] ?? '[]', true);
                    if (!is_array($videos)) { $videos = []; } // Ensure it's an array
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
                                <h5>Video Practicals / Guides</h5>
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
                echo '<p class="placeholder-text">No assignments have been uploaded for this year yet.</p>';
            endif;
            echo '</div>'; 
            ?>
        </main>
    </div>

    <!-- Reusable Preview Modal -->
    <div class="modal-overlay" id="preview-modal">
        <div class="modal-content"><div class="preview-header"><button class="preview-action-btn" id="preview-back-btn" aria-label="Close Preview"><i class="fas fa-arrow-left"></i></button><h3 id="preview-title"></h3><div class="preview-actions"><a href="#" id="preview-download-btn" class="preview-action-btn" aria-label="Download File" download><i class="fas fa-download"></i></a></div></div><iframe src="" frameborder="0"></iframe></div>
    </div>
        <!-- FULLSCREEN VIEW FOR GENERATOR -->
    <div id="generator-view">
        <div class="generator-header">
            <button id="generator-back-btn" class="generator-back-btn" aria-label="Go Back"><i class="fas fa-arrow-left"></i></button>
            <h2>Cover Page Generator</h2>
            <p>Cover page for Practical Notebook</p>
        </div>
        <div class="generator-body">
            <div class="generator-form-view" id="generator-form-view">
                <div class="form-card">
                    <form id="cover-form">
                        <div class="form-grid-group"><label for="department">Department</label><input type="text" id="department" value="Mathematics"></div>
                        <div class="form-grid-group"><label for="course-title">Course Title</label><input type="text" id="course-title" value="Chemistry-I Practical"></div>
                        <div class="form-grid-group"><label for="course-code">Course Code</label><input type="text" id="course-code" value="212808"></div>
                        <div class="form-grid-group"><label for="session">Session</label><input type="text" id="session" value="2023-24"></div>
                        <hr class="form-divider">
                        <div class="form-grid-group"><label for="name">Name</label><input type="text" id="name" placeholder="Enter your full name"></div>
                        <div class="form-grid-group"><label for="academic-year">Academic Year</label><input type="text" id="academic-year" value="Hons. 1st year"></div>
                        <div class="form-grid-group"><label for="exam-code">Examination Code</label><input type="text" id="exam-code" placeholder="e.g., 2201"></div>
                        <div class="form-grid-group"><label for="class-roll">Class Roll No.</label><input type="text" id="class-roll" placeholder="e.g., 50"></div>
                        <div class="form-grid-group"><label for="roll-no">Roll No.</label><input type="text" id="roll-no" placeholder="University roll number"></div>
                        <div class="form-grid-group"><label for="reg-no">Registration No.</label><input type="text" id="reg-no" placeholder="University registration number"></div>
                        <div class="form-actions">
                             <button id="generate-btn" type="submit" class="main-action-btn btn-primary"><i class="fas fa-file-invoice"></i> Generate Cover Page</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="generator-preview-view hidden" id="generator-preview-view">
                 <h3 class="preview-header-text">Generated Preview</h3>
                 <div id="preview-wrapper">
                    <div id="cover-preview">
                         <div class="cover-outer-border">
                             <div class="cover-inner-border">
                                 <img src="images/college-logo.png" class="cover-watermark" alt="">
                                 <div class="cover-content">
                                    <header class="cover-header">
                                        <div class="header-text"><h1>GOVT. SAADAT COLLEGE</h1><p>Karatia, Tangail.</p></div>
                                        <img src="images/college-logo.png" alt="College Logo" class="header-logo">
                                    </header>
                                    <div class="cover-body">
                                        <h2 class="notebook-title">Practical Notebook</h2>
                                        <table class="details-table"><tbody>
                                            <tr><td>Department</td><td>:</td><td id="preview-department"></td></tr>
                                            <tr><td>Course Title</td><td>:</td><td id="preview-course-title"></td></tr>
                                            <tr><td>Course Code</td><td>:</td><td id="preview-course-code"></td></tr>
                                            <tr><td>Session</td><td>:</td><td id="preview-session"></td></tr>
                                        </tbody></table>
                                        <footer class="submitted-by-section">
                                            <p class="submitted-by-title">Submitted by &mdash;</p>
                                            <table class="submitted-by-table"><tbody>
                                                <tr><td>Name</td><td>:</td><td id="preview-name"></td></tr>
                                                <tr><td>Academic Year</td><td>:</td><td id="preview-academic-year"></td></tr>
                                                <tr><td>Examination Code</td><td>:</td><td id="preview-exam-code"></td></tr>
                                                <tr><td>Class Roll No.</td><td>:</td><td id="preview-class-roll"></td></tr>
                                                <tr><td>Roll No.</td><td>:</td><td id="preview-roll-no"></td></tr>
                                                <tr><td>Registration No.</td><td>:</td><td id="preview-reg-no"></td></tr>
                                            </tbody></table>
                                        </footer>
                                    </div>
                                 </div>
                             </div>
                         </div>
                    </div>
                </div>
                <div class="preview-actions">
                    <button id="generate-again-btn" class="main-action-btn btn-secondary"><i class="fas fa-pencil-alt"></i> Edit Details</button>
                    <button id="download-btn" class="main-action-btn btn-primary"><i class="fas fa-file-pdf"></i> Download as PDF</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="js/global.js?v=<?= filemtime('js/global.js'); ?>"></script>
    <script src="js/bookmarks.js?v=<?= filemtime('js/bookmarks.js'); ?>"></script>
    
    <script>
    // This script is identical to the one in lectures.php for toggling cards and controlling videos
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
                // Stop YouTube video when closing card
                const iframe = card.querySelector('.video-player-wrapper iframe');
                if (iframe && iframe.src.includes('youtube')) {
                    iframe.contentWindow.postMessage('{"event":"command","func":"stopVideo","args":""}', '*');
                }
            }
        });

        // Video playlist logic
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
        const { jsPDF } = window.jspdf;
        const openGeneratorBtn = document.getElementById('open-generator-btn');
        const generatorView = document.getElementById('generator-view');
        const backBtn = document.getElementById('generator-back-btn');

        const formView = document.getElementById('generator-form-view');
        const previewView = document.getElementById('generator-preview-view');
        const coverForm = document.getElementById('cover-form');
        const generateAgainBtn = document.getElementById('generate-again-btn');
        const downloadBtn = document.getElementById('download-btn');
        const previewWrapper = document.getElementById('preview-wrapper');
        const coverPreview = document.getElementById('cover-preview');
        const localStorageKey = 'saadatCoverGeneratorData';
        
        const fields = ['department', 'course-title', 'course-code', 'session', 'name', 'academic-year', 'exam-code', 'class-roll', 'roll-no', 'reg-no'];

        const openGenerator = () => {
            generatorView.classList.add('is-visible');
            document.body.style.overflow = 'hidden';
            setTimeout(scalePreview, 50);
        };
        const closeGenerator = () => {
            generatorView.classList.remove('is-visible');
            document.body.style.overflow = '';
            previewView.classList.add('hidden');
            formView.classList.remove('hidden');
        };

        openGeneratorBtn.addEventListener('click', openGenerator);
        backBtn.addEventListener('click', closeGenerator);

        const scalePreview = () => {
            if (!previewWrapper || !coverPreview) return;
            const wrapperWidth = previewWrapper.clientWidth;
            const previewBaseWidth = coverPreview.offsetWidth;
            if (previewBaseWidth === 0) return;
            const scale = wrapperWidth / previewBaseWidth;
            coverPreview.style.transform = `scale(${scale})`;
        };

        const saveData = () => {
            const data = {};
            fields.forEach(id => data[id] = document.getElementById(id).value);
            localStorage.setItem(localStorageKey, JSON.stringify(data));
        };

        const loadData = () => {
            try {
                const savedData = JSON.parse(localStorage.getItem(localStorageKey));
                if (!savedData) return;
                fields.forEach(id => {
                    if (savedData[id]) document.getElementById(id).value = savedData[id];
                });
            } catch (e) { console.warn("Could not load saved data."); }
        };
        fields.forEach(id => document.getElementById(id).addEventListener('input', saveData));

        const updateAllPreviews = () => {
            fields.forEach(id => {
                const value = document.getElementById(id).value.trim();
                const previewEl = document.getElementById(`preview-${id}`);
                if (previewEl) previewEl.textContent = value || '';
            });
        };

        coverForm.addEventListener('submit', (e) => {
            e.preventDefault();
            updateAllPreviews();
            formView.classList.add('hidden');
            previewView.classList.remove('hidden');
            setTimeout(scalePreview, 50);
        });

        generateAgainBtn.addEventListener('click', () => {
            previewView.classList.add('hidden');
            formView.classList.remove('hidden');
        });

        downloadBtn.addEventListener('click', () => {
            const originalBtnHTML = downloadBtn.innerHTML;
            downloadBtn.disabled = true;
            downloadBtn.innerHTML = '<div class="loader-spinner"></div> Creating PDF...';
            coverPreview.style.transform = 'scale(1)';

            html2canvas(coverPreview, { scale: 3, useCORS: true, logging: false }).then(canvas => {
                const imgData = canvas.toDataURL('image/png', 1.0);
                const pdf = new jsPDF('p', 'mm', 'a4');
                pdf.addImage(imgData, 'PNG', 0, 0, 210, 297, '', 'FAST');
                const name = document.getElementById('name').value.trim() || 'student';
                pdf.save(`${name}_Practical_Cover.pdf`);
            }).catch(err => {
                console.error('PDF Generation Error:', err);
                alert('An error occurred while creating the PDF.');
            }).finally(() => {
                scalePreview();
                downloadBtn.disabled = false;
                downloadBtn.innerHTML = originalBtnHTML;
            });
        });

        loadData();
        window.addEventListener('resize', scalePreview);
    });
    </script>
</body>
</html>
<?php
// Close the database connection
if (isset($conn)) { $conn->close(); }
?>