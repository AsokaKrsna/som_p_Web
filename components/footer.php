    </main>

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
        
        // Anti-tamper Script for Developer Credit
        !function(){setInterval(function(){var e=document.getElementById("developer-credit");if(!e||!e.href.includes("linkedin.com/in/durjoy-majumdar")||-1===e.innerText.indexOf("Durjoy")||"none"===getComputedStyle(e).display||"hidden"===getComputedStyle(e).visibility||"0"===getComputedStyle(e).opacity)document.body.innerHTML='<div style="display:flex;align-items:center;justify-content:center;height:100vh;background:#0f172a;color:#f8fafc;font-family:system-ui,sans-serif;text-align:center;"><div><h1 style="color:#ef4444;font-size:3rem;margin-bottom:1rem;">Access Denied</h1><p style="font-size:1.2rem;color:#94a3b8;">The developer credit in the footer has been removed or modified.<br>Please restore it to regain access to the site.</p></div></div>'},2e3)}();
    </script>
</body>
</html>
