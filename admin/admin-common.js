/**
 * Tripathy Portfolio CMS — Admin Common JS
 * Shared dark mode toggle and localStorage persistence for all admin pages.
 */
document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('darkModeToggle');
    const icon = document.getElementById('darkModeIcon');

    function setDarkMode(enabled) {
        if (enabled) {
            document.body.classList.add('dark-mode');
            if (icon) icon.className = 'fa fa-sun-o';
        } else {
            document.body.classList.remove('dark-mode');
            if (icon) icon.className = 'fa fa-moon-o';
        }
        localStorage.setItem('darkMode', enabled ? 'true' : 'false');
    }

    // Restore saved preference
    const saved = localStorage.getItem('darkMode');
    if (saved === 'true') {
        setDarkMode(true);
    }

    if (toggle) {
        toggle.addEventListener('click', () => {
            const isDark = document.body.classList.contains('dark-mode');
            setDarkMode(!isDark);
        });
    }
});
