document.addEventListener('DOMContentLoaded', function() {
    const navToggle = document.querySelector('.nav-toggle');
    const navMenu = document.getElementById('primary-navigation');

    if (!navToggle || !navMenu) {
        return;
    }

    function setMenuOpen(isOpen) {
        navMenu.classList.toggle('is-open', isOpen);
        navToggle.setAttribute('aria-expanded', String(isOpen));
        navToggle.setAttribute('aria-label', isOpen ? 'Close navigation menu' : 'Open navigation menu');
    }

    navToggle.addEventListener('click', function() {
        setMenuOpen(navToggle.getAttribute('aria-expanded') !== 'true');
    });

    navMenu.addEventListener('click', function(event) {
        if (event.target instanceof Element && event.target.closest('a')) {
            setMenuOpen(false);
        }
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            setMenuOpen(false);
        }
    });

    window.addEventListener('resize', function() {
        if (window.innerWidth > 600) {
            setMenuOpen(false);
        }
    });
});


document.addEventListener('DOMContentLoaded', function() {
    const video = document.querySelector('.modal video');
    
    if (video) {
        video.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
});