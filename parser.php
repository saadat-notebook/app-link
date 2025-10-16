<?php
// drive-parser.php
require_once 'admin/config.php';

// Security Check: Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drive Link Converter - Saadat Notebook</title>

    <script> (function () { try { const theme = localStorage.getItem('saadatNotesTheme'); const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches; if (theme === 'dark' || (!theme && systemPrefersDark)) { document.documentElement.classList.add('dark-mode'); } const ua = navigator.userAgent || ""; if (ua.includes("wv") || ua.includes("WrapperApp") || ua.includes("saadatnotebook")) { document.documentElement.classList.add("wrapper-app"); } } catch (e) { } })(); </script>

    <!-- Standard Website Styles -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <!-- Parser-Specific Styles (Adapted for main site theme) -->
    <style>
        .parser-card {
            background-color: var(--card-background);
            border-radius: 12px;
            padding: 24px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
        }

        .parser-card h2 {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-top: 0;
            margin-bottom: 20px;
        }

        .parser-card textarea,
        .parser-card button {
            padding: 12px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            outline: none;
            margin: 5px 0;
            font-size: 1rem;
            font-family: inherit;
        }

        .parser-card textarea {
            width: 100%;
            height: 120px;
            resize: vertical;
            background-color: var(--primary-light);
            box-sizing: border-box;
            color: var(--text-primary);
        }

        .parser-card button {
            background-color: var(--primary-color);
            color: #fff;
            cursor: pointer;
            border: none;
            transition: background-color 0.2s ease;
        }
        html.dark-mode .parser-card button { color: #0a0a0a; }

        .parser-card button:disabled {
            background: #9ca3af;
            cursor: not-allowed;
            color: white;
        }

        .parser-card button:hover:not(:disabled) { background: #333; }
        html.dark-mode .parser-card button:hover:not(:disabled) { background: #e0e0e0; }
        
        .output-container { margin-top: 20px; }
        .result-block { margin-bottom: 15px; padding: 15px; background: var(--primary-light); border-radius: 12px; }
        .result-line { margin: 10px 0; display: flex; justify-content: space-between; align-items: center; gap: 10px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px; }
        .result-line:last-child { border-bottom: none; padding-bottom: 0; }
        .result-line span { word-break: break-all; flex: 1; font-size: 0.9rem; }

        .copy-btn {
            background: #22c55e; padding: 8px 14px; border-radius: 8px; color: white;
            font-size: 14px; cursor: pointer; border: none; position: relative;
            transition: background-color 0.2s;
        }
        .copy-btn:hover { background: #16a34a; }
        
        .tooltip {
            position: absolute; bottom: 125%; left: 50%; transform: translateX(-50%);
            background: #111827; color: white; padding: 4px 8px; border-radius: 6px;
            font-size: 12px; opacity: 0; pointer-events: none; transition: opacity 0.3s ease; white-space: nowrap;
        }
        .tooltip.show { opacity: 1; }
    </style>
</head>

<body>
    <!-- Standard Website Navigation (Bottom Bar, Header, Side Menu) -->
    <script> (function() { if(document.cookie.includes("PHPSESSID")){ const bottomNavHTML = `<nav class="bottom-nav"><a href="index.php" class="nav-link nav-home-link"><i class="fas fa-home"></i><span>Dashboard</span></a><a href="profile.php" class="nav-link"><i class="fas fa-user-circle"></i><span>Profile</span></a><a href="bcs-math.html" class="nav-link"><i class="fas fa-square-root-alt"></i><span>BCS Maths</span></a></nav>`; document.write(bottomNavHTML); } })(); </script>
    <header class="header"> <a href="index.php" class="logo-link"><div class="logo-container"><img src="images/logo.png" alt="Saadat Notebook Logo" class="site-logo"><div class="logo-text"><h1>Saadat Notebook</h1><p>Department of Mathematics</p></div></div></a><div class="header-actions"><button class="menu-icon" aria-label="Open Menu"><i class="fas fa-bars"></i></button></div> </header>

    <div class="page-wrapper animate__animated animate__fadeIn">
        <main>
            <!-- Your Parser Tool, using the standard page title class -->
            <h1 class="page-title">Drive Link Converter</h1>
            <div class="parser-card">
                <textarea id="driveLinks" placeholder="Paste one or more Google Drive links (each on a new line)"></textarea>
                <br>
                <button onclick="convertLinks()" id="convertBtn">Convert Links</button>
                <div id="results" class="output-container"></div>
            </div>
        </main>
    </div>
    
    <!-- Scripts -->
    <script src="js/config.js"></script>
    <script src="js/user-session.js"></script>
    <script src="js/global.js"></script>

    <script>
        // THIS IS THE FULL, UNCHANGED PARSER SCRIPT
        
        async function convertLinks() {
            const driveLinksInput = document.getElementById("driveLinks").value.trim();
            const resultsDiv = document.getElementById("results");
            const convertBtn = document.getElementById("convertBtn");

            const links = driveLinksInput.split("\n").map(l => l.trim()).filter(Boolean);
            if (links.length === 0) {
                resultsDiv.innerHTML = "";
                return;
            }

            convertBtn.disabled = true;
            convertBtn.textContent = "Converting...";
            resultsDiv.innerHTML = "";

            const promises = links.map(url => processUrl(url));
            const results = await Promise.allSettled(promises);

            convertBtn.disabled = false;
            convertBtn.textContent = "Convert Links";

            results.forEach(result => {
                if (result.status === "fulfilled" && result.value) {
                    result.value.forEach(block => resultsDiv.appendChild(block));
                } else if (result.status === "rejected") {
                    resultsDiv.appendChild(createErrorBlock(result.reason));
                }
            });
        }

        async function processUrl(url) {
            const idMatch = url.match(/(?:\/folders\/|\/file\/d\/)([-\w]{25,})/);
            if (!idMatch) {
                throw new Error(`Invalid Google Drive link format: ${url}`);
            }
            const id = idMatch[1];
            const isFolder = url.includes("/folders/");

            try {
                const proxyUrl = isFolder ?
                    `api_proxy.php?type=folder&id=${id}` :
                    `api_proxy.php?type=file&id=${id}`;

                const response = await fetch(proxyUrl);
                const data = await handleApiResponse(response, url);

                if (isFolder) {
                    if (!data.files || data.files.length === 0) {
                        return [createErrorBlock(`Folder is empty or inaccessible: ${url}`)];
                    }
                    let folderBlocks = [];
                    let folderHeader = document.createElement("div");
                    folderHeader.className = "result-block";
                    folderHeader.innerHTML = `<b>Folder Contents (${data.files.length} items):</b>`;
                    folderBlocks.push(folderHeader);

                    data.files.forEach(file => {
                        if (file.mimeType !== 'application/vnd.google-apps.folder') {
                            folderBlocks.push(generateFileBlock(file.name, file.id));
                        }
                    });
                    return folderBlocks;
                } else {
                    return [generateFileBlock(data.name || `File with ID ${id}`, id)];
                }
            } catch (e) {
                throw e;
            }
        }

        async function handleApiResponse(response, url) {
            if (!response.ok) {
                try {
                     const errorData = await response.json();
                     throw new Error(`API Error for ${url}: ${errorData.error || response.statusText}`);
                } catch(e) {
                     throw new Error(`Request Error for ${url}: ${response.statusText}`);
                }
            }
            return response.json();
        }

        function generateFileBlock(name, id) {
            let block = document.createElement("div");
            block.className = "result-block";
            const cleanName = name.replace(/\.[^/.]+$/, "");
            const previewUrl = `https://drive.google.com/file/d/${id}/preview`;
            const downloadUrl = `https://drive.google.com/uc?export=download&id=${id}`;

            block.appendChild(createLine(cleanName, "Copy Name"));
            block.appendChild(createLine(previewUrl, "Copy Preview"));
            block.appendChild(createLine(downloadUrl, "Copy Download"));

            return block;
        }

        function createErrorBlock(message) {
            const block = document.createElement('div');
            block.className = 'result-block';
            block.style.backgroundColor = '#fee2e2';
            block.style.color = '#991b1b';
            block.style.border = '1px solid #fecaca';
            block.textContent = String(message).replace("Error: ", "");
            return block;
        }

        function createLine(value, label) {
            let line = document.createElement("div");
            line.className = "result-line";
            let span = document.createElement("span");
            span.textContent = value;
            line.appendChild(span);

            let btn = document.createElement("button");
            btn.className = "copy-btn";
            btn.textContent = label;

            let tooltip = document.createElement("span");
            tooltip.className = "tooltip";
            tooltip.textContent = "Copied ✓";
            btn.appendChild(tooltip);

            btn.addEventListener("click", () => {
                copyTextToClipboard(value, btn);
            });

            line.appendChild(btn);
            return line;
        }

        function copyTextToClipboard(text, btn) {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(() => { showTooltip(btn); });
            } else {
                fallbackCopyText(text, btn);
            }
        }

        function fallbackCopyText(text, btn) {
            const textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.position = "fixed"; textArea.style.opacity = "0";
            document.body.appendChild(textArea);
            textArea.focus(); textArea.select();
            try { document.execCommand("copy"); showTooltip(btn); } catch (err) {}
            document.body.removeChild(textArea);
        }

        function showTooltip(btn) {
            let tooltip = btn.querySelector(".tooltip");
            tooltip.classList.add("show");
            setTimeout(() => tooltip.classList.remove("show"), 1500);
        }
    </script>
</body>
</html>