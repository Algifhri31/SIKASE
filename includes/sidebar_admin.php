<?php
// Pastikan session sudah dimulai
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Deteksi halaman aktif
$current_page = basename($_SERVER['PHP_SELF']);

// Basic URL handling
$base_url = isset($base_url) ? $base_url : '';

// Cek role
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'admin';
$is_super_admin = ($user_role === 'super_admin');
?>

<!-- Sidebar Style -->
<style>
    #sidebar-wrapper {
        min-height: 100vh;
        width: 280px;
        margin-left: -280px;
        transition: margin .25s ease-out;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); /* Modern Gradient */
        flex-shrink: 0;
        box-shadow: 4px 0 15px rgba(0,0,0,0.1);
    }

    #sidebar-wrapper .sidebar-heading {
        padding: 1.5rem 1.25rem;
        font-size: 1.5rem;
        font-weight: bold;
        color: #fff;
        text-align: center;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
    }

    #sidebar-wrapper .list-group {
        width: 280px;
    }

    #sidebar-wrapper .list-group-item {
        background-color: transparent;
        color: rgba(255, 255, 255, 0.8);
        border: none;
        padding: 1rem 1.25rem;
        font-size: 1rem;
        transition: all 0.2s;
        border-left: 4px solid transparent;
    }

    #sidebar-wrapper .list-group-item:hover {
        background-color: rgba(255, 255, 255, 0.1);
        color: #fff;
        padding-left: 1.5rem;
        border-left-color: rgba(255, 255, 255, 0.5);
    }

    #sidebar-wrapper .list-group-item.active {
        background-color: rgba(255, 255, 255, 0.2);
        color: #fff;
        font-weight: 600;
        border-left-color: #fff;
    }

    #sidebar-wrapper .list-group-item i {
        width: 25px;
        margin-right: 10px;
        text-align: center;
    }

    .sidebar-divider {
        height: 0;
        margin: 0.5rem 0;
        overflow: hidden;
        border-top: 1px solid rgba(255, 255, 255, 0.15);
    }

    .sidebar-subtitle {
        color: rgba(255, 255, 255, 0.6);
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 1rem 1.25rem 0.5rem;
        font-weight: 700;
    }

    /* Sidebar responsiveness */
    @media (max-width: 768px) {
        #sidebar-wrapper {
            position: fixed;
            z-index: 1000;
        }
        body.sb-sidenav-toggled #sidebar-wrapper {
            margin-left: 0;
        }
    }

    @media (min-width: 768px) {
        #sidebar-wrapper {
            margin-left: 0;
        }
        body.sb-sidenav-toggled #sidebar-wrapper {
            margin-left: -280px;
        }
    }

    #page-content-wrapper {
        flex-grow: 1;
        width: 100%;
        transition: margin .25s ease-out;
    }
</style>

<div id="sidebar-wrapper">
    <div class="sidebar-heading">
        <i class="fas fa-graduation-cap fa-2x mb-2"></i>
        <span>KSE ADMINISTRATOR</span>
    </div>
    
    <div class="list-group list-group-flush mt-2">
        <!-- Menu Utama -->
        <div class="sidebar-subtitle">Menu Utama</div>
        
        <a href="<?php echo $base_url; ?>admin_dashboard_fixed.php" class="list-group-item list-group-item-action <?php echo($current_page == 'admin_dashboard_fixed.php') ? 'active' : ''; ?>">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        
        <a href="<?php echo $base_url; ?>data_absensi_modern.php" class="list-group-item list-group-item-action <?php echo($current_page == 'data_absensi_modern.php') ? 'active' : ''; ?>">
            <i class="fas fa-calendar-check"></i> Data Absensi
        </a>
        
        <a href="<?php echo $base_url; ?>data_keterangan_modern.php" class="list-group-item list-group-item-action <?php echo($current_page == 'data_keterangan_modern.php') ? 'active' : ''; ?>">
            <i class="fas fa-clipboard-list"></i> Data Keterangan
        </a>

        <!-- Super Admin Menu -->
        <?php if ($is_super_admin): ?>
            <div class="sidebar-divider"></div>
            <div class="sidebar-subtitle">Super Admin</div>
            
            <a href="<?php echo $base_url; ?>admin/data_beswan_modern.php" class="list-group-item list-group-item-action <?php echo($current_page == 'data_beswan_modern.php') ? 'active' : ''; ?>">
                <i class="fas fa-users"></i> Data Beswan
            </a>
            
            <a href="<?php echo $base_url; ?>datajabatan_modern.php" class="list-group-item list-group-item-action <?php echo($current_page == 'datajabatan_modern.php') ? 'active' : ''; ?>">
                <i class="fas fa-sitemap"></i> Data Divisi
            </a>
            
            <a href="<?php echo $base_url; ?>data_admin_modern.php" class="list-group-item list-group-item-action <?php echo($current_page == 'data_admin_modern.php') ? 'active' : ''; ?>">
                <i class="fas fa-user-shield"></i> Kelola Admin
            </a>

            <a href="<?php echo $base_url; ?>export.php" class="list-group-item list-group-item-action <?php echo($current_page == 'export.php') ? 'active' : ''; ?>">
                <i class="fas fa-file-export"></i> Export Data
            </a>
        <?php
endif; ?>
        
        <div class="mt-auto pb-4">
            <div class="sidebar-divider mb-3"></div>
            <a href="#" onclick="confirmLogout(event); return false;" class="list-group-item list-group-item-action text-danger" style="color: #ffcccc !important;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
</div>

<script>
    // Global Sidebar Toggle Handler
    document.addEventListener('click', function(e) {
        if (e.target && (e.target.id === 'sidebarToggle' || e.target.closest('#sidebarToggle'))) {
            document.body.classList.toggle('sb-sidenav-toggled');
            const isToggled = document.body.classList.contains('sb-sidenav-toggled');
            document.cookie = "sidebar_toggle=" + isToggled + "; path=/";
        }
    });

    // Global Logout Handler
    if (typeof confirmLogout !== 'function') {
        window.confirmLogout = function(event) {
            if (event) event.preventDefault();
            
            // Check if Swal is loaded
            if (typeof Swal === 'undefined') {
                if (confirm('Apakah Anda yakin ingin keluar?')) {
                    window.location.href = '<?php echo $base_url; ?>logout.php';
                }
                return;
            }

            Swal.fire({
                title: 'Konfirmasi Logout',
                text: "Apakah Anda yakin ingin keluar?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Logout',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?php echo $base_url; ?>logout.php';
                }
            });
        };
    }

    // Sidebar Backdrop logic for mobile
    const backdrop = document.createElement('div');
    backdrop.id = 'sidebar-backdrop';
    document.body.appendChild(backdrop);

    backdrop.addEventListener('click', function() {
        document.body.classList.remove('sb-sidenav-toggled');
        document.cookie = "sidebar_toggle=false; path=/";
    });

    // Auto-hide sidebar on mobile after clicking a link
    document.querySelectorAll('#sidebar-wrapper .list-group-item').forEach(link => {
        link.addEventListener('click', function(e) {
            if (window.innerWidth <= 768 && !this.onclick && !this.getAttribute('onclick')) {
                document.body.classList.remove('sb-sidenav-toggled');
                document.cookie = "sidebar_toggle=false; path=/";
            }
        });
    });
</script>

<style>
    /* Mobile Backdrop Style */
    #sidebar-backdrop {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0,0,0,0.5);
        z-index: 999;
        backdrop-filter: blur(2px);
    }
    
    @media (max-width: 768px) {
        body.sb-sidenav-toggled #sidebar-backdrop {
            display: block;
        }
    }
</style>
