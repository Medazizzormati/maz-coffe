// ===== FONCTIONS POUR LA PAGE D'ACCUEIL =====
function initSlideshow() {
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.dot');
    let currentSlide = 0;

    function showSlide(index) {
        // Masquer toutes les slides
        slides.forEach(slide => {
            slide.classList.remove('active');
        });

        // Désactiver tous les dots
        dots.forEach(dot => {
            dot.classList.remove('active');
        });

        // Afficher la slide demandée
        slides[index].classList.add('active');
        dots[index].classList.add('active');
        currentSlide = index;
    }

    // Gestion des clics sur les dots
    dots.forEach(dot => {
        dot.addEventListener('click', function () {
            const slideIndex = parseInt(this.getAttribute('data-index'));
            showSlide(slideIndex);
        });
    });

    // Changement automatique des slides
    function nextSlide() {
        let nextIndex = currentSlide + 1;
        if (nextIndex >= slides.length) {
            nextIndex = 0;
        }
        showSlide(nextIndex);
    }

    // Démarrer le slideshow
    if (slideInterval) {
        clearInterval(slideInterval);
    }
    slideInterval = setInterval(nextSlide, 2500);

    // Arrêt du slideshow au survol
    const slideshowContainer = document.querySelector('.slideshow-container');
    if (slideshowContainer) {
        slideshowContainer.addEventListener('mouseenter', function () {
            clearInterval(slideInterval);
        });

        slideshowContainer.addEventListener('mouseleave', function () {
            slideInterval = setInterval(nextSlide, 2500);
        });
    }
}

// ===== GESTION DE LA NAVIGATION POUR L'INDEX =====
function initNavigation() {
    // Navigation entre les pages (pour la version single page)
    document.querySelectorAll('#nav-home, #nav-menu, #nav-contact').forEach(navItem => {
        navItem.addEventListener('click', function (e) {
            e.preventDefault();
            const pageId = this.id.replace('nav-', '');

            // Si ce n'est pas la page d'accueil, rediriger vers la page correspondante
            if (pageId === 'menu') {
                window.location.href = 'menu.php';
            } else if (pageId === 'contact') {
                window.location.href = 'contact.php';
            }
        });
    });
}

// ===== INITIALISATION DE LA PAGE D'ACCUEIL =====
function initHomePage() {
    // Initialiser le slideshow
    initSlideshow();

    // Initialiser la navigation
    initNavigation();

    // Bouton CTA pour aller au menu
    const ctaButton = document.getElementById('cta-button');
    if (ctaButton) {
        ctaButton.addEventListener('click', function (e) {
            e.preventDefault();
            window.location.href = 'menu.html';
        });

        // Effet sur le bouton CTA
        ctaButton.addEventListener('mouseenter', function () {
            this.style.transform = 'scale(1.05)';
            this.style.boxShadow = '0 10px 20px rgba(139, 69, 19, 0.3)';
        });

        ctaButton.addEventListener('mouseleave', function () {
            this.style.transform = 'scale(1)';
            this.style.boxShadow = '0 10px 20px rgba(139, 69, 19, 0.2)';
        });
    }
}

// Initialiser la page d'accueil au chargement
document.addEventListener('DOMContentLoaded', initHomePage);