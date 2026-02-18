<?php
$page_title = "Edit Profil";
include '../koneksi.php';

$id = $_SESSION['idsi'];
$nama_user = $_SESSION['namasi'];

// Ambil data profil
$sql = "SELECT * FROM tb_karyawan WHERE id_karyawan = '$id'";
$result = mysqli_query($koneksi, $sql);
$profil = mysqli_fetch_assoc($result);

// Proses update profil
if (isset($_POST['update'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $no_hp = mysqli_real_escape_string($koneksi, $_POST['no_hp']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    
    $update_sql = "UPDATE tb_karyawan SET 
                   nama = '$nama',
                   email = '$email',
                   no_hp = '$no_hp',
                   alamat = '$alamat'
                   WHERE id_karyawan = '$id'";
    
    if (mysqli_query($koneksi, $update_sql)) {
        $_SESSION['namasi'] = $nama; // Update session
        echo "<script>alert('Profil berhasil diupdate!'); window.location.href='?m=karyawan&s=profil';</script>";
    } else {
        echo "<script>alert('Gagal update profil!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil - Beswan KSE</title>
    
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
        
        .form-label {
            font-size: 14px;
            color: #64748b;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .form-control {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 14px;
            color: #2b3144;
        }

        .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
        }

        .btn {
            font-size: 14px;
            font-weight: 500;
            padding: 12px 24px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
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
                    <h5 class="mb-0 fw-bold text-dark">Edit Profil</h5>
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
        
        <!-- Edit Form -->
        <div class="card">
            <div class="card-header bg-white" style="border-bottom: 1px solid #eef0f7; padding: 25px;">
                <div class="d-flex align-items-center">
                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                        <i class="fas fa-edit" style="color: white; font-size: 16px;"></i>
                    </div>
                    <div>
                        <h5 class="card-title mb-0" style="color: #2b3144; font-weight: 600;">Edit Profil</h5>
                        <p class="text-muted mb-0" style="font-size: 14px;">Update informasi profil Anda</p>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">ID Karyawan</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($profil['id_karyawan'] ?? $id); ?>" readonly>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" name="nama" value="<?php echo htmlspecialchars($profil['nama'] ?? $nama_user); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($profil['email'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">No. Telepon</label>
                                <input type="text" class="form-control" name="no_hp" value="<?php echo htmlspecialchars($profil['no_hp'] ?? ''); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Alamat</label>
                                <textarea class="form-control" name="alamat" rows="4"><?php echo htmlspecialchars($profil['alamat'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center mt-4">
                        <button type="submit" name="update" class="btn btn-primary me-2">
                            <i class="fas fa-save me-2"></i>Simpan Perubahan
                        </button>
                        <a href="?m=karyawan&s=profil" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Batal
                        </a>
                    </div>
                </form>
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