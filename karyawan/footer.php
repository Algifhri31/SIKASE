        </div> <!-- End page-content -->
    </main>

    <!-- JS Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Toggle Sidebar function
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
            document.getElementById('sidebarOverlay').classList.toggle('show');
        }

        // Logout confirmation with SweetAlert2
        function confirmLogout(event) {
            if(event) event.preventDefault();
            
            Swal.fire({
                title: 'Konfirmasi Logout',
                text: "Apakah Anda yakin ingin keluar dari sistem?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Logout Sekarang',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                padding: '2em',
                customClass: {
                    container: 'my-swal'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'logout.php';
                }
            });
        }

        // Active Link Handler (Double Check)
        document.addEventListener('DOMContentLoaded', function() {
            const currentUrl = window.location.search;
            const navLinks = document.querySelectorAll('.nav-link-custom');
            
            navLinks.forEach(link => {
                const href = link.getAttribute('href');
                if (href && currentUrl.includes(href.replace('index.php', ''))) {
                    // Check logic might need refinement based on exact params
                }
            });

            // Update realtime clock in topbar
            function updateClock() {
                const now = new Date();
                const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                
                const timeStr = days[now.getDay()] + ', ' + 
                                now.getDate().toString().padStart(2, '0') + ' ' + 
                                months[now.getMonth()] + ' ' + 
                                now.getFullYear() + ' • ' + 
                                now.getHours().toString().padStart(2, '0') + ':' + 
                                now.getMinutes().toString().padStart(2, '0') + ':' + 
                                now.getSeconds().toString().padStart(2, '0');
                
                const timeEl = document.getElementById('currentDate');
                if (timeEl) timeEl.textContent = timeStr;
            }
            setInterval(updateClock, 1000);
            updateClock();
        });
    </script>
</body>
</html>