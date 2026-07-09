    </main>

    <!-- Footer | Developed by Durjoy Majumdar | https://www.linkedin.com/in/durjoy-majumdar/ -->
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>Copyright <i aria-hidden="true" class="fa fa-copyright"></i> Dr. Somanath Tripathy | IIT Patna</p>
            <div class="credit">Made with <i class="fa fa-heart" style="color:red;"></i> by <a href="https://www.linkedin.com/in/durjoy-majumdar/" target="_blank" id="developer-credit" style="color: inherit; text-decoration: none; font-weight: bold; transition: color 0.3s;" onmouseover="this.style.color='var(--accent-blue)'" onmouseout="this.style.color='inherit'">Durjoy Majumdar</a></div>
        </div>
    </footer>

    <!-- Bootstrap & Custom Scripts -->
    <!-- Using local bootstrap JS or CDN -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>
    
    <!-- Unified Navbar Interaction Logic -->
    <script src="components/navbar.js"></script>
    
    <!-- Active Link Highlighter for PHP Pages -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const currentPath = window.location.pathname.split('/').pop() || 'index.php';
            const navLinks = document.querySelectorAll('.glass-overlay .nav-link-large, .desktop-nav .nav-link, .desktop-nav .dropdown-item');
            
            navLinks.forEach(link => {
                const href = link.getAttribute('href');
                // Skip anchor-only links (handled by scroll in navbar.js)
                if (href && href.startsWith('#')) return;
                
                // Strip anchors for comparison: compare.php#section -> compare.php
                const linkPath = href ? href.split('#')[0] : '';
                
                if (linkPath === currentPath || (currentPath === 'index.php' && (linkPath === 'index.php' || linkPath === '' || linkPath === '#'))) {
                    link.classList.add('active');
                    const parentDropdown = link.closest('.dropdown');
                    if (parentDropdown) {
                        const toggle = parentDropdown.querySelector('.dropdown-toggle');
                        if (toggle) toggle.classList.add('active');
                    }
                } else {
                    link.classList.remove('active');
                }
            });
        });
    </script>
</body>
</html>
