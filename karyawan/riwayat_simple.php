<?php
$page_title = "Riwayat Absen";

// Cek session
if (!isset($_SESSION['idsi']) || !isset($_SESSION['level']) || $_SESSION['level'] !== 'beswan') {
    echo "<script>alert('Silakan login terlebih dahulu'); window.location.href='../login.php';</script>";
    exit;
}

// Include koneksi
include '../koneksi.php';

$id = $_SESSION['idsi'];
$nama_user = $_SESSION['namasi'] ?? 'User';

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Filter
$filter_bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('Y-m');

// Get total records
$count_sql = "SELECT COUNT(*) as total FROM tb_absen WHERE id_karyawan = '$id'";
if ($filter_bulan) {
    $count_sql .= " AND DATE_FORMAT(waktu, '%Y-%m') = '$filter_bulan'";
}
$count_result = mysqli_query($koneksi, $count_sql);
$total_records = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_records / $limit);

// Get absen data with pagination
$sql = "SELECT * FROM tb_absen WHERE id_karyawan = '$id'";
if ($filter_bulan) {
    $sql .= " AND DATE_FORMAT(waktu, '%Y-%m') = '$filter_bulan'";
}
$sql .= " ORDER BY waktu DESC LIMIT $limit OFFSET $offset";
$result = mysqli_query($koneksi, $sql);

// Get statistics for current month
$stats_sql = "SELECT 
    COUNT(*) as total_absen,
    SUM(CASE WHEN tipe = 'masuk' THEN 1 ELSE 0 END) as total_masuk,
    SUM(CASE WHEN tipe = 'pulang' THEN 1 ELSE 0 END) as total_pulang
    FROM tb_absen WHERE id_karyawan = '$id'";
if ($filter_bulan) {
    $stats_sql .= " AND DATE_FORMAT(waktu, '%Y-%m') = '$filter_bulan'";
}
$stats_result = mysqli_query($koneksi, $stats_sql);
$stats = mysqli_fetch_assoc($stats_result);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Absen - Beswan KSE</title>
    
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
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .back-btn {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 10px 20px;
            text-decoration: none;
            color: #495057;
            display: inline-flex;
            align-items: center;
            margin-bottom: 20px;
            transition: all 0.3s;
        }
        
        .back-btn:hover {
            background: #f8f9fa;
            color: #495057;
            text-decoration: none;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 25px;
        }
        
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
        }
        
        .stat-item {
            text-align: center;
            padding: 20px;
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
        }
        
        .table th {
            border: none;
            font-weight: 600;
            color: #495057;
            font-size: 14px;
            background: #f8f9fa;
        }
        
        .table td {
            border: none;
            vertical-align: middle;
            font-size: 14px;
            padding: 15px 12px;
        }
        
        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        .badge {
            font-size: 12px;
            padding: 6px 12px;
            border-radius: 6px;
        }
        
        .filter-form {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
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
        
        .stats-card {
            background: #fff;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 25px;
        }
        
        .stat-item {
            text-align: center;
            padding: 15px;
        }
        
        .stat-number {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #6c757d;
            font-size: 14px;
            font-weight: 500;
        }
        
        .history-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        
        .history-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            text-align: center;
        }
        
        .absen-item {
            padding: 20px 25px;
            border-bottom: 1px solid #f1f3f4;
            transition: all 0.3s ease;
        }
        
        .absen-item:hover {
            background: #f8f9fa;
        }
        
        .absen-item:last-child {
            border-bottom: none;
        }
        
        .absen-type {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .type-masuk {
            background: #d4edda;
            color: #155724;
        }
        
        .type-pulang {
            background: #f8d7da;
            color: #721c24;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .main-content {
                margin-left: 0;
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
            <a href="?m=karyawan&s=profil">
                <i class="fas fa-user"></i>Profil Saya
            </a>
            <a href="?m=karyawan&s=riwayat" class="active">
                <i class="fas fa-history"></i>Riwayat Absen
            </a>
            <div style="margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px;">
                <a href="logout.php" style="color: #dc3545;" onclick="return confirm('Yakin ingin logout?')">
                    <i class="fas fa-sign-out-alt"></i>Logout
                </a>
            </div>
        </div>
    </div>
    
    <!-- Sidebar -->
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
            <a href="?m=karyawan&s=profil">
                <i class="fas fa-user"></i>Profil Saya
            </a>
            <a href="?m=karyawan&s=riwayat" class="active">
                <i class="fas fa-history"></i>Riwayat Absen
            </a>
            <div style="margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px;">
                <a href="logout.php" style="color: #dc3545;">
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
                    <h5 class="mb-0 fw-bold text-dark">Riwayat Absen</h5>
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
        
        <!-- Statistics Cards -->
        <div class="stats-card">
            <div class="row">
                <div class="col-md-4">
                    <div class="stat-item">
                        <div class="stat-number text-primary"><?php echo $stats['total_absen']; ?></div>
                        <div class="stat-label">Total Absen</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-item">
                        <div class="stat-number text-success"><?php echo $stats['total_masuk']; ?></div>
                        <div class="stat-label">Absen Masuk</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-item">
                        <div class="stat-number text-danger"><?php echo $stats['total_pulang']; ?></div>
                        <div class="stat-label">Absen Pulang</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- History Card -->
        <div class="history-card">
            <div class="history-header">
                <h4 class="fw-bold mb-2">
                    <i class="fas fa-clock me-2"></i>
                    Riwayat Absensi
                </h4>
                <p class="mb-0 opacity-75">
                    Menampilkan semua data absensi Anda
                </p>
            </div>
            
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <div class="absen-item">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <div class="fw-bold text-dark"><?php echo htmlspecialchars($row['waktu']); ?></div>
                            </div>
                            
                            <div class="col-md-2">
                                <span class="absen-type <?php echo ($row['tipe'] == 'masuk') ? 'type-masuk' : 'type-pulang'; ?>">
                                    <i class="fas fa-<?php echo ($row['tipe'] == 'masuk') ? 'sign-in-alt' : 'sign-out-alt'; ?> me-1"></i>
                                    <?php echo ucfirst($row['tipe']); ?>
                                </span>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="fw-medium"><?php echo htmlspecialchars($row['kegiatan']); ?></div>
                                <?php if (!empty($row['lokasi'])): ?>
                                <div class="text-muted small">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    <?php echo htmlspecialchars(substr($row['lokasi'], 0, 30)) . '...'; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-md-4">
                                <?php if (!empty($row['keterangan'])): ?>
                                <div class="bg-light p-2 rounded">
                                    <small class="text-muted">
                                        <i class="fas fa-comment me-1"></i>
                                        <?php echo htmlspecialchars($row['keterangan']); ?>
                                    </small>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
                
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-calendar-times"></i>
                    <h5 class="fw-bold mb-2">Belum Ada Data Absensi</h5>
                    <p class="mb-3">Anda belum memiliki riwayat absensi.</p>
                    <a href="?m=presensi" class="btn btn-primary">
                        <i class="fas fa-clock me-2"></i>Mulai Absensi
                    </a>
                </div>
            <?php endif; ?>
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