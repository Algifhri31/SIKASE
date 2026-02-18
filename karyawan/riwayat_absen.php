<?php
// Session sudah di-start di index.php
$page_title = "Riwayat Absen";

// Koneksi sudah di-include di sesi_karyawan.php
if (!isset($koneksi)) {
    include '../koneksi.php';
}

$id = $_SESSION['idsi'];
$nama_user = $_SESSION['namasi'] ?? 'User';

// Pagination
$limit = 15;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Filter
$filter_bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('Y-m');
$filter_tipe = isset($_GET['tipe']) ? $_GET['tipe'] : '';

// Build query dengan perbaikan - escape untuk keamanan
$id_escaped = mysqli_real_escape_string($koneksi, $id);
$where_conditions = ["id_karyawan = '$id_escaped'"];

if (!empty($filter_bulan)) {
    $filter_bulan_escaped = mysqli_real_escape_string($koneksi, $filter_bulan);
    // Gunakan LIKE untuk format tanggal dd-mm-YYYY HH:ii:ss
    $where_conditions[] = "waktu LIKE '%" . substr($filter_bulan_escaped, 5, 2) . "-" . substr($filter_bulan_escaped, 0, 4) . "%'";
}

if (!empty($filter_tipe)) {
    $filter_tipe_escaped = mysqli_real_escape_string($koneksi, $filter_tipe);
    $where_conditions[] = "tipe = '$filter_tipe_escaped'";
}

$where_clause = implode(" AND ", $where_conditions);

// Count total records
$count_sql = "SELECT COUNT(*) as total FROM tb_absen WHERE $where_clause";
$count_result = mysqli_query($koneksi, $count_sql);
$total_records = $count_result ? mysqli_fetch_assoc($count_result)['total'] : 0;
$total_pages = ceil($total_records / $limit);

// Get absen data
$sql = "SELECT * FROM tb_absen WHERE $where_clause ORDER BY id DESC LIMIT $limit OFFSET $offset";
$result = mysqli_query($koneksi, $sql);

// Get statistics
$stats_sql = "SELECT 
    COUNT(*) as total_absen,
    SUM(CASE WHEN tipe = 'masuk' THEN 1 ELSE 0 END) as total_masuk,
    SUM(CASE WHEN tipe = 'pulang' THEN 1 ELSE 0 END) as total_pulang
    FROM tb_absen WHERE id_karyawan = '$id_escaped'";
$stats_result = mysqli_query($koneksi, $stats_sql);
$stats = mysqli_fetch_assoc($stats_result);

// Hitung hari hadir (distinct tanggal)
$hari_sql = "SELECT COUNT(DISTINCT DATE(STR_TO_DATE(waktu, '%d-%m-%Y %H:%i:%s'))) as hari_hadir 
             FROM tb_absen WHERE id_karyawan = '$id_escaped'";
$hari_result = mysqli_query($koneksi, $hari_sql);
$hari_data = mysqli_fetch_assoc($hari_result);
$stats['hari_hadir'] = $hari_data['hari_hadir'] ?? 0;
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
            transition: all 0.3s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.12);
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
        
        .filter-card {
            background: #fff;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 25px;
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
        
        .absen-date {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .absen-time {
            color: #6c757d;
            font-size: 14px;
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
        
        .absen-location {
            color: #6c757d;
            font-size: 13px;
            margin-top: 5px;
        }
        
        .absen-keterangan {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 8px;
            margin-top: 10px;
            font-size: 14px;
            color: #495057;
        }
        
        .pagination-custom {
            margin-top: 30px;
        }
        
        .pagination-custom .page-link {
            border: none;
            padding: 10px 15px;
            margin: 0 3px;
            border-radius: 8px;
            color: #667eea;
            font-weight: 500;
        }
        
        .pagination-custom .page-item.active .page-link {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
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
            
            .stats-card {
                padding: 20px;
            }
            
            .stat-item {
                padding: 10px;
            }
            
            .absen-item {
                padding: 15px 20px;
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
                <a href="../logout.php" style="color: #dc3545;" onclick="return confirm('Yakin ingin logout?')">
                    <i class="fas fa-sign-out-alt"></i>Logout
                </a>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-dark mb-0">Riwayat Absen</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="?m=awal" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active">Riwayat Absen</li>
                </ol>
            </nav>
        </div>
        
        <!-- Statistics Cards -->
        <div class="stats-card">
            <div class="row">
                <div class="col-md-3">
                    <div class="stat-item">
                        <div class="stat-number text-primary"><?php echo $stats['total_absen']; ?></div>
                        <div class="stat-label">Total Absen</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <div class="stat-number text-success"><?php echo $stats['total_masuk']; ?></div>
                        <div class="stat-label">Absen Masuk</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <div class="stat-number text-danger"><?php echo $stats['total_pulang']; ?></div>
                        <div class="stat-label">Absen Pulang</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <div class="stat-number text-info"><?php echo $stats['hari_hadir']; ?></div>
                        <div class="stat-label">Hari Hadir</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Filter -->
        <div class="filter-card">
            <h5 class="fw-bold mb-3">
                <i class="fas fa-filter text-primary me-2"></i>
                Filter Riwayat
            </h5>
            <form method="GET" class="row g-3">
                <input type="hidden" name="m" value="karyawan">
                <input type="hidden" name="s" value="riwayat">
                
                <div class="col-md-4">
                    <label class="form-label">Bulan</label>
                    <input type="month" name="bulan" class="form-control" value="<?php echo $filter_bulan; ?>">
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Tipe</label>
                    <select name="tipe" class="form-select">
                        <option value="">Semua Tipe</option>
                        <option value="masuk" <?php echo ($filter_tipe == 'masuk') ? 'selected' : ''; ?>>Masuk</option>
                        <option value="pulang" <?php echo ($filter_tipe == 'pulang') ? 'selected' : ''; ?>>Pulang</option>
                    </select>
                </div>
                
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-1"></i>Filter
                    </button>
                    <a href="?m=karyawan&s=riwayat" class="btn btn-outline-secondary">
                        <i class="fas fa-refresh me-1"></i>Reset
                    </a>
                </div>
            </form>
        </div>
        
        <!-- History Card -->
        <div class="history-card">
            <div class="history-header">
                <h4 class="fw-bold mb-2">
                    <i class="fas fa-clock me-2"></i>
                    Riwayat Absensi
                </h4>
                <p class="mb-0 opacity-75">
                    Menampilkan <?php echo mysqli_num_rows($result); ?> dari <?php echo $total_records; ?> data absensi
                </p>
            </div>
            
            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <?php
                    // Parse waktu format: dd-mm-YYYY HH:ii:ss
                    $waktu_parts = explode(' ', $row['waktu']);
                    $tanggal = $waktu_parts[0] ?? date('d-m-Y');
                    $jam = $waktu_parts[1] ?? date('H:i:s');
                    
                    // Convert tanggal ke format yang lebih readable
                    $tgl_parts = explode('-', $tanggal);
                    if (count($tgl_parts) == 3) {
                        $hari_array = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                        $timestamp = strtotime($tgl_parts[2] . '-' . $tgl_parts[1] . '-' . $tgl_parts[0]);
                        $hari = $hari_array[date('w', $timestamp)];
                        $tanggal_formatted = $hari . ', ' . $tanggal;
                    } else {
                        $tanggal_formatted = $tanggal;
                    }
                    ?>
                    <div class="absen-item">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <div class="absen-date"><?php echo $tanggal_formatted; ?></div>
                                <div class="absen-time">
                                    <i class="fas fa-clock me-1"></i>
                                    <?php echo $jam; ?>
                                </div>
                            </div>
                            
                            <div class="col-md-2">
                                <span class="absen-type <?php echo ($row['tipe'] == 'masuk') ? 'type-masuk' : 'type-pulang'; ?>">
                                    <i class="fas fa-<?php echo ($row['tipe'] == 'masuk') ? 'sign-in-alt' : 'sign-out-alt'; ?> me-1"></i>
                                    <?php echo ucfirst($row['tipe']); ?>
                                </span>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="fw-medium"><?php echo htmlspecialchars($row['kegiatan'] ?? 'Tidak ada kegiatan'); ?></div>
                                <?php if (!empty($row['lokasi'])): ?>
                                <div class="absen-location">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    <?php echo htmlspecialchars($row['lokasi']); ?>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-md-3">
                                <?php if (!empty($row['keterangan'])): ?>
                                <div class="absen-keterangan">
                                    <i class="fas fa-comment me-1"></i>
                                    <?php echo htmlspecialchars($row['keterangan']); ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
                
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <div class="d-flex justify-content-center">
                    <nav class="pagination-custom">
                        <ul class="pagination">
                            <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?m=karyawan&s=riwayat&page=<?php echo ($page-1); ?>&bulan=<?php echo $filter_bulan; ?>&tipe=<?php echo $filter_tipe; ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $page-2); $i <= min($total_pages, $page+2); $i++): ?>
                            <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                <a class="page-link" href="?m=karyawan&s=riwayat&page=<?php echo $i; ?>&bulan=<?php echo $filter_bulan; ?>&tipe=<?php echo $filter_tipe; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                            <?php endfor; ?>
                            
                            <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?m=karyawan&s=riwayat&page=<?php echo ($page+1); ?>&bulan=<?php echo $filter_bulan; ?>&tipe=<?php echo $filter_tipe; ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
                <?php endif; ?>
                
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-calendar-times"></i>
                    <h5 class="fw-bold mb-2">Belum Ada Data Absensi</h5>
                    <p class="mb-3">Anda belum memiliki riwayat absensi untuk filter yang dipilih.</p>
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
        // Auto refresh setiap 5 menit
        setTimeout(function() {
            location.reload();
        }, 300000);
        
        // Smooth scroll untuk pagination
        document.querySelectorAll('.pagination a').forEach(function(link) {
            link.addEventListener('click', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>