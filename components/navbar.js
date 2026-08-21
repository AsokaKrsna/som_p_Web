document.addEventListener("DOMContentLoaded", () => {
    const menuToggle = document.getElementById('mobile-menu');
    const glassOverlay = document.getElementById('overlayMenu');
    const navbar = document.querySelector('.custom-navbar');
    const scrollProgress = document.getElementById('scrollProgress');
    const backToTop = document.getElementById('backToTop');
    const sideNavItems = document.querySelectorAll('.nav-side-item');
    const sideNavToggle = document.getElementById('navSideToggle');
    const sideNav = document.getElementById('navSide');
    const sections = document.querySelectorAll('section[id]');
    const indicatorDots = document.querySelectorAll('.scroll-indicator-dot');
    const indicatorLabel = document.getElementById('scrollIndicatorLabel');
    const timelineFill = document.getElementById('navSideTimelineFill');

    const sectionNames = {
        'home': 'Home',
        'bio': 'About',
        'publications': 'Publications',
        'patents': 'Patents',
        'projects': 'Projects',
        'memberships': 'Memberships',
        'editorship': 'Editorship',
        'admin-responsibilities': 'Admin Roles',
        'teaching': 'Teaching',
        'seminars': 'Seminars',
        'other-responsibilities': 'Recognition',
        'awards': 'Awards'
    };

    // ──────────────────────────────────────────────
    // Mobile menu toggle
    // ──────────────────────────────────────────────
    if (menuToggle && glassOverlay) {
        menuToggle.addEventListener('click', () => {
            menuToggle.classList.toggle('active');
            glassOverlay.classList.toggle('active');
            document.body.classList.toggle('no-scroll');
        });
    }

    // ──────────────────────────────────────────────
    // Navbar scroll effect
    // ──────────────────────────────────────────────
    if (navbar) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    }

    // ──────────────────────────────────────────────
    // Scroll progress bar
    // ──────────────────────────────────────────────
    function updateScrollProgress() {
        if (!scrollProgress) return;
        const scrollTop = window.scrollY;
        const docHeight = document.documentElement.scrollHeight - window.innerHeight;
        if (docHeight <= 0) {
            scrollProgress.style.width = '0%';
            return;
        }
        const progress = Math.min((scrollTop / docHeight) * 100, 100);
        scrollProgress.style.width = progress + '%';
    }

    // ──────────────────────────────────────────────
    // Back to top button
    // ──────────────────────────────────────────────
    function toggleBackToTop() {
        if (!backToTop) return;
        if (window.scrollY > 500) {
            backToTop.classList.add('visible');
        } else {
            backToTop.classList.remove('visible');
        }
    }

    if (backToTop) {
        backToTop.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // ──────────────────────────────────────────────
    // Get current active section ID
    // ──────────────────────────────────────────────
    function getCurrentSection() {
        let current = '';
        const navHeight = navbar ? navbar.offsetHeight : 80;

        sections.forEach(section => {
            const rect = section.getBoundingClientRect();
            if (rect.top <= navHeight + 150) {
                current = section.getAttribute('id');
            }
        });

        return current;
    }

    // ──────────────────────────────────────────────
    // Side nav – update active section on scroll
    // ──────────────────────────────────────────────
    function updateSideNav(current) {
        if (!sideNavItems.length) return;

        sideNavItems.forEach(item => {
            const sectionId = item.getAttribute('data-section');
            if (sectionId === current) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }

            // Mark sections above current as "visited"
            if (current) {
                const order = ['home','bio','publications','patents','projects','memberships','editorship','admin-responsibilities','teaching','seminars','other-responsibilities','awards'];
                const currentIdx = order.indexOf(current);
                const itemIdx = order.indexOf(sectionId);
                if (itemIdx < currentIdx) {
                    item.classList.add('visited');
                } else {
                    item.classList.remove('visited');
                }
            }
        });
    }

    // ──────────────────────────────────────────────
    // Side nav timeline fill
    // ──────────────────────────────────────────────
    function updateTimelineFill() {
        if (!timelineFill) return;
        const activeItem = document.querySelector('.nav-side-item.active');
        if (activeItem) {
            const currentSectionId = activeItem.getAttribute('data-section');
            const currentSection = document.getElementById(currentSectionId);
            
            let sectionProgress = 0;
            if (currentSection) {
                const rect = currentSection.getBoundingClientRect();
                const navHeight = navbar ? navbar.offsetHeight : 80;
                const scrolledWithin = (navHeight + 150) - rect.top;
                const sectionHeight = rect.height || currentSection.offsetHeight;
                sectionProgress = Math.max(0, Math.min(1, scrolledWithin / sectionHeight));
            }

            let nextItem = activeItem.nextElementSibling;
            
            const activeTop = activeItem.offsetTop + 12; // approximate center of dot
            let nextTop = activeTop;
            if (nextItem) {
                nextTop = nextItem.offsetTop + 12;
            } else {
                nextTop = document.querySelector('.nav-side-items').offsetHeight; // end of track
            }
            
            const fillHeight = activeTop + (nextTop - activeTop) * sectionProgress;
            timelineFill.style.height = fillHeight + 'px';
        } else {
            timelineFill.style.height = '0px';
        }
    }



    // ──────────────────────────────────────────────
    // Helper: close tablet side nav
    // ──────────────────────────────────────────────
    function closeTabletSideNav() {
        if (sideNavToggle && sideNav) {
            sideNavToggle.classList.remove('open');
            sideNav.classList.remove('open');
            const icon = sideNavToggle.querySelector('i');
            if (icon) icon.className = 'fa fa-chevron-right';
        }
    }



    // ──────────────────────────────────────────────
    // Smooth scroll for anchor links (side nav & any # links)
    // ──────────────────────────────────────────────
    document.querySelectorAll('a[href^="#"], a[href*="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (!href || href === '#') return;

            const hashIndex = href.indexOf('#');
            if (hashIndex === -1) return;
            const targetId = href.substring(hashIndex);
            if (!targetId || targetId === '#') return;

            const pathPart = href.substring(0, hashIndex);
            const currentPage = window.location.pathname.split('/').pop() || 'index.php';
            if (pathPart && pathPart !== currentPage && !pathPart.endsWith('/' + currentPage)) return;

            const target = document.querySelector(targetId);
            if (target) {
                e.preventDefault();
                const navHeight = navbar ? navbar.offsetHeight : 80;
                const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - navHeight;

                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });

                // Close mobile overlay if open
                if (menuToggle && glassOverlay) {
                    menuToggle.classList.remove('active');
                    glassOverlay.classList.remove('active');
                    document.body.classList.remove('no-scroll');
                }

                // Close tablet side nav
                closeTabletSideNav();

                // Update URL hash without jumping
                history.pushState(null, null, targetId);
            }
        });
    });

    // ──────────────────────────────────────────────
    // Collapsible side nav toggle (tablets)
    // ──────────────────────────────────────────────
    if (sideNavToggle && sideNav) {
        sideNavToggle.addEventListener('click', () => {
            sideNavToggle.classList.toggle('open');
            sideNav.classList.toggle('open');

            const icon = sideNavToggle.querySelector('i');
            if (icon) {
                if (sideNav.classList.contains('open')) {
                    icon.className = 'fa fa-chevron-left';
                } else {
                    icon.className = 'fa fa-chevron-right';
                }
            }
        });

        // Close side nav when clicking a link (on tablets)
        sideNav.querySelectorAll('.nav-side-item').forEach(item => {
            item.addEventListener('click', () => {
                if (window.innerWidth >= 768 && window.innerWidth <= 991) {
                    sideNavToggle.classList.remove('open');
                    sideNav.classList.remove('open');
                    const icon = sideNavToggle.querySelector('i');
                    if (icon) icon.className = 'fa fa-chevron-right';
                }
            });
        });
    }

    // ──────────────────────────────────────────────
    // Staggered entrance animation for side nav items
    // ──────────────────────────────────────────────
    sideNavItems.forEach((item, index) => {
        item.style.setProperty('--item-index', index);
    });

    // ──────────────────────────────────────────────
    // Dark Mode Toggle
    // ──────────────────────────────────────────────
    const darkModeToggle = document.getElementById('darkModeToggle');
    const darkModeIcon = document.getElementById('darkModeIcon');
    const overlayDarkModeToggle = document.getElementById('overlayDarkModeToggle');
    const overlayDarkModeIcon = document.getElementById('overlayDarkModeIcon');
    const overlayDarkModeLabel = document.getElementById('overlayDarkModeLabel');

    let darkModeTimeout;

    function setDarkMode(enabled) {
        // Enable smooth transition class during toggle
        clearTimeout(darkModeTimeout);
        document.body.classList.add('dark-mode-transitioning');
        
        if (enabled) {
            document.body.classList.add('dark-mode');
            if (darkModeIcon) {
                darkModeIcon.className = 'fa fa-sun-o';
            }
            if (overlayDarkModeIcon) {
                overlayDarkModeIcon.className = 'fa fa-sun-o';
            }
            if (overlayDarkModeLabel) {
                overlayDarkModeLabel.textContent = 'Light Mode';
            }
        } else {
            document.body.classList.remove('dark-mode');
            if (darkModeIcon) {
                darkModeIcon.className = 'fa fa-moon-o';
            }
            if (overlayDarkModeIcon) {
                overlayDarkModeIcon.className = 'fa fa-moon-o';
            }
            if (overlayDarkModeLabel) {
                overlayDarkModeLabel.textContent = 'Dark Mode';
            }
        }
        
        // Remove transition class after animation completes
        darkModeTimeout = setTimeout(() => {
            document.body.classList.remove('dark-mode-transitioning');
        }, 500);

        // Persist preference
        localStorage.setItem('darkMode', enabled ? 'true' : 'false');
    }

    function toggleDarkMode() {
        const isDark = document.body.classList.contains('dark-mode');
        setDarkMode(!isDark);
    }

    if (darkModeToggle) {
        darkModeToggle.addEventListener('click', toggleDarkMode);
    }
    if (overlayDarkModeToggle) {
        overlayDarkModeToggle.addEventListener('click', toggleDarkMode);
    }

    // Restore saved dark mode preference
    const savedDarkMode = localStorage.getItem('darkMode');
    if (savedDarkMode === 'true') {
        setDarkMode(true);
    }

    // ──────────────────────────────────────────────
    // Unified scroll update
    // ──────────────────────────────────────────────
    function onScroll() {
        const current = getCurrentSection();
        updateScrollProgress();
        toggleBackToTop();
        updateSideNav(current);
        updateTimelineFill();

    }

    window.addEventListener('scroll', onScroll);

    // Run once on load
    onScroll();
});
