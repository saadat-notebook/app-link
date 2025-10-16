// js/user-session.js (THE NEW CENTRAL HUB)

window.NotebookSession = {
    isLoggedIn: false,
    userData: null,
    bookmarks: [],
    isLoaded: false,
    onReady: function(callback) {
        if (this.isLoaded) {
            callback();
        } else {
            document.addEventListener('sessionReady', callback);
        }
    }
};

async function initializeSession() {
    try {
        const response = await fetch(`${window.API_BASE_URL}/api/get-user-session.php`, {credentials: 'include'});
        const data = await response.json();
        window.NotebookSession.isLoggedIn = data.isLoggedIn;
        window.NotebookSession.userData = data.userData;
        window.NotebookSession.bookmarks = data.bookmarks;
    } catch (error) {
        console.error("Failed to initialize session:", error);
    } finally {
        window.NotebookSession.isLoaded = true;
        document.dispatchEvent(new Event('sessionReady'));
    }
}

initializeSession();