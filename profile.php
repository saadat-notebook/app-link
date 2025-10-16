<?php
// profile.php (FINAL FILTERING VERSION 2.0 - COMPLETE)
require_once 'admin/config.php';

// --- Security Check & Data Fetching ---
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch user data from the session for the page header
$userName = $_SESSION['user_name'];
$userYearText = ($_SESSION['academic_year'] == 2) ? 'Honours 2nd Year' : 'Honours 1st Year';
$userEmail = ''; // Initialize email

// We also need to fetch the email fresh from the database for display
$stmt = $conn->prepare("SELECT email FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$user_result = $stmt->get_result();
$user_db_data = $user_result->fetch_assoc();
$stmt->close();
if ($user_db_data) {
    $userEmail = $user_db_data['email'];
} else {
    // If user somehow doesn't exist in DB but has a session, log them out.
    header('Location: logout.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Saadat Notebook</title>

    <script> (function () { try { const theme = localStorage.getItem('saadatNotesTheme'); const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches; if (theme === 'dark' || (!theme && systemPrefersDark)) { document.documentElement.classList.add('dark-mode'); } } catch (e) { } })(); </script>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/filter-bar.css"> <!-- Added filter bar styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style> .profile-edit-link { color: var(--text-primary); font-weight: bold; text-decoration: none; } </style>
    <!-- ADD THIS STYLE BLOCK TO THE <head> SECTION -->
<style>
    .profile-actions {
        display: grid;
        grid-template-columns: 1fr 1fr; /* Two equal columns */
        gap: 1rem;
        margin-top: 1.5rem;
    }
    .profile-action-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        padding: 14px;
        border-radius: 12px;
        font-size: 1rem;
        font-weight: 600;
        text-decoration: none;
        text-align: center;
        transition: all 0.2s ease-in-out;
    }
    .btn-edit-profile {
        background-color: var(--primary-light);
        color: var(--text-primary);
        border: 1px solid var(--border-color);
    }
    .btn-edit-profile:hover {
        background-color: #e2e8f0; /* Slightly darker hover */
        transform: translateY(-2px);
    }
    html.dark-mode .btn-edit-profile:hover {
        background-color: #373737;
    }
    .btn-logout {
        background-color: transparent;
        color: #e53935; /* Danger Red */
        border: 1px solid #e53935;
    }
    .btn-logout:hover {
        background-color: #e53935;
        color: #ffffff;
        transform: translateY(-2px);
    }
</style>
<!-- END OF NEW STYLE BLOCK -->
</head>

<body>
    <script> (function () { const bottomNavHTML = `<nav class="bottom-nav"><a href="index.php" class="nav-link nav-home-link"><i class="fas fa-home"></i><span>Dashboard</span></a><a href="profile.php" class="nav-link"><i class="fas fa-user-circle"></i><span>Profile</span></a><a href="bcs-math.html" class="nav-link"><i class="fas fa-square-root-alt"></i><span>BCS Maths</span></a></nav>`; document.write(bottomNavHTML); })(); </script>

    <header class="header"> <a href="index.php" class="logo-link"><div class="logo-container"><img src="images/logo.png" alt="Saadat Notebook Logo" class="site-logo"><div class="logo-text"><h1>Saadat Notebook</h1><p>Department of Mathematics</p></div></div></a><div class="header-actions"><button class="menu-icon" aria-label="Open Menu"><i class="fas fa-bars"></i></button></div></header>

    <div class="page-wrapper animate__animated animate__fadeIn">
        <main>
            <div class="user-card">
                <div class="welcome-header"><h2>My Profile</h2></div>
                <div class="user-info">
                    <div class="avatar-container"><img src="images/avatar.jpg" alt="Avatar" class="avatar" id="user-avatar-img"></div>
                    <div class="user-details">
                        <span class="user-name"><?php echo htmlspecialchars($userName); ?></span>
                        <span class="user-year"><?php echo htmlspecialchars($userYearText); ?></span>
                    </div>
                </div>
                <!-- REPLACE WITH THIS NEW BLOCK -->
<div class="user-meta">
    <div class="meta-item"><i class="fas fa-envelope"></i><span><?php echo htmlspecialchars($userEmail); ?></span></div>
</div>
</div> <!-- This closes the .user-card div -->

<!-- New Action Button Grid -->
<div class="profile-actions">
    <a href="edit-profile.php" class="profile-action-btn btn-edit-profile">
        <i class="fas fa-pencil-alt"></i>
        <span>Edit Profile</span>
    </a>
    <a href="logout.php" id="logout-btn-trigger" class="profile-action-btn btn-logout">
        <i class="fas fa-sign-out-alt"></i>
        <span>Log Out</span>
    </a>
</div>
<!-- END OF REPLACEMENT -->

            <div id="bookmarks-section">
                <div class="section-title" style="margin-top: 2rem;"><h3>My Saved Items</h3></div>
                <div id="bookmarks-filter-container"></div> <!-- Filter buttons will be inserted here -->
                <div id="bookmarks-list-container"> <!-- The list of items will be inserted here -->
                    <p style="text-align: center; color: var(--text-secondary); padding: 1rem;">Loading saved items...</p>
                </div>
            </div>
        </main>
    </div>

    <!-- The preview modal is necessary for the preview links to work -->
    <div class="modal-overlay" id="preview-modal">
         <div class="modal-content">
            <div class="preview-header">
                <button class="preview-action-btn" id="preview-back-btn" aria-label="Close Preview"><i class="fas fa-arrow-left"></i></button>
                <h3 id="preview-title"></h3>
                <div class="preview-actions"><a href="#" id="preview-download-btn" class="preview-action-btn" aria-label="Download" download><i class="fas fa-download"></i></a></div>
            </div>
            <iframe src="" frameborder="0"></iframe>
        </div>
    </div>
    
    <!-- Load scripts in the correct, modular order -->
    <script src="js/config.js"></script>
    <script src="js/user-session.js"></script>
    <script src="js/global.js"></script>
    <script src="js/bookmarks.js"></script>

    <!-- Page-specific script for rendering bookmarks -->
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const filterContainer = document.getElementById('bookmarks-filter-container');
        const listContainer = document.getElementById('bookmarks-list-container');
        let allBookmarksData = []; // Cache for all fetched bookmark items

        // Function to load the custom profile picture from localStorage
        window.NotebookSession.onReady(() => {
            const customPic = localStorage.getItem('saadatNotesCustomUserPic');
            const userAvatarImg = document.getElementById('user-avatar-img');
            if (userAvatarImg && customPic) { userAvatarImg.src = customPic; }
        });
        
        // Main function to fetch data and initiate rendering
        async function loadAndRenderBookmarks() {
            try {
                const response = await fetch('api/get-full-bookmarks.php');
                const data = await response.json();

                if (data.success && data.bookmarks.length > 0) {
                    allBookmarksData = data.bookmarks;
                    const categories = [...new Set(allBookmarksData.map(item => item.category))].sort();
                    
                    renderFilterBar(categories);
                    renderBookmarkList(allBookmarksData);
                    activateInteractiveContent(listContainer);
                } else {
                    document.getElementById('bookmarks-section').innerHTML = '<div class="section-title" style="margin-top: 2rem;"><h3>My Saved Items</h3></div><p style="text-align: center; color: var(--text-secondary); padding: 1rem;">You have no saved items.</p>';
                }
            } catch (e) {
                listContainer.innerHTML = `<p style="text-align: center; color: var(--text-secondary); padding: 1rem;">Error: Could not load saved items.</p>`;
                console.error("Error loading bookmarks:", e);
            }
        }

        // Creates the HTML for the filter buttons
        function renderFilterBar(categories) {
            let filterHtml = `<div class="subject-filter-container"><div class="filter-buttons-grid">
                <button class="filter-btn active" data-category="all">All</button>`;
            categories.forEach(cat => {
                const safeCategory = cat.replace(/"/g, '&quot;');
                filterHtml += `<button class="filter-btn" data-category="${safeCategory}">${safeCategory}</button>`;
            });
            filterHtml += `</div></div>`;
            filterContainer.innerHTML = filterHtml;
            
            filterContainer.querySelectorAll('.filter-btn').forEach(button => {
                button.addEventListener('click', handleFilterClick);
            });
        }
        
        // Handles multi-select filter logic
        function handleFilterClick(event) {
            const clickedBtn = event.currentTarget;
            const category = clickedBtn.dataset.category;
            const allBtn = filterContainer.querySelector('[data-category="all"]');

            if (category === 'all') {
                const isActive = clickedBtn.classList.contains('active');
                filterContainer.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
                if (!isActive) clickedBtn.classList.add('active'); // If it wasn't active, activate it.
            } else {
                allBtn.classList.remove('active');
                clickedBtn.classList.toggle('active');
            }

            if (filterContainer.querySelectorAll('.filter-btn.active').length === 0) {
                allBtn.classList.add('active');
            }
            filterItems();
        }

        // Shows/hides items based on active filters
        function filterItems() {
             const activeFilters = [...filterContainer.querySelectorAll('.filter-btn.active')].map(btn => btn.dataset.category);
             const allItems = listContainer.querySelectorAll('.content-card');
             if (activeFilters.includes('all')) {
                 allItems.forEach(item => item.style.display = 'block');
                 return;
             }
             allItems.forEach(item => {
                 item.style.display = activeFilters.includes(item.dataset.category) ? 'block' : 'none';
             });
        }
        
        // Renders the list of cards into the DOM
        function renderBookmarkList(bookmarks) {
            const cardList = document.createElement('div');
            cardList.className = 'content-card-list';
            bookmarks.forEach(item => {
                cardList.innerHTML += buildContentCardHTML(item);
            });
            listContainer.innerHTML = ''; // Clear "loading" message
            listContainer.appendChild(cardList);
        }
        
        // Helper function to build the HTML string for a single content card
        function buildContentCardHTML(item) {
            const videos = JSON.parse(item.video_links_json || '[]');
            const hasVideos = videos.length > 0;
            const hasFiles = item.file_link && item.file_link !== '#';
            const hasDescription = item.description && item.description.trim() !== '';
            
            const title = item.title ? item.title.replace(/"/g, '&quot;') : 'Untitled';
            const description = item.description ? item.description.replace(/\n/g, '<br>') : '';
            
            return `
            <div class="content-card" data-category="${item.category}"
                data-id="${item.id}" data-title="${title}" data-preview-link="${item.preview_link || '#'}" data-file-link="${item.file_link || '#'}" data-description="${item.description || ''}" data-videos-json='${item.video_links_json || '[]'}'>
                <div class="card-header"><h4>${item.title}</h4><i class="fas fa-chevron-down toggle-icon"></i></div>
                <div class="card-body">
                    <div class="card-content-wrapper">
                        ${hasDescription ? `<p class="card-description">${description}</p>` : ''}
                        ${hasFiles ? `
                            <div class="card-actions">
                                <a href="#" class="btn btn-preview preview-link" data-preview-url="${item.preview_link}" data-download-url="${item.file_link}" data-title="${title}"><i class="fas fa-eye"></i> Preview</a>
                                <a href="${item.file_link}" class="btn btn-download download-btn" download><i class="fas fa-download"></i> Download</a>
                                <button class="btn btn-bookmark bookmark-btn active" aria-label="Remove Bookmark" data-item-id="${item.id}"><i class="fas fa-bookmark"></i> <span>Unbookmark</span></button>
                            </div>` : ''}
                        ${hasVideos ? `<div class="video-playlist-section" data-videos='${JSON.stringify(videos)}'></div>` : ''}
                    </div>
                </div>
            </div>`;
        }

        // This master function finds and activates all interactive JS elements on the newly rendered cards
        function activateInteractiveContent(container) {
            window.setupPreviewLinks(container); 
            setupDownloadListeners(container);
            
            // Accordion Logic to expand/collapse cards
            container.querySelectorAll('.card-header').forEach(header => {
                header.addEventListener('click', (e) => {
                    if (e.target.closest('button')) return; // Don't trigger if a button in the header was clicked
                    const card = header.closest('.content-card');
                    const body = card.querySelector('.card-body');
                    card.classList.toggle('is-open');
                    body.style.maxHeight = card.classList.contains('is-open') ? body.scrollHeight + 'px' : '0';
                });
            });

            // Video Player Logic
            container.querySelectorAll('.video-playlist-section').forEach(playlist => {
                const videos = JSON.parse(playlist.dataset.videos);
                if(videos.length > 0) {
                    let videoHtml = `<h5>Video Lectures</h5>
                    <div class="video-player-wrapper"><iframe src="" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture; fullscreen"></iframe></div>
                    <div class="video-player-controls"><button class="prev-video-btn"><i class="fas fa-chevron-left"></i></button><span class="video-current-title"></span><button class="next-video-btn"><i class="fas fa-chevron-right"></i></button></div>
                    <div class="video-thumbnail-reel">
                        ${videos.map((video, index) => { const thumbUrl = `https://img.youtube.com/vi/${getYouTubeId(video.url)}/mqdefault.jpg`; return `<div class="video-thumbnail" data-index="${index}"><img src="${thumbUrl}" alt="${video.title}"></div>`; }).join('')}
                    </div>`;
                    playlist.innerHTML = videoHtml;
                    activateVideoPlayer(playlist, videos);
                }
            });

            function getYouTubeId(url) { let id = ''; if(url){ const match = url.match(/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/); if (match && match[1]) { id = match[1]; } } return id; }
            function activateVideoPlayer(playlistEl, videos) {
                 let currentIndex = 0;
                 const iframe = playlistEl.querySelector('iframe');
                 const titleEl = playlistEl.querySelector('.video-current-title');
                 const prevBtn = playlistEl.querySelector('.prev-video-btn');
                 const nextBtn = playlistEl.querySelector('.next-video-btn');
                 const thumbnails = playlistEl.querySelectorAll('.video-thumbnail');
                 function updatePlayer(index) {
                     currentIndex = index;
                     const embedUrl = `https://www.youtube.com/embed/${getYouTubeId(videos[index].url)}?enablejsapi=1`;
                     iframe.src = embedUrl;
                     titleEl.textContent = videos[index].title;
                     thumbnails.forEach(t => t.classList.remove('active'));
                     const activeThumb = playlistEl.querySelector(`.video-thumbnail[data-index="${index}"]`);
                     if (activeThumb) activeThumb.classList.add('active');
                     if (activeThumb) activeThumb.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                     prevBtn.disabled = index === 0;
                     nextBtn.disabled = index === (videos.length - 1);
                 }
                 updatePlayer(0);
                 prevBtn.addEventListener('click', () => { if (currentIndex > 0) updatePlayer(currentIndex - 1) });
                 nextBtn.addEventListener('click', () => { if (currentIndex < videos.length - 1) updatePlayer(currentIndex + 1) });
                 thumbnails.forEach(t => t.addEventListener('click', () => updatePlayer(parseInt(t.dataset.index))));
            }
        }
        
        loadAndRenderBookmarks();
    });
    </script>
</body>
</html>