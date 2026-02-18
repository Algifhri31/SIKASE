<?php
session_start();

// Cek apakah user sudah login sebagai admin
if (!isset($_SESSION['username']) || !isset($_SESSION['level']) || $_SESSION['level'] !== 'admin') {
    header("location: login.php");
    exit();
}

$username = $_SESSION['username'];
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'admin'; // Default role
$is_super_admin = ($role === 'super_admin');

// Include koneksi database
include 'koneksi.php';

// Ambil statistik data dengan error handling
$stats = [];

try {
    // Total Beswan
    $total_beswan = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_karyawan");
    $stats['total_beswan'] = $total_beswan ? mysqli_fetch_assoc($total_beswan)['total'] : 0;

    // Total Admin
    $total_admin = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_daftar");
    $stats['total_admin'] = $total_admin ? mysqli_fetch_assoc($total_admin)['total'] : 0;

    // Total Absensi
    $total_absen = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_absen");
    $stats['total_absen'] = $total_absen ? mysqli_fetch_assoc($total_absen)['total'] : 0;

    // Absensi Hari Ini
    $today = date('d-m-Y');
    // Adjust query for date format if needed (assuming 'waktu' column might be datetime or string)
    // Using simple LIKE for flexibility as seen in original code
    $absen_today = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_absen WHERE waktu LIKE '%$today%'");
    $stats['absen_today'] = $absen_today ? mysqli_fetch_assoc($absen_today)['total'] : 0;

    // Absensi Masuk vs Pulang
    $absen_masuk = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_absen WHERE tipe = 'masuk'");
    $stats['absen_masuk'] = $absen_masuk ? mysqli_fetch_assoc($absen_masuk)['total'] : 0;

    $absen_pulang = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_absen WHERE tipe = 'pulang'");
    $stats['absen_pulang'] = $absen_pulang ? mysqli_fetch_assoc($absen_pulang)['total'] : 0;

    // Absensi 7 hari terakhir
    $absen_week = [];
    $labels_week = [];
    $data_week = [];

    for ($i = 6; $i >= 0; $i--) {
        $date = date('d-m-Y', strtotime("-$i days"));
        $display_date = date('d M', strtotime("-$i days"));

        $query = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_absen WHERE waktu LIKE '%$date%'");
        $total = $query ? mysqli_fetch_assoc($query)['total'] : 0;

        $absen_week[] = [
            'date' => $date,
            'total' => $total
        ];
        $labels_week[] = $display_date;
        $data_week[] = $total;
    }

    // Absensi terbaru
    $recent_absen = mysqli_query($koneksi, "SELECT tb_absen.*, 
                                           COALESCE(tb_karyawan.nama, tb_absen.nama, 'Tidak Diketahui') as nama_lengkap 
                                           FROM tb_absen 
                                           LEFT JOIN tb_karyawan ON tb_absen.id_karyawan = tb_karyawan.id_karyawan 
                                           ORDER BY tb_absen.id DESC LIMIT 5");
}
catch (Exception $e) {
    // Set default values jika ada error
    $stats = [
        'total_beswan' => 0,
        'total_admin' => 0,
        'total_absen' => 0,
        'absen_today' => 0,
        'absen_masuk' => 0,
        'absen_pulang' => 0
    ];
    $absen_week = [];
    $recent_absen = false;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - KSE UINSU</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            overflow-x: hidden;
        }
        
        #wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        #sidebar-wrapper {
            min-height: 100vh;
            width: 280px;
            margin-left: -280px;
            transition: margin .25s ease-out;
            background-color: #212529;
            flex-shrink: 0;
        }

        #sidebar-wrapper .sidebar-heading {
            padding: 1.5rem 1.25rem;
            font-size: 1.5rem;
            font-weight: bold;
            color: #fff;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        #sidebar-wrapper .list-group {
            width: 280px;
        }

        #sidebar-wrapper .list-group-item {
            background-color: transparent;
            color: rgba(255, 255, 255, 0.75);
            border: none;
            padding: 1rem 1.25rem;
            font-size: 1rem;
            transition: all 0.2s;
        }

        #sidebar-wrapper .list-group-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: #fff;
            padding-left: 1.5rem;
        }

        #sidebar-wrapper .list-group-item.active {
            background-color: #0d6efd;
            color: #fff;
            font-weight: 600;
        }

        #sidebar-wrapper .list-group-item i {
            width: 25px;
            margin-right: 10px;
            text-align: center;
        }

        #page-content-wrapper {
            width: 100%;
            flex-grow: 1;
        }

        body.sb-sidenav-toggled #sidebar-wrapper {
            margin-left: 0;
        }

        .stat-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
            transition: transform 0.2s;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            margin-bottom: 15px;
        }

        .bg-gradient-primary { background: linear-gradient(45deg, #4e73df, #224abe); }
        .bg-gradient-success { background: linear-gradient(45deg, #1cc88a, #13855c); }
        .bg-gradient-info { background: linear-gradient(45deg, #36b9cc, #258391); }
        .bg-gradient-warning { background: linear-gradient(45deg, #f6c23e, #dda20a); }
        .bg-gradient-danger { background: linear-gradient(45deg, #e74a3b, #be2617); }

        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,.08);
            background-color: #fff !important;
        }

        .welcome-badge {
            background: linear-gradient(45deg, #4e73df, #224abe);
            color: white;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        @media (min-width: 768px) {
            #sidebar-wrapper {
                margin-left: 0;
            }

            body.sb-sidenav-toggled #sidebar-wrapper {
                margin-left: -280px;
            }
        }
    </style>
</head>
<body class="<?php echo isset($_COOKIE['sidebar_toggle']) && $_COOKIE['sidebar_toggle'] == 'true' ? 'sb-sidenav-toggled' : ''; ?>">

<div class="d-flex" id="wrapper">
    <!-- Sidebar -->
    <!-- Sidebar -->
    <?php include 'includes/sidebar_admin.php'; ?>

    <!-- Page Content -->
    <div id="page-content-wrapper">
        <nav class="navbar navbar-expand-lg navbar-light border-bottom px-4 py-3">
            <button class="btn btn-light" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            
            <div class="ms-auto d-flex align-items-center">
                <div class="dropdown">
                    <a class="nav-link dropdown-toggle text-dark fw-bold" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle fa-lg me-2 text-primary"></i>
                        <?php echo htmlspecialchars($username); ?>
                        <span class="badge bg-<?php echo $is_super_admin ? 'danger' : 'secondary'; ?> ms-2">
                            <?php echo $is_super_admin ? 'SUPER ADMIN' : 'Admin'; ?>
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownMenuLink">
                        <?php if ($is_super_admin): ?>
                        <li><a class="dropdown-item" href="datauser.php"><i class="fas fa-cog me-2"></i>Pengaturan</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <?php
endif; ?>
                        <li><a class="dropdown-item text-danger" href="#" onclick="confirmLogout(); return false;"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container-fluid px-4 py-4">
            
            <div class="welcome-badge shadow-sm">
                <div>
                    <h2 class="mb-1">Selamat Datang, <?php echo htmlspecialchars($username); ?>! 👋</h2>
                    <p class="mb-0 opacity-75">
                        <?php echo $is_super_admin ? 'Anda memiliki akses penuh sebagai Super Admin.' : 'Kelola data absensi harian dengan mudah.'; ?>
                    </p>
                </div>
                <div class="d-none d-md-block">
                    <i class="fas fa-chart-line fa-4x opacity-50"></i>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row g-4 mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total Beswan</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['total_beswan']; ?></div>
                                </div>
                                <div class="col-auto">
                                    <div class="stat-icon bg-gradient-primary">
                                        <i class="fas fa-users"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Absen Hari Ini</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['absen_today']; ?></div>
                                </div>
                                <div class="col-auto">
                                    <div class="stat-icon bg-gradient-success">
                                        <i class="fas fa-calendar-check"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Absen Masuk</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['absen_masuk']; ?></div>
                                </div>
                                <div class="col-auto">
                                    <div class="stat-icon bg-gradient-info">
                                        <i class="fas fa-sign-in-alt"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Absen Pulang</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['absen_pulang']; ?></div>
                                </div>
                                <div class="col-auto">
                                    <div class="stat-icon bg-gradient-warning">
                                        <i class="fas fa-sign-out-alt"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity & Quick Actions -->
            <div class="row g-4">
                <!-- Recent Absensi -->
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-history me-2"></i>Absensi Terbaru
                            </h6>
                            <a href="data_absensi_modern.php" class="btn btn-sm btn-primary">Lihat Semua</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4">Nama</th>
                                            <th>Waktu</th>
                                            <th>Tipe</th>
                                            <th>Kegiatan</th>
                                            <th>Keterangan</th>
                                            <th>Lokasi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($recent_absen && mysqli_num_rows($recent_absen) > 0): ?>
                                            <?php while ($row = mysqli_fetch_assoc($recent_absen)): ?>
                                            <tr>
                                                <td class="ps-4 fw-bold"><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
                                                <td>
                                                    <span class="badge bg-light text-dark border">
                                                        <?php echo date('d M Y H:i', strtotime($row['waktu'])); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($row['tipe'] == 'masuk'): ?>
                                                        <span class="badge bg-success">Masuk</span>
                                                    <?php
        else: ?>
                                                        <span class="badge bg-warning text-dark">Pulang</span>
                                                    <?php
        endif; ?>
                                                </td>
                                                <td class="small"><?php echo htmlspecialchars($row['kegiatan'] ?: '-'); ?></td>
                                                <td class="small text-truncate" style="max-width: 100px;"><?php echo htmlspecialchars($row['keterangan'] ?: '-'); ?></td>
                                                <td class="small text-muted">
                                                    <i class="fas fa-map-marker-alt me-1"></i>
                                                    <?php echo htmlspecialchars(substr($row['lokasi'], 0, 30)) . (strlen($row['lokasi']) > 30 ? '...' : ''); ?>
                                                </td>
                                            </tr>
                                            <?php
    endwhile; ?>
                                        <?php
else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center py-4 text-muted">Belum ada data absensi</td>
                                            </tr>
                                        <?php
endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions (Super Admin Only) -->
                <?php if ($is_super_admin): ?>
                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 rounded-3 mb-4">
                        <div class="card-header bg-white py-3">
                            <h6 class="m-0 font-weight-bold text-danger">
                                <i class="fas fa-bolt me-2"></i>Quick Actions
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="data_admin_modern.php" class="btn btn-outline-primary text-start p-3">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary text-white rounded-circle p-2 me-3">
                                            <i class="fas fa-user-shield"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">Kelola Admin</div>
                                            <div class="small text-muted">Manajemen akun administrator</div>
                                        </div>
                                    </div>
                                </a>
                                
                                <a href="admin/data_beswan_modern.php" class="btn btn-outline-success text-start p-3">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-success text-white rounded-circle p-2 me-3">
                                            <i class="fas fa-user-graduate"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">Data Beswan</div>
                                            <div class="small text-muted">Kelola data penerima beasiswa</div>
                                        </div>
                                    </div>
                                </a>

                                <a href="export.php" class="btn btn-outline-info text-start p-3">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-info text-white rounded-circle p-2 me-3">
                                            <i class="fas fa-file-download"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">Export Laporan</div>
                                            <div class="small text-muted">Unduh data absensi</div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
