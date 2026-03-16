    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>Copyright <i aria-hidden="true" class="fa fa-copyright"></i> Dr. Somanath Tripathy | IIT Patna</p>
            <div class="credit">Made with <i class="fa fa-heart" style="color:red;"></i> by Durjoy Majumdar</div>
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
            const navLinks = document.querySelectorAll('.glass-overlay .nav-link-large');
            
            navLinks.forEach(link => {
                const href = link.getAttribute('href');
                if (href === currentPath) {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            });
        });
    </script>
</body>
</html>
