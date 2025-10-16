document.addEventListener('DOMContentLoaded', () => {

    // --- ACCORDION LOGIC ---
    // Finds all accordion items on the page
    const accordionItems = document.querySelectorAll('.accordion-item');

    // Loops through each accordion item to make it interactive
    accordionItems.forEach(item => {
        const header = item.querySelector('.accordion-header');
        const content = item.querySelector('.accordion-content');

        header.addEventListener('click', () => {
            // Checks if the item you clicked is already open
            const isActive = item.classList.contains('active');
            
            // This part ensures only ONE accordion is open at a time.
            // It loops through all items and closes them first.
            accordionItems.forEach(otherItem => {
                otherItem.classList.remove('active');
                otherItem.querySelector('.accordion-content').style.maxHeight = '0px';
                otherItem.querySelector('.accordion-content').style.padding = '0 1.25rem';
            });
            
            // If the item you clicked was closed, this part opens it.
            if (!isActive) {
                item.classList.add('active');
                // Sets the height so the content becomes visible with a smooth animation
                content.style.maxHeight = content.scrollHeight + 'px';
                // Adds some padding for a nice visual look when open
                content.style.padding = '0 1.25rem 1rem 1.25rem'; 
            }
        });
    });
    // Function to close the modal pop-up
    function closeModal() {
        modal.classList.remove('active');
        // IMPORTANT: Clears the iframe src to stop any background loading or video playback
        iframe.setAttribute('src', '');
    }
    
    // Attaches the closeModal function to the 'X' button
    modalCloseBtn.addEventListener('click', closeModal);

    // Also closes the modal if the user clicks on the dark background overlay
    modal.addEventListener('click', (e) => {
        if (e.target === modal) { 
            closeModal(); 
        }
    });

});