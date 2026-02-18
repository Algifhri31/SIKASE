    </div> <!-- End Main Content -->
    
    <!-- Footer -->
    <footer class="text-center py-3 mt-5" style="margin-left: 250px;">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <hr class="my-3">
                    <p class="text-muted mb-0" style="font-size: 14px;">
                        &copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?> v<?php echo APP_VERSION; ?>. 
                        Sistem Informasi Kehadiran Beasiswa KSE.
                    </p>
                    <small class="text-muted">
                        Dibuat dengan <i class="fas fa-heart text-danger"></i> untuk kemudahan absensi
                    </small>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- JS Assets -->
    <?php foreach ($dashboard_assets['js'] as $js): ?>
    <script src="<?php echo $js; ?>"></script>
    <?php endforeach; ?>
    
    <!-- Custom Scripts -->
    <script>
        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', function() {
            // Update active menu
            const currentUrl = window.location.search;
            const menuLinks = document.querySelectorAll('.sidebar-menu a');
            
            menuLinks.forEach(link => {
                if (link.getAttribute('href') === currentUrl || 
                    (currentUrl === '' && link.getAttribute('href') === '?m=awal')) {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            });
            
            // Auto-hide alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    setTimeout(() => {
                        alert.remove();
                    }, 300);
                }, 5000);
            });
        });
        
        // Global error handler
        window.addEventListener('error', function(e) {
            console.error('JavaScript Error:', e.error);
        });
        
        // Service Worker for offline functionality (optional)
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('sw.js').then(function(registration) {
                    console.log('SW registered: ', registration);
                }).catch(function(registrationError) {
                    console.log('SW registration failed: ', registrationError);
                });
            });
        }
    </script>
    
    <!-- Mobile Footer -->
    <style>
        @media (max-width: 768px) {
            footer {
                margin-left: 0 !important;
                padding: 15px !important;
            }
        }
    </style>
</body>
</html>