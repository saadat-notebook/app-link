<?php
// question-of-the-day.php (FINAL, COMPLETE, AND CORRECTED VERSION)
require_once 'admin/config.php';

// Security Check: If the user is not logged in, redirect them.
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Pass the username to JavaScript safely.
$userNameForJS = $_SESSION['user_name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Question of the Day - Saadat Notebook</title>

    <script> (function () { try { const theme = localStorage.getItem('saadatNotesTheme'); const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches; if (theme === 'dark' || (!theme && systemPrefersDark)) { document.documentElement.classList.add('dark-mode'); } const ua = navigator.userAgent || ""; if (ua.includes("wv") || ua.includes("WrapperApp") || ua.includes("saadatnotebook")) { document.documentElement.classList.add("wrapper-app"); } } catch (e) { } })(); </script>
    
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.css">
    
    <style>
        .qotd-card { background-color: var(--card-background); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.5rem; margin-bottom: 2rem; box-shadow: var(--shadow-sm); }
        .qotd-card h2 { font-size: 1.3rem; margin-top: 0; margin-bottom: 1.5rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border-color); }
        .qotd-image { max-width: 100%; border-radius: 8px; margin-bottom: 1.5rem; }
        .qotd-text { font-size: 1.1rem; line-height: 1.8; }
        .qotd-answer-form input { width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 1rem; margin-top: 1rem; }
        .qotd-answer-form .btn-primary { width: 100%; padding: 12px; margin-top: 1rem; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; background-color: var(--primary-color); color: #fff; }
        html.dark-mode .qotd-answer-form .btn-primary { color: #0a0a0a; }
        .answer-hint { font-size: 0.9rem; color: var(--text-secondary); margin-top: 0.5rem; }
        .submitted-answer-box { background-color: var(--primary-light); padding: 1rem; border-radius: 8px; margin-top: 1rem; }
        .solution-box { border-top: 1px solid var(--border-color); margin-top: 1.5rem; padding-top: 1.5rem; }
        .solution-box h3 { margin-top: 0; }
        .result-badge { display: inline-block; padding: 4px 10px; border-radius: 50px; font-weight: 600; color: #fff; }
        .result-badge.correct { background-color: var(--success-color); }
        .result-badge.incorrect { background-color: #dc3545; }
        #loader { text-align: center; padding: 2rem; }
        .archive-section { margin-top: 2.5rem; }
        .archive-accordion .accordion-item { margin-bottom: 0.5rem; border-radius: 8px; border: 1px solid var(--border-color); overflow: hidden; }
        .archive-accordion .accordion-header { width: 100%; background-color: var(--card-background); padding: 1rem 1.25rem; display: flex; justify-content: space-between; align-items: center; border: none; cursor: pointer; font-family: 'Poppins', sans-serif; }
        .archive-accordion .accordion-header-title { flex-grow: 1; text-align: left; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; padding-right: 1rem; }
        .archive-accordion .accordion-header-date { font-size: 0.85em; font-weight: 400; color: var(--text-secondary); white-space: nowrap; }
        .archive-accordion .toggle-icon { transition: transform 0.3s ease; }
        .archive-accordion .accordion-item.open .toggle-icon { transform: rotate(180deg); }
        .archive-accordion .accordion-content { max-height: 0; overflow: hidden; transition: max-height 0.4s ease-out, padding 0.4s ease-out; background-color: var(--primary-light); padding: 0 1.5rem; }
    </style>
</head>
<body>
    <script> (function() { try { if (document.cookie.includes("PHPSESSID")) { const bottomNavHTML = `<nav class="bottom-nav"><a href="index.php" class="nav-link nav-home-link"><i class="fas fa-home"></i><span>Dashboard</span></a><a href="profile.php" class="nav-link"><i class="fas fa-user-circle"></i><span>Profile</span></a><a href="bcs-math.html" class="nav-link"><i class="fas fa-square-root-alt"></i><span>BCS Maths</span></a></nav>`; document.write(bottomNavHTML); } } catch(e) {} })(); </script>

    <header class="header"> <a href="index.php" class="logo-link"><div class="logo-container"><img src="images/logo.png" alt="Saadat Notebook Logo" class="site-logo"><div class="logo-text"><h1>Saadat Notebook</h1><p>Department of Mathematics</p></div></div></a><div class="header-actions"><button class="menu-icon" aria-label="Open Menu"><i class="fas fa-bars"></i></button></div> </header>
    
    <div class="page-wrapper animate__animated animate__fadeIn">
        <main id="qotd-main-content">
            <h1 class="page-title">Question of the Day</h1>
            <div id="loader"><p>Loading questions...</p></div>
            <div id="today-question-container"></div>
            <div id="yesterday-review-container"></div>
            <div id="archive-container"></div>
        </main>
    </div>
    <script src="js/config.js"></script>
    <script src="js/user-session.js"></script>
    <script src="js/global.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/contrib/auto-render.min.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const userName = <?php echo json_encode($userNameForJS); ?>;

        if (!userName) {
            document.getElementById('qotd-main-content').innerHTML = '<div class="qotd-card"><p>Could not verify user. Please try logging in again.</p></div>';
            return;
        }

        const mainContent = document.getElementById('qotd-main-content');
        const todayContainer = document.getElementById('today-question-container');
        const yesterdayContainer = document.getElementById('yesterday-review-container');
        const archiveContainer = document.getElementById('archive-container');
        const loader = document.getElementById('loader');
        const katexConfig = { delimiters: [{left: '$$', right: '$$', display: true}, {left: '$', right: '$', display: false}] };

        async function loadDefaultView() {
            try {
                const data = await fetchAPI();
                if (loader) loader.style.display = 'none';
                renderTodayQuestion(data.today_question);
                renderReviewCard(data.yesterday_review, yesterdayContainer, "Yesterday's Review");
                renderArchive(data.archive);
                if (window.renderMathInElement) {
                    renderMathInElement(mainContent, katexConfig);
                }
            } catch (error) { 
                handleError(error, loader);
            }
        }
        
        function renderTodayQuestion(q_data) {
            if (!q_data) {
                todayContainer.innerHTML='<div class="qotd-card"><h2>Today\'s Challenge</h2><p>No question scheduled for today. Please check back tomorrow!</p></div>';
                return;
            }
            let h = `<div class="qotd-card"><h2>Today\'s Challenge</h2>`;
            if (q_data.question_image_url) h += `<img src="${q_data.question_image_url}" alt="Question Diagram" class="qotd-image">`;
            h += `<div class="qotd-text">${q_data.question_text}</div>`;
            if (q_data.user_submission) {
                h += `<div class="submitted-answer-box"><p><strong>Your Answer:</strong> ${q_data.user_submission.submitted_answer}</p><p style="font-size:0.9em; opacity:0.8;">Come back tomorrow for the result and solution!</p></div>`;
            } else {
                h += `<form class="qotd-answer-form" id="qotd-form" data-question-id="${q_data.id}"><input type="text" id="answer-input" placeholder="Enter your answer..." required>${q_data.answer_format_hint ? `<p class="answer-hint">${q_data.answer_format_hint}</p>` : ''}<button type="submit" class="btn-primary">Submit Answer</button></form>`;
            }
            h += '</div>';
            todayContainer.innerHTML = h;
            const form = document.getElementById('qotd-form');
            if (form) form.addEventListener('submit', handleAnswerSubmit);
        }
        
        function renderReviewCard(r_data, container, title) {
            if (!r_data) { container.innerHTML = ''; return; }
            let html = `<div class="qotd-card"><h2>${title}</h2>`;
            if (r_data.question_image_url) html += `<img src="${r_data.question_image_url}" alt="Diagram" class="qotd-image">`;
            html += `<div class="qotd-text">${r_data.question_text}</div>`;
            if (r_data.user_submission) {
                const resultClass = r_data.user_submission.result.toLowerCase();
                html += `<div class="submitted-answer-box"><p style="display: flex; justify-content: space-between; align-items: center;"><span><strong>Your Answer:</strong> ${r_data.user_submission.submitted_answer}</span><span class="result-badge ${resultClass}">${r_data.user_submission.result}</span></p></div>`;
            } else {
                html += `<div class="submitted-answer-box"><p>You did not submit an answer for this question.</p></div>`;
            }
            html += `<div class="solution-box"><h3>Solution</h3><p><strong>Correct Answer(s):</strong> ${r_data.correct_answer.replace(/\|/g, ' or ')}</p><div class="solution-text">${r_data.solution_text}</div></div>`;
            html += '</div>';
            container.innerHTML = html;
        }

        function renderArchive(archive_data) {
            if (!archive_data || archive_data.length === 0) return;
            let html = `<div class="qotd-card archive-section"><h2>Previous Questions</h2><div class="archive-accordion" id="archive-accordion">`;
            archive_data.forEach(item => {
                const itemDate = new Date(item.show_date + 'T00:00:00Z');
                const formattedDate = itemDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric', timeZone: 'UTC' });
                const previewText = item.question_text.replace(/(\$\$?)(.*?)(\$\$?)/g, '[math]').substring(0, 50);
                html += `<div class="accordion-item" data-id="${item.id}" data-full-data='${JSON.stringify(item)}'><button class="accordion-header"><span class="accordion-header-title">${previewText}...</span><span class="accordion-header-date">${formattedDate}</span><i class="fas fa-chevron-down toggle-icon"></i></button><div class="accordion-content"><div class="content-placeholder"><p style="padding: 1rem 0;">Loading...</p></div></div></div>`;
            });
            html += `</div></div>`;
            archiveContainer.innerHTML = html;
            const accordion = document.getElementById('archive-accordion');
            if(accordion) accordion.addEventListener('click', handleArchiveClick);
        }
        
        function handleArchiveClick(event) {
            const header = event.target.closest('.accordion-header');
            if (!header) return;
            const item = header.closest('.accordion-item');
            const content = item.querySelector('.accordion-content');
            const placeholder = item.querySelector('.content-placeholder');
            const isOpen = item.classList.contains('open');

            document.querySelectorAll('#archive-accordion .accordion-item.open').forEach(openItem => {
                if (openItem !== item) {
                    openItem.classList.remove('open');
                    openItem.querySelector('.accordion-content').style.maxHeight = '0px';
                }
            });

            if (isOpen) {
                item.classList.remove('open');
                content.style.maxHeight = '0px';
            } else {
                item.classList.add('open');
                if (placeholder && content.innerHTML.includes('content-placeholder')) {
                    const fullData = JSON.parse(item.dataset.fullData);
                    let innerHTML = `<div style="padding: 1.5rem 0;">`;
                    if (fullData.question_image_url) innerHTML += `<img src="${fullData.question_image_url}" alt="Diagram" class="qotd-image">`;
                    innerHTML += `<div class="qotd-text">${fullData.question_text}</div>`;
                    innerHTML += `<div class="solution-box"><h3>Solution</h3><p><strong>Correct Answer(s):</strong> ${fullData.correct_answer.replace(/\|/g, ' or ')}</p><div class="solution-text">${fullData.solution_text}</div></div>`;
                    innerHTML += `</div>`;
                    content.innerHTML = innerHTML;
                    if(window.renderMathInElement) renderMathInElement(content, katexConfig);
                }
                content.style.maxHeight = content.scrollHeight + "px";
            }
        }
        
        async function handleAnswerSubmit(event) {
            event.preventDefault();
            const form = event.target;
            const button = form.querySelector('button');
            const questionId = form.dataset.questionId;
            const answer = document.getElementById('answer-input').value;
            if (!answer.trim()) { alert('Please enter an answer.'); return; }
            button.disabled = true; button.textContent = 'Submitting...';
            try {
                const responseData = await fetchAPI({ question_id: questionId, submitted_answer: answer }, 'api/submit_answer.php');
                if (responseData.success) {
                    todayContainer.innerHTML = '<div class="qotd-card"><h2>Submitted!</h2><p>Your answer has been recorded. Good luck! Check back tomorrow for the result.</p></div>';
                } else {
                    alert(responseData.message || 'An error occurred.');
                    button.disabled = false; button.textContent = 'Submit Answer';
                }
            } catch (e) {
                handleError(e, button, 'Could not submit answer.');
            }
        }
        
        async function fetchAPI(payload = {}, endpoint = 'api/get_daily_question.php') {
    payload.user_name = userName; // Keep this, as your QOTD backend expects it.
    const fullUrl = `${window.API_BASE_URL}/${endpoint}`; // Construct the full URL

    const response = await fetch(fullUrl, { 
        method: 'POST', 
        credentials: 'include', // Add this line
        headers: { 'Content-Type': 'application/json' }, 
        body: JSON.stringify(payload) 
    });
    
    if (!response.ok) throw new Error(`Network error from ${endpoint}: ${response.statusText}`);
    return response.json();
}
        
        function handleError(error, element, message = 'Could not load content.') {
            if (element && element.tagName === 'BUTTON') { 
                element.disabled = false;
                element.textContent = 'Submit Answer'; 
                alert(message); 
            } else if (element) {
                element.innerHTML = `<div class="qotd-card"><p>${message}</p></div>`;
            }
            console.error('API Error:', error);
        }

        loadDefaultView();
    });
    </script>
</body>
</html>