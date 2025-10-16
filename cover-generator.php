<?php
// cover-generator.php (All-in-One File)
require_once 'admin/config.php';

// Get user data from the session to pre-fill some form fields
$userName = $_SESSION['user_name'];
$userYearInt = $_SESSION['academic_year'];
$userYearText = ($userYearInt == 2) ? '2nd' : '1st';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cover Page Generator - Saadat Notebook</title>
    
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
        .generator-layout {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        /* On larger screens, use a two-column layout */
        @media (min-width: 992px) {
            .generator-layout {
                flex-direction: row;
                align-items: flex-start;
            }
            .form-column {
                flex: 1;
                max-width: 450px;
                /* Make the form sticky so it stays visible while scrolling on long pages */
                position: sticky;
                top: 100px;
            }
            .preview-column {
                flex: 2;
                padding-left: 1rem;
            }
        }

        .form-card-generator {
            background-color: var(--card-background);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: var(--shadow-sm);
        }

        .form-card-generator h3 {
            margin-top: 0;
            margin-bottom: 1.5rem;
            font-size: 1.2rem;
        }

        .form-group-generator {
            margin-bottom: 1.25rem;
        }

        .form-group-generator label {
            display: block;
            font-weight: 500;
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .form-group-generator input[type="text"] {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background-color: var(--background-color);
            color: var(--text-primary);
            font-size: 1rem;
            font-family: 'Poppins', sans-serif;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-group-generator input[type="text"]:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(10, 10, 10, 0.1);
        }

        html.dark-mode .form-group-generator input[type="text"]:focus {
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
        }

        .download-actions {
            position: relative;
            margin-top: 2rem;
        }

        .submit-btn {
            width: 100%;
            padding: 14px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            background-color: var(--primary-color);
            color: #fff;
            border: none;
            transition: background-color 0.2s ease, opacity 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .submit-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        html.dark-mode .submit-btn { color: #0a0a0a; }

        /* ---- 2. Cover Page Preview Styling ---- */

        .cover-a4-ratio {
            /* For the image generator, a solid white background is essential */
            background: #fff; 
            color: #000;
            font-family: 'Times New Roman', Times, serif;
            box-shadow: var(--shadow-lg);
            border: 1px solid #ccc;
            display: flex;
            flex-direction: column;
            /* This maintains the A4 paper aspect ratio */
            aspect-ratio: 1 / 1.414;
            width: 100%;
            margin: 0 auto;
            /* The padding is set to match standard margin sizes */
            padding: 2cm 1.5cm; 
        }

        /* Header Section */
        .cover-header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 15px;
            margin-bottom: 1.5cm; 
        }

        .cover-logo {
            width: 70px; /* Adjust size as needed */
            height: auto;
            margin-bottom: 10px;
        }

        .cover-header h2 {
            font-size: 22pt;
            font-weight: bold;
            margin: 0;
            line-height: 1.2;
        }

        .cover-header h3 {
            font-size: 16pt;
            margin: 5px 0 0 0;
        }

        /* Body Section */
        .cover-body {
            text-align: center;
            flex-grow: 1; /* This is key to pushing the footer down */
            display: flex;
            flex-direction: column;
            justify-content: center; 
        }

        .subject-line {
            border: 2px solid #000;
            padding: 8px 15px;
            display: inline-block; /* Allows margin auto to work */
            margin: 0 auto 1cm;
            font-size: 18pt;
            font-weight: bold;
        }

        .title-line p {
            font-size: 14pt;
            margin: 0 0 10px 0;
        }
        .title-line h1 {
            font-size: 32pt;
            font-weight: bold;
            margin: 0;
            line-height: 1.3;
        }

        /* Footer Section */
        .cover-footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end; /* Align to the bottom of the page */
            font-size: 12pt;
            line-height: 1.5;
        }

        .submitted-by-section, .submitted-to-section {
            width: 48%; /* Use slightly less than 50% to prevent wrapping */
        }

        .footer-title {
            font-weight: bold;
            margin: 0 0 10px 0;
            text-decoration: underline;
        }

        .footer-value {
            font-weight: bold;
            margin: 0;
            min-height: 1.5em; /* Ensures layout doesn't jump when name is empty */
        }

        .footer-meta {
            margin: 2px 0 0 0;
            min-height: 1.5em; /* Ensures layout doesn't jump */
        }


        /* ---- 3. Loading Spinner for Download ---- */

        .loader-spinner {
            position: absolute;
            top: 50%;
            right: 20px;
            transform: translateY(-50%);
            width: 24px;
            height: 24px;
            border: 3px solid rgba(255,255,255,0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        html.dark-mode .loader-spinner {
            border-color: rgba(0,0,0,0.3);
            border-top-color: #0a0a0a;
        }

        @keyframes spin {
            to { transform: translateY(-50%) rotate(360deg); }
        }
    </style>
</head>
<body>
    <header class="header">
        <a href="index.php" class="logo-link"><div class="logo-container"><img src="images/college-logo.png" alt="Saadat Notebook Logo" class="site-logo"><div class="logo-text"><h1>Saadat Notebook</h1><p>Department of Mathematics</p></div></div></a>
        <div class="header-actions"><button class="menu-icon" aria-label="Open Menu"><i class="fas fa-bars"></i></button></div>
    </header>

    <div class="page-wrapper animate__animated animate__fadeIn">
        <main>
            <h1 class="page-title">Practical Notebook Cover Generator</h1>

            <div class="generator-layout">
                <!-- ============================ -->
                <!-- ===== COLUMN 1: THE FORM ===== -->
                <!-- ============================ -->
                <div class="form-column">
                    <div class="form-card-generator">
                        <h3>Enter Your Details</h3>
                        <div class="form-group-generator">
                            <label for="cover-subject">Subject</label>
                            <input type="text" id="cover-subject" value="Physics-IV (Practical)">
                        </div>
                        <div class="form-group-generator">
                            <label for="cover-title">Practical Title / Course Code</label>
                            <input type="text" id="cover-title" value="Course Code: 223708">
                        </div>
                         <div class="form-group-generator">
                            <label for="submitted-by">Submitted By (Your Name)</label>
                            <input type="text" id="submitted-by" value="<?php echo htmlspecialchars($userName); ?>">
                        </div>
                        <div class="form-group-generator">
                            <label for="student-year">Year</label>
                            <input type="text" id="student-year" value="Honours <?php echo $userYearText; ?> Year">
                        </div>
                        <div class="form-group-generator">
                            <label for="roll-number">Class Roll</label>
                            <input type="text" id="roll-number" placeholder="e.g., 201">
                        </div>
                        <div class="form-group-generator">
                            <label for="reg-number">Registration No.</label>
                            <input type="text" id="reg-number" placeholder="e.g., 123456789">
                        </div>
                        <div class="form-group-generator">
                            <label for="session">Session</label>
                            <input type="text" id="session" placeholder="e.g., 2022-2023">
                        </div>
                        <div class="form-group-generator">
                            <label for="submitted-to">Submitted To (Teacher's Name)</label>
                            <input type="text" id="submitted-to" placeholder="Enter the name of your teacher">
                        </div>
                         <div class="form-group-generator">
                            <label for="designation">Teacher's Designation</label>
                            <input type="text" id="designation" value="Lecturer, Dept. of Mathematics">
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
                        <div class="cover-header">
                            <img src="images/college-logo.png" alt="College Logo" class="cover-logo">
                            <h2>GOVT. SAADAT COLLEGE, KARATIA, TANGAIL</h2>
                            <h3>DEPARTMENT OF MATHEMATICS</h3>
                        </div>
                        <div class="cover-body">
                             <div class="subject-line">
                                <span id="preview-subject">Physics-IV (Practical)</span>
                            </div>
                            <div class="title-line">
                                 <p>A Practical Notebook On</p>
                                 <h1 id="preview-title">Course Code: 223708</h1>
                            </div>
                        </div>
                        <div class="cover-footer">
                             <div class="submitted-by-section">
                                <p class="footer-title">Submitted By:</p>
                                <p class="footer-value" id="preview-submitted-by"><?php echo htmlspecialchars($userName); ?></p>
                                <p class="footer-meta" id="preview-student-year">Honours <?php echo $userYearText; ?> Year</p>
                                <p class="footer-meta">Department of Mathematics</p>
                                <p class="footer-meta" id="preview-roll-number">Class Roll:</p>
                                <p class="footer-meta" id="preview-reg-number">Reg No.:</p>
                                <p class="footer-meta" id="preview-session">Session:</p>
                            </div>
                            <div class="submitted-to-section">
                                <p class="footer-title">Submitted To:</p>
                                <p class="footer-value" id="preview-submitted-to">[Teacher's Name]</p>
                                <p class="footer-meta" id="preview-designation">Lecturer, Dept. of Mathematics</p>
                                <p class="footer-meta">Govt. Saadat College</p>
                            </div>
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

            // Object to map form input IDs to the IDs of the preview elements they control.
            const mappings = {
                'cover-subject': { targetId: 'preview-subject' },
                'cover-title': { targetId: 'preview-title' },
                'submitted-by': { targetId: 'preview-submitted-by' },
                'student-year': { targetId: 'preview-student-year' },
                'submitted-to': { targetId: 'preview-submitted-to' },
                'designation': { targetId: 'preview-designation' },
                'session': { targetId: 'preview-session', prefix: 'Session: ' },
                'roll-number': { targetId: 'preview-roll-number', prefix: 'Class Roll: ' },
                'reg-number': { targetId: 'preview-reg-number', prefix: 'Reg No.: ' }
            };

            /**
             * Updates the text content of a preview element based on its corresponding input field.
             * @param {string} inputId - The ID of the form input element.
             * @param {string} previewId - The ID of the element in the cover preview.
             * @param {string} [prefix=''] - Optional text to add before the user's input.
             */
            function updatePreviewText(inputId, previewId, prefix = '') {
                const inputElement = document.getElementById(inputId);
                const previewElement = document.getElementById(previewId);

                if (inputElement && previewElement) {
                    const inputValue = inputElement.value.trim();
                    // Use the input's value. If it's empty, use the placeholder as a default.
                    const textToShow = inputValue || inputElement.placeholder;
                    
                    // For fields with prefixes, only show the prefix if there is content.
                    if (prefix && inputValue) {
                        previewElement.textContent = prefix + inputValue;
                    } else if (prefix && !inputValue) {
                        previewElement.textContent = prefix; // Show only prefix if field is empty
                    } else {
                        previewElement.textContent = textToShow;
                    }
                }
            }

            // Loop through all mappings to attach event listeners and perform the initial update.
            for (const inputId in mappings) {
                const config = mappings[inputId];
                const inputElement = document.getElementById(inputId);
                if (inputElement) {
                    const updateFn = () => updatePreviewText(inputId, config.targetId, config.prefix);
                    
                    inputElement.addEventListener('keyup', updateFn); // Update as user types
                    inputElement.addEventListener('change', updateFn); // Catches paste events
                    
                    updateFn(); // Initial call to populate preview on page load
                }
            }

            // --- Part 2: Image Download Functionality ---

            const downloadBtn = document.getElementById('download-btn');
            const spinner = document.getElementById('loader-spinner');
            const previewPane = document.getElementById('cover-preview');

            if (downloadBtn && previewPane && spinner) {
                downloadBtn.addEventListener('click', () => {
                    // 1. Set the UI to a loading state
                    spinner.style.display = 'block';
                    downloadBtn.disabled = true;
                    const originalButtonHTML = downloadBtn.innerHTML;
                    downloadBtn.innerHTML = '<i class="fas fa-cog"></i> Generating...';

                    // 2. Use html2canvas library to capture the preview div
                    html2canvas(previewPane, {
                        scale: 3,           // Renders at 3x the resolution for high quality
                        useCORS: true,      // Needed if your logo is on another server
                        logging: false,     // Keeps the console clean
                        backgroundColor: '#ffffff' // Explicitly set background
                    }).then(canvas => {
                        // 3. Create a temporary link element to trigger the download
                        const link = document.createElement('a');
                        
                        // Get subject name to use in the filename
                        const subjectName = document.getElementById('cover-subject').value.trim().replace(/[^a-z0-9]/gi, '_');
                        link.download = `Cover_Page_${subjectName || 'Practical'}.jpg`;
                        
                        // Convert the canvas to a high-quality JPG image
                        link.href = canvas.toDataURL('image/jpeg', 0.95);
                        
                        // 4. Click the link to start the download, then remove it
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);

                    }).catch(error => {
                        // Handle any errors during image generation
                        console.error('Error generating cover page image:', error);
                        alert('Sorry, an error occurred while generating the image. Please try again.');

                    }).finally(() => {
                        // 5. ALWAYS reset the button back to its normal state
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