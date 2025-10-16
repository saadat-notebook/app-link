document.addEventListener('DOMContentLoaded', () => {

    // --- ACCORDION LOGIC ---
    const accordionItems = document.querySelectorAll('.accordion-item');

    accordionItems.forEach(item => {
        const header = item.querySelector('.accordion-header');
        const content = item.querySelector('.accordion-content');

        header.addEventListener('click', () => {
            // Check if the current item is already active
            const isActive = item.classList.contains('active');
            
            // First, close all other accordion items for a cleaner experience
            accordionItems.forEach(otherItem => {
                otherItem.classList.remove('active');
                otherItem.querySelector('.accordion-content').style.maxHeight = '0px';
                otherItem.querySelector('.accordion-content').style.padding = '0 1.25rem';
            });
            
            // If the clicked item was not active, open it
            if (!isActive) {
                item.classList.add('active');
                // Set max-height to the actual scroll height of the content
                content.style.maxHeight = content.scrollHeight + 'px';
                // Add padding for aesthetics
                content.style.padding = '0 1.25rem 1rem 1.25rem'; 
            }
        });
    });
    function closeModal() {
        modal.classList.remove('active');
        iframe.setAttribute('src', '');
    }
    
    modalCloseBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', (e) => {
        if (e.target === modal) { closeModal(); }
    });

});