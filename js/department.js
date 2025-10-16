document.addEventListener('DOMContentLoaded', () => {
    
    // --- Initialize the main Campus Carousel ---
    new Swiper('.hero-swiper', {
        effect: 'coverflow',
        grabCursor: true,
        centeredSlides: true,
        slidesPerView: 'auto',
        loop: true,
        autoplay: {
            delay: 3000,
            disableOnInteraction: false,
        },
        coverflowEffect: {
            rotate: 50,
            stretch: 0,
            depth: 100,
            modifier: 1,
            slideShadows: false, // Cleaner look without shadows
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
    });

    // --- Initialize the Department Gallery Carousel ---
    new Swiper('.gallery-swiper', {
        effect: 'coverflow',
        grabCursor: true,
        centeredSlides: true,
        slidesPerView: 'auto',
        loop: true,
        autoplay: {
            delay: 3500,
            disableOnInteraction: false,
        },
        coverflowEffect: {
            rotate: 50,
            stretch: 0,
            depth: 100,
            modifier: 1,
            slideShadows: true, // Shadows look good here
        },
    });

// ===========================================
// === FINAL: Faculty Profile Modal Logic ======
// ===========================================

const facultyCards = document.querySelectorAll('.faculty-card-v3');
const modalBackdrop = document.getElementById('faculty-modal-backdrop');

if (modalBackdrop && facultyCards.length > 0) {
    const closeModalBtn = modalBackdrop.querySelector('.close-modal-btn');
    const facultyImgEl = document.getElementById('modal-faculty-img');
    const facultyNameEl = document.getElementById('modal-faculty-name');
    const facultyDesignationEl = document.getElementById('modal-faculty-designation');
    const contactInfoContainer = modalBackdrop.querySelector('.modal-contact-info');

    const openFacultyModal = (card) => {
        // 1. Get all data from the card's data attributes
        const data = card.dataset;

        // 2. Populate the main modal elements
        facultyImgEl.src = data.img || 'images/avatar.jpg';
        facultyNameEl.textContent = data.name || 'N/A';
        facultyDesignationEl.textContent = data.designation || 'N/A';
        
        // 3. Dynamically build the contact info list
        contactInfoContainer.innerHTML = ''; // Clear previous info
        
        if (data.bcs) {
            contactInfoContainer.innerHTML += `
                <div class="contact-info-item">
                    <i class="fas fa-award"></i>
                    <span>${data.bcs}</span>
                </div>`;
        }

        if (data.email) {
            contactInfoContainer.innerHTML += `
                <a href="mailto:${data.email}" class="contact-info-item" target="_blank">
                    <i class="fas fa-envelope"></i>
                    <span>${data.email}</span>
                </a>`;
        }
        
        if (data.mobile) {
            contactInfoContainer.innerHTML += `
                <a href="tel:${data.mobile}" class="contact-info-item">
                    <i class="fas fa-phone-alt"></i>
                    <span>${data.mobile}</span>
                </a>`;
        }
        
        if (data.social) {
            contactInfoContainer.innerHTML += `
                <a href="${data.social}" class="contact-info-item" target="_blank">
                    <i class="fab fa-facebook"></i>
                    <span>View Profile on Facebook</span>
                </a>`;
        }
        
        // 4. Show the modal
        document.body.style.overflow = 'hidden';
        modalBackdrop.classList.add('is-visible');
    };
    
    const closeFacultyModal = () => {
        document.body.style.overflow = '';
        modalBackdrop.classList.remove('is-visible');
    };
    
    facultyCards.forEach(card => {
        card.addEventListener('click', () => openFacultyModal(card));
    });

    closeModalBtn.addEventListener('click', closeFacultyModal);
    
    modalBackdrop.addEventListener('click', (e) => {
        if (e.target === modalBackdrop) closeFacultyModal();
    });
}

    // --- Photo Gallery Lightbox Logic ---
    const lightbox = document.getElementById('lightbox-backdrop');
    if (lightbox) {
        const gallerySlides = document.querySelectorAll('.gallery-swiper .swiper-slide img');
        const lightboxImg = lightbox.querySelector('.lightbox-image');
        const lightboxDesc = lightbox.querySelector('.lightbox-description');
        const closeBtn = lightbox.querySelector('.close-lightbox-btn');
        
        const openLightbox = (imgElement) => {
            lightboxImg.src = imgElement.src;
            const description = imgElement.dataset.description;
            if (description) {
                lightboxDesc.textContent = description;
                lightboxDesc.style.display = 'block';
            } else {
                lightboxDesc.style.display = 'none';
            }
            lightbox.classList.add('is-visible');
            document.body.style.overflow = 'hidden';
        };

        const closeLightbox = () => {
            lightbox.classList.remove('is-visible');
            document.body.style.overflow = '';
        };

        gallerySlides.forEach(img => img.addEventListener('click', () => openLightbox(img)));
        closeBtn.addEventListener('click', closeLightbox);
        lightbox.addEventListener('click', (e) => { if (e.target === lightbox) closeLightbox(); });
    }
});