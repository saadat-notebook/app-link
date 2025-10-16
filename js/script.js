// js/script.js (NEW SIMPLIFIED VERSION)

document.addEventListener('DOMContentLoaded', () => {

    // This function's only job is to get the custom profile picture
    // from localStorage and update the image tag on the page.
    function loadProfilePicture() {
        const userAvatarImg = document.getElementById('user-avatar-img');
        
        // This must be a different key from the old system to avoid conflicts.
        const customPic = localStorage.getItem('saadatNotesCustomUserPic');

        if (userAvatarImg && customPic) {
            userAvatarImg.src = customPic;
        }
        // If no custom pic is found, the page will just show the default 
        // avatar.jpg that is set in the HTML, which is exactly what we want.
    }

    // Run the function as soon as the page is ready.
    loadProfilePicture();
});