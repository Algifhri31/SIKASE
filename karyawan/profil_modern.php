<?php
$page_title = "Profil Saya";
include '../koneksi.php';

$id = $_SESSION['idsi'];
$nama_user = $_SESSION['namasi'];

// Ambil data profil
$sql = "SELECT * FROM tb_karyawan WHERE id_karyawan = '$id'";
$result = mysqli_query($koneksi, $sql);
$profil = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - Beswan KSE</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        
        .sidebar {
            width: 250px;
            height: 100vh;
            background: #fff;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
        }
        
        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid #eee;
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .sidebar-menu a {
            display: block;
            padding: 12px 20px;
            color: #6c757d;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: #e3f2fd;
            color: #1976d2;
        }
        
        .sidebar-menu i {
            width: 20px;
            margin-right: 10px;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
            min-height: 100vh;
        }
        
        .top-bar {
            background: #fff;
            padding: 15px 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 25px;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 25px;
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        
        .mobile-menu-btn {
            display: none;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                padding: 15px;
            }
            
            .mobile-menu-btn {
                display: block;
                background: none;
                border: none;
                font-size: 20px;
                color: #495057;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="d-flex align-items-center">
                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                    <i class="fas fa-graduation-cap" style="color: white; font-size: 18px;"></i>
                </div>
                <h5 class="mb-0 fw-bold" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Beswan KSE</h5>
            </div>
        </div>
        
        <div class="sidebar-menu">
            <a href="?m=awal">
                <i class="fas fa-tachometer-alt"></i>Dashboard
            </a>
            <a href="?m=presensi">
                <i class="fas fa-clock"></i>Presensi
            </a>
            <a href="?m=karyawan&s=profil" class="active">
                <i class="fas fa-user"></i>Profil Saya
            </a>
            <a href="?m=karyawan&s=riwayat">
                <i class="fas fa-history"></i>Riwayat Absen
            </a>
            <div style="margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px;">
                <a href="logout.php" style="color: #dc3545;" onclick="return confirm('Yakin ingin logout?')">
                    <i class="fas fa-sign-out-alt"></i>Logout
                </a>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <div class="top-bar">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <button class="mobile-menu-btn me-3" onclick="toggleSidebar()">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h5 class="mb-0 fw-bold text-dark">Profil Saya</h5>
                </div>
                <div class="d-flex align-items-center">
                    <span class="text-muted me-3 d-none d-md-block" style="font-size: 14px;">
                        <?php echo date('d M Y, H:i'); ?>
                    </span>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-user-circle text-primary me-2" style="font-size: 24px;"></i>
                        <span class="fw-medium d-none d-sm-block"><?php echo htmlspecialchars($nama_user); ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Profile Card -->
        <div class="card">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <div class="profile-avatar">
                        <i class="fas fa-user" style="color: white; font-size: 48px;"></i>
                    </div>
                    <h2 class="fw-bold text-dark mb-2"><?php echo htmlspecialchars($profil['nama'] ?? $nama_user); ?></h2>
                    <p class="text-muted">Beasiswa KSE</p>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">ID Karyawan</label>
                            <p class="form-control-plaintext"><?php echo htmlspecialchars($profil['id_karyawan'] ?? $id); ?></p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Lengkap</label>
                            <p class="form-control-plaintext"><?php echo htmlspecialchars($profil['nama'] ?? $nama_user); ?></p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <p class="form-control-plaintext"><?php echo htmlspecialchars($profil['email'] ?? '-'); ?></p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">No. Telepon</label>
                            <p class="form-control-plaintext"><?php echo htmlspecialchars($profil['no_hp'] ?? '-'); ?></p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Alamat</label>
                            <p class="form-control-plaintext"><?php echo htmlspecialchars($profil['alamat'] ?? '-'); ?></p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Status</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-success">Aktif</span>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <a href="?m=karyawan&s=edit" class="btn btn-primary me-2">
                        <i class="fas fa-edit me-2"></i>Edit Profil
                    </a>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Toggle Sidebar for Mobile
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.classList.toggle('show');
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.querySelector('.sidebar');
            const menuBtn = document.querySelector('.mobile-menu-btn');
            
            if (window.innerWidth <= 768) {
                if (!sidebar.contains(event.target) && !menuBtn.contains(event.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });
    </script>
</body>
</html>