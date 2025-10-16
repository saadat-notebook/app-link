// js/bookmarks.js (NEW DEDICATED FILE)

function updateAllBookmarkButtons() {
    const bookmarks = window.NotebookSession.bookmarks;
    document.querySelectorAll('.bookmark-btn').forEach(button => {
        const itemId = button.dataset.itemId;
        if (!itemId) return;
        
        const icon = button.querySelector('i');
        if (bookmarks.includes(itemId)) {
            button.classList.add('active');
            if (icon) icon.classList.replace('far', 'fas');
        } else {
            button.classList.remove('active');
            if (icon) icon.classList.replace('fas', 'far');
        }
    });
}

function setupBookmarkListeners() {
    document.body.addEventListener('click', async function (event) {
        const button = event.target.closest('.bookmark-btn');
        if (!button) return;

        event.preventDefault();
        const itemId = button.dataset.itemId;
        if (!itemId) return;

        if (!window.NotebookSession.isLoggedIn) {
            alert('Please log in to save items.');
            window.location.href = 'login.php';
            return;
        }

        try {
            const response = await fetch(`${window.API_BASE_URL}/api/toggle-bookmark.php`, {
    method: 'POST',
    credentials: 'include', // Add this line
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ upload_id: itemId })
});
            
            const data = await response.json();
            if (data.success) {
                // Update the global bookmark list
                if (data.status === 'added') {
                    if (!window.NotebookSession.bookmarks.includes(itemId)) {
                        window.NotebookSession.bookmarks.push(itemId);
                    }
                } else { // removed
                    window.NotebookSession.bookmarks = window.NotebookSession.bookmarks.filter(id => id !== itemId);
                }
                // Re-sync all buttons on the page to reflect the change
                updateAllBookmarkButtons();
            } else {
                alert(data.message || 'Could not update bookmark.');
            }
        } catch (error) {
            console.error('Bookmark toggle error:', error);
        }
    });
}

// Wait for the session data to be ready, then activate everything.
window.NotebookSession.onReady(() => {
    if (window.NotebookSession.isLoggedIn) {
        updateAllBookmarkButtons();
    }
    setupBookmarkListeners();
});