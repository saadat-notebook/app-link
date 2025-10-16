<?php
// cover-generator.php (All-in-One File - New Assignment Style)
require_once 'admin/config.php';

// Get user data from the session to pre-fill some form fields
$userName = $_SESSION['user_name'];
$current_year = date("Y");
$next_year = date("y") + 1;
$academicSession = $current_year . "-" . $next_year;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment Cover Generator - Saadat Notebook</title>
    
    <script>
        (function () { try { const theme = localStorage.getItem('saadatNotesTheme'); const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches; if (theme === 'dark' || (!theme && systemPrefersDark)) { document.documentElement.classList.add('dark-mode'); } const ua = navigator.userAgent || ""; if (ua.includes("wv") || ua.includes("WrapperApp") || ua.includes("saadatnotebook")) { document.documentElement.classList.add("wrapper-app"); } } catch (e) { } })();
    </script>
    
    <!-- External libraries -->
    <link rel="stylesheet" href="css/style.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- ======================================================= -->
    <!-- ===== EMBEDDED CSS FOR THE COVER GENERATOR PAGE ===== -->
    <!-- ======================================================= -->
    <style>
        /* --- Stylesheet for the Cover Page Generator --- */
        /* ---- 1. Page Layout & Form Styling ---- */
        .generator-layout { display: flex; flex-direction: column; gap: 2rem; }
        @media (min-width: 992px) {
            .generator-layout { flex-direction: row; align-items: flex-start; }
            .form-column { flex: 1; max-width: 450px; position: sticky; top: 100px; }
            .preview-column { flex: 2; padding-left: 1rem; }
        }
        .form-card-generator { background-color: var(--card-background); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-sm); }
        .form-card-generator h3 { margin-top: 0; margin-bottom: 1.5rem; font-size: 1.2rem; }
        .form-group-generator { margin-bottom: 1.25rem; }
        .form-group-generator label { display: block; font-weight: 500; color: var(--text-secondary); margin-bottom: 0.5rem; font-size: 0.9rem; }
        .form-group-generator input[type="text"], .form-group-generator input[type="date"] { width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 8px; background-color: var(--background-color); color: var(--text-primary); font-size: 1rem; font-family: 'Poppins', sans-serif; transition: border-color 0.2s, box-shadow 0.2s; }
        .form-group-generator input:focus { outline: none; border-color: var(--primary-color); box-shadow: 0 0 0 3px rgba(10, 10, 10, 0.1); }
        html.dark-mode .form-group-generator input:focus { box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1); }
        .download-actions { position: relative; margin-top: 2rem; }
        .submit-btn { width: 100%; padding: 14px; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer; background-color: var(--primary-color); color: #fff; border: none; transition: background-color 0.2s ease, opacity 0.2s ease; display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; }
        .submit-btn:disabled { opacity: 0.7; cursor: not-allowed; }
        html.dark-mode .submit-btn { color: #0a0a0a; }

        /* ---- 2. NEW Cover Page Preview Styling ---- */
        .cover-a4-ratio { background: #fff; color: #000; font-family: 'Times New Roman', Times, serif; box-shadow: var(--shadow-lg); border: 1px solid #ccc; display: flex; flex-direction: column; aspect-ratio: 1 / 1.414; width: 100%; margin: 0 auto; padding: 2cm 1.5cm; }
        
        /* Header */
        .cover-header { text-align: center; line-height: 1.5; margin-bottom: 2cm; }
        .college-group-name { font-size: 16pt; font-weight: bold; }
        .college-full-name { font-size: 18pt; font-weight: bold; }
        .cover-logo { width: 70px; height: auto; margin: 12px 0; }
        .cover-meta-info { font-size: 14pt; font-weight: bold; }

        /* Assignment Title */
        .assignment-title-section { text-align: center; margin-bottom: 2cm; font-size: 14pt; }
        #preview-assignment-name span { font-weight: bold; }

        /* Student Details */
        .student-details-section { display: flex; justify-content: space-between; margin-bottom: auto; /* Pushes signature section down */ font-size: 14pt; line-height: 2.2; }
        .details-left, .details-right { width: 50%; }
        .detail-item span { font-weight: bold; }

        /* Signature Section */
        .signature-section { display: flex; justify-content: space-between; align-items: flex-end; padding-top: 1cm; font-size: 14pt; font-weight: bold; }
        .signature-line { border-top: 1px solid #000; padding-top: 5px; width: 180px; text-align: center; }

        /* ---- 3. Loading Spinner ---- */
        .loader-spinner { position: absolute; top: 50%; right: 20px; transform: translateY(-50%); width: 24px; height: 24px; border: 3px solid rgba(255,255,255,0.3); border-top-color: #fff; border-radius: 50%; animation: spin 1s linear infinite; }
        html.dark-mode .loader-spinner { border-color: rgba(0,0,0,0.3); border-top-color: #0a0a0a; }
        @keyframes spin { to { transform: translateY(-50%) rotate(360deg); } }
    </style>
</head>
<body>
    <header class="header">
        <a href="index.php" class="logo-link"><div class="logo-container"><img src="images/logo.png" alt="Saadat Notebook Logo" class="site-logo"><div class="logo-text"><h1>Saadat Notebook</h1><p>Department of Mathematics</p></div></div></a>
        <div class="header-actions"><button class="menu-icon" aria-label="Open Menu"><i class="fas fa-bars"></i></button></div>
    </header>

    <div class="page-wrapper animate__animated animate__fadeIn">
        <main>
            <h1 class="page-title">Assignment Cover Generator</h1>

            <div class="generator-layout">
                <!-- ============================ -->
                <!-- ===== COLUMN 1: THE FORM ===== -->
                <!-- ============================ -->
                <div class="form-column">
                    <div class="form-card-generator">
                        <h3>Enter Assignment Details</h3>
                        <div class="form-group-generator">
                            <label for="academic-year">Academic Year</label>
                            <input type="text" id="academic-year" value="<?php echo $academicSession; ?>">
                        </div>
                         <div class="form-group-generator">
                            <label for="department">Department</label>
                            <input type="text" id="department" value="Mathematics">
                        </div>
                        <div class="form-group-generator">
                            <label for="assignment-name">Name of Assignment</label>
                            <input type="text" id="assignment-name" placeholder="e.g., New Product Development">
                        </div>
                        <div class="form-group-generator">
                            <label for="full-name">Full Name</label>
                            <input type="text" id="full-name" value="<?php echo htmlspecialchars($userName); ?>">
                        </div>
                        <div class="form-group-generator">
                            <label for="roll-no">Roll No.</label>
                            <input type="text" id="roll-no" placeholder="Enter class roll">
                        </div>
                         <div class="form-group-generator">
                            <label for="subject">Subject</label>
                            <input type="text" id="subject" placeholder="e.g., Basic of Marketing">
                        </div>
                        <div class="form-group-generator">
                            <label for="section">Section</label>
                            <input type="text" id="section" placeholder="e.g., A">
                        </div>
                         <div class="form-group-generator">
                            <label for="submission-date">Date of Submission</label>
                            <input type="date" id="submission-date" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        
                        <div class="download-actions">
                             <button id="download-btn" class="submit-btn"><i class="fas fa-download"></i> Download as JPG</button>
                             <div class="loader-spinner" id="loader-spinner" style="display: none;"></div>
                        </div>
                    </div>
                </div>

                <!-- =================================== -->
                <!-- ===== COLUMN 2: THE PREVIEW PANE ===== -->
                <!-- =================================== -->
                <div class="preview-column">
                    <!-- This div is what will be captured as an image -->
                    <div id="cover-preview" class="cover-a4-ratio">
                        <!-- Header Section -->
                        <div class="cover-header">
                            <div class="college-group-name">Saadat Notebook Presents</div>
                            <div class="college-full-name">Govt. Saadat College, Karatia, Tangail.</div>
                            <img src="images/college-logo.png" alt="College Logo" class="cover-logo">
                            <div id="preview-academic-year" class="cover-meta-info">Academic Year <?php echo $academicSession; ?></div>
                            <div id="preview-department" class="cover-meta-info">Department: Mathematics</div>
                        </div>

                        <!-- Title Section -->
                        <div class="assignment-title-section">
                            <p id="preview-assignment-name">Name of Assignment: <span>New Product Development</span></p>
                        </div>

                        <!-- Student Details Section -->
                        <div class="student-details-section">
                            <div class="details-left">
                                <div id="preview-full-name" class="detail-item">Full Name: <span><?php echo htmlspecialchars($userName); ?></span></div>
                                <div id="preview-roll-no" class="detail-item">Roll No.: <span></span></div>
                                <div id="preview-subject" class="detail-item">Subject: <span></span></div>
                            </div>
                            <div class="details-right">
                                <div id="preview-section" class="detail-item">Section: <span></span></div>
                                <div id="preview-submission-date" class="detail-item">Date of Submission: <span><?php echo date('d/m/Y'); ?></span></div>
                            </div>
                        </div>

                        <!-- Signature Section -->
                        <div class="signature-section">
                            <div class="signature-line">Student Sign</div>
                            <div class="signature-line">Professor Sign</div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- This external library is required for the image generation to work -->
    <script src="js/config.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <!-- Your existing global script -->
    <script src="js/global.js?v=<?= filemtime('js/global.js'); ?>"></script>
    
    <!-- ======================================================== -->
    <!-- ===== EMBEDDED JAVASCRIPT FOR PAGE INTERACTIVITY ===== -->
    <!-- ======================================================== -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // --- Part 1: Live Preview Updates ---
            const mappings = {
                'academic-year': { targetId: 'preview-academic-year', prefix: 'Academic Year ' },
                'department': { targetId: 'preview-department', prefix: 'Department: ' },
                'assignment-name': { targetId: 'preview-assignment-name', prefix: 'Name of Assignment: ' },
                'full-name': { targetId: 'preview-full-name', prefix: 'Full Name: ' },
                'roll-no': { targetId: 'preview-roll-no', prefix: 'Roll No.: ' },
                'subject': { targetId: 'preview-subject', prefix: 'Subject: ' },
                'section': { targetId: 'preview-section', prefix: 'Section: ' },
                'submission-date': { targetId: 'preview-submission-date', prefix: 'Date of Submission: ' },
            };

            function updatePreviewText(inputId, previewId, prefix = '') {
                const inputElement = document.getElementById(inputId);
                const previewElement = document.getElementById(previewId);

                if (!inputElement || !previewElement) return;

                let value = inputElement.value.trim();
                const placeholder = inputElement.placeholder;

                // Handle date formatting separately
                if (inputElement.type === 'date' && value) {
                    const date = new Date(value);
                    value = date.toLocaleDateString('en-GB'); // Format as DD/MM/YYYY
                }
                
                const content = value || placeholder;
                const boldPart = `<span>${content || ''}</span>`;

                if (prefix) {
                    previewElement.innerHTML = `${prefix}${boldPart}`;
                } else {
                    previewElement.innerHTML = boldPart;
                }
            }

            // Loop through all mappings to attach event listeners and perform initial updates.
            for (const inputId in mappings) {
                const config = mappings[inputId];
                const inputElement = document.getElementById(inputId);
                if (inputElement) {
                    const updateFn = () => updatePreviewText(inputId, config.targetId, config.prefix);
                    inputElement.addEventListener('keyup', updateFn);
                    inputElement.addEventListener('change', updateFn);
                    updateFn(); // Initial call
                }
            }

            // --- Part 2: Image Download Functionality ---
            const downloadBtn = document.getElementById('download-btn');
            const spinner = document.getElementById('loader-spinner');
            const previewPane = document.getElementById('cover-preview');

            if (downloadBtn && previewPane && spinner) {
                downloadBtn.addEventListener('click', () => {
                    spinner.style.display = 'block';
                    downloadBtn.disabled = true;
                    const originalButtonHTML = downloadBtn.innerHTML;
                    downloadBtn.innerHTML = '<i class="fas fa-cog"></i> Generating...';

                    html2canvas(previewPane, {
                        scale: 3, useCORS: true, logging: false, backgroundColor: '#ffffff'
                    }).then(canvas => {
                        const link = document.createElement('a');
                        const subjectName = document.getElementById('subject').value.trim().replace(/[^a-z0-9]/gi, '_');
                        link.download = `Cover_Page_${subjectName || 'Assignment'}.jpg`;
                        link.href = canvas.toDataURL('image/jpeg', 0.95);
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    }).catch(error => {
                        console.error('Error generating cover image:', error);
                        alert('An error occurred. Please try again.');
                    }).finally(() => {
                        spinner.style.display = 'none';
                        downloadBtn.disabled = false;
                        downloadBtn.innerHTML = originalButtonHTML;
                    });
                });
            }
        });
    </script>

    <?php include 'footer.php'; ?>
</body>
</html>