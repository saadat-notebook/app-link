<?php
// department.php (CORRECTED VERSION)
require_once 'admin/config.php';

// Security Check: If the user is not logged in, redirect them.
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// --- Fetch BOTH sets of images ---
$campus_images = [];
$department_images = [];

$campus_sql = "SELECT image_url, description FROM gallery_images WHERE gallery_type = 'campus' ORDER BY uploaded_at DESC LIMIT 7";
if ($campus_result = $conn->query($campus_sql)) { while ($row = $campus_result->fetch_assoc()) { $campus_images[] = $row; } }

$dept_sql = "SELECT image_url, description FROM gallery_images WHERE gallery_type = 'department' ORDER BY uploaded_at DESC LIMIT 10";
if ($dept_result = $conn->query($dept_sql)) { while ($row = $dept_result->fetch_assoc()) { $department_images[] = $row; } }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Department - Saadat Notebook</title>

    <script>
        (function () { try { const theme = localStorage.getItem('saadatNotesTheme'); const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches; if (theme === 'dark' || (!theme && systemPrefersDark)) { document.documentElement.classList.add('dark-mode'); } const ua = navigator.userAgent || ""; if (ua.includes("wv") || ua.includes("WrapperApp") || ua.includes("saadatnotebook")) { document.documentElement.classList.add("wrapper-app"); } } catch (e) { } })();
    </script>
    
    <!-- The rest of your head section is unchanged -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/department.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
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
            <h1 class="page-title">Our Campus</h1>
            <p class="page-subtitle">Welcome to our department at Saadat College. Here's a glimpse of our beautiful campus.</p>

            <?php if (!empty($campus_images)): ?>
            <div class="photo-gallery-container">
                <div class="swiper gallery-swiper">
                    <div class="swiper-wrapper">
                    <?php foreach ($campus_images as $image): ?>
                    <div class="swiper-slide">
                        <img src="<?php echo htmlspecialchars($image['image_url']); ?>" alt="<?php echo htmlspecialchars($image['description']); ?>" data-description="<?php echo htmlspecialchars($image['description']); ?>" loading="lazy">
                    </div>
                    <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <h3 class="section-title-department">Honourable Principal</h3>
            <div class="principal-card">
                <img src="images/principal.jpg" alt="College Principal">
                <div class="principal-details">
                    <h4>Prof. Mohd. Moniruzzaman Miah</h4>
                    <h5>Principal, Saadat College</h5>
                </div>
            </div>

                        <?php if(!empty($department_images)): ?>
            <h2 class="section-title-department">Department of Mathematics</h2>
            <p class="page-subtitle">Gallery</p>
            <div class="photo-gallery-container">
                <div class="swiper gallery-swiper">
                    <div class="swiper-wrapper">
                        <?php foreach($department_images as $image): ?>
                        <div class="swiper-slide">
                            <img src="<?php echo htmlspecialchars($image['image_url']); ?>" alt="<?php echo htmlspecialchars($image['description']); ?>" data-description="<?php echo htmlspecialchars($image['description']); ?>" loading="lazy">
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <h2 class="section-title-department">Faculty Members</h2>
            <!-- ===== NEW: FACULTY LIST WITH FULL DATA ATTRIBUTES ===== -->
<div class="faculty-list-v2">

    <!-- Card 1: Anjurani Debnath (Reordered to be Head of Dept) -->
    <div class="faculty-card-v3" 
         data-img="images/avatar.jpg" 
         data-name="Anjurani Debnath" 
         data-designation="Associate Professor & Head of Department"
         data-bcs="14th BCS Cadre"
         data-email="anjurani1968@gmail.com"
         data-mobile="01789-615317">
        <img src="images/avatar.jpg" alt="Anjurani Debnath">
        <div class="faculty-info-v3">
            <h3>Anjurani Debnath</h3>
            <p>Associate Professor & Head of Department</p>
        </div>
    </div>

    <!-- Card 2: Samar Chandra Saha -->
    <div class="faculty-card-v3"
         data-img="images/avatar.jpg"
         data-name="Samar Chandra Saha"
         data-designation="Associate Professor"
         data-bcs="14th BCS Cadre"
         data-email="samar.csaha@gmail.com"
         data-mobile="01715-088413">
        <img src="images/avatar.jpg" alt="Samar Chandra Saha">
        <div class="faculty-info-v3">
            <h3>Samar Chandra Saha</h3>
            <p>Associate Professor</p>
        </div>
    </div>

    <!-- Card 3: Atiqur Rahman -->
    <div class="faculty-card-v3"
         data-img="images/atiqsir.jpg"
         data-name="Atiqur Rahman"
         data-designation="Assistant Professor"
         data-bcs="29th BCS Cadre"
         data-email="atiq315166@gmail.com"
         data-mobile="01724-315166"
         data-social="https://www.facebook.com/profile.php?id=100011599728176">
        <img src="images/atiqsir.jpg" alt="Atiqur Rahman">
        <div class="faculty-info-v3">
            <h3>Atiqur Rahman</h3>
            <p>Assistant Professor</p>
        </div>
    </div>

    <!-- Card 4: Md. Muktar Hossain -->
    <div class="faculty-card-v3"
         data-img="images/muktarsir.jpg"
         data-name="Md. Muktar Hossain"
         data-designation="Assistant Professor"
         data-bcs="29th BCS Cadre"
         data-email="muktarhossain29math@gmail.com"
         data-mobile="01914-272900"
         data-social="https://www.facebook.com/share/16Gqz5XXQm/">
        <img src="images/muktarsir.jpg" alt="Md. Muktar Hossain">
        <div class="faculty-info-v3">
            <h3>Md. Muktar Hossain</h3>
            <p>Assistant Professor</p>
        </div>
    </div>
    
    <!-- Card 5: Samsunnahar Rubina -->
    <div class="faculty-card-v3"
         data-img="images/avatar.jpg"
         data-name="Samsunnahar Rubina"
         data-designation="Lecturer"
         data-bcs="34th BCS Cadre"
         data-email="samsunnaharrubina87@gmail.com"
         data-mobile="01752-729523">
        <img src="images/avatar.jpg" alt="Samsunnahar Rubina">
        <div class="faculty-info-v3">
            <h3>Samsunnahar Rubina</h3>
            <p>Lecturer</p>
        </div>
    </div>
</div>               
            <!-- Section 7: Department Contacts (FINAL) -->
<h2 class="section-title-department">Department Contacts</h2>
<div class="contact-grid">
    <div class="contact-card">
        <i class="fas fa-desktop"></i>
        <h3>MD. Sabuj Mia</h3>
        <p>Computer Operator</p>
        <a href="tel:+8801318439899" class="contact-link">01739-409849</a>
    </div>
    <div class="contact-card">
        <i class="fas fa-user-tie"></i>
        <h3>MD. Mofazzal Hossain</h3>
        <p>Office Assistant</p>
        <a href="tel:+8801726619940" class="contact-link">01776-629940</a>
    </div>
    <div class="contact-card">
        <i class="fas fa-user-tie"></i>
        <h3>MD. Abdul Hai</h3>
        <p>Office Assistant</p>
        <a href="tel:+8801301890141" class="contact-link">01731-810241</a>
    </div>
</div>
            <h3 class="section-title-department">Academic Calendar (2025)</h3>
            <div class="academic-calendar-card">
                <img src="https://i.postimg.cc/ryYbyPvv/yearc-conv-1.png" alt="Academic Calendar Preview">
            </div>

            <h2 class="section-title-department">About This Project</h2>
            <div class="creator-note">
                <div class="welcome-text"><p>Welcome! As students of the Mathematics Department, we know it can be challenging to keep all our academic materials organized. This website was created by a fellow student from the 2023-24 session with a simple mission: to build a central, easy-to-use hub for all our resources—from handwritten notes to past questions. The goal is to help us spend less time searching and more time learning.</p></div>
            </div>
        </main>
    </div>
    
    <div class="faculty-modal-backdrop" id="faculty-modal-backdrop"><div class="faculty-modal-content"><div class="modal-header"><button class="modal-action-btn close-modal-btn" aria-label="Close Profile"><i class="fas fa-times"></i></button></div><div class="modal-body"><img src="" alt="Faculty Profile Picture" id="modal-faculty-img"><h2 id="modal-faculty-name"></h2><p id="modal-faculty-designation"></p><div class="modal-contact-info"></div></div></div></div>
    <div class="lightbox-backdrop" id="lightbox-backdrop"><button class="close-lightbox-btn" aria-label="Close image"><i class="fas fa-times"></i></button><img src="" alt="Full size gallery image" class="lightbox-image"><p class="lightbox-description"></p></div>
    
    <script src="js/config.js"></script>
    <script src="js/global.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="js/department.js"></script>
</body>
</html>