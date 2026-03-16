document.addEventListener("DOMContentLoaded", () => {
    const menuToggle = document.getElementById('mobile-menu');
    const glassOverlay = document.getElementById('overlayMenu');
    const navbar = document.querySelector('.custom-navbar');

    if (menuToggle && glassOverlay) {
        menuToggle.addEventListener('click', () => {
            menuToggle.classList.toggle('active');
            glassOverlay.classList.toggle('active');
            document.body.classList.toggle('no-scroll');
        });
    }

    // Navbar scroll effect
    if (navbar) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    }
});
