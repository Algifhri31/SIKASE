<?php
session_start();
require_once("koneksi.php");

// Cek auth
if (!isset($_SESSION['username']) || !isset($_SESSION['level']) || $_SESSION['level'] !== 'admin') {
    header("location: login.php");
    exit();
}

// Current date components
$now_month = date('m');
$now_year = date('Y');

// Get filter values from GET
$filter_month = isset($_GET['bulan']) ? $_GET['bulan'] : $now_month;
$filter_year = isset($_GET['tahun']) ? $_GET['tahun'] : $now_year;
$filter_date = isset($_GET['tanggal']) ? $_GET['tanggal'] : '';

// Build Where Clauses
$where_absen = "";
$where_ket = "";
if (!empty($filter_date)) {
    $where_absen = "WHERE DATE(a.waktu) = '$filter_date'";
    $where_ket = "WHERE DATE(k.waktu) = '$filter_date'";
    $filter_label = date('d F Y', strtotime($filter_date));
}
else {
    $where_absen = "WHERE MONTH(a.waktu) = '$filter_month' AND YEAR(a.waktu) = '$filter_year'";
    $where_ket = "WHERE MONTH(k.waktu) = '$filter_month' AND YEAR(k.waktu) = '$filter_year'";
    $filter_label = date('F Y', strtotime("$filter_year-$filter_month-01"));
}

// Fetch Combined Data using UNION
$sql = "(SELECT a.id_karyawan, a.nama, a.waktu, a.tipe, a.kegiatan, a.keterangan, a.lokasi 
         FROM tb_absen a 
         $where_absen)
        UNION ALL
        (SELECT k.id_karyawan, COALESCE(kar.nama, k.nama) as nama, k.waktu, k.keterangan as tipe, '-' as kegiatan, k.alasan as keterangan, '-' as lokasi 
         FROM tb_keterangan k 
         LEFT JOIN tb_karyawan kar ON k.id_karyawan = kar.id_karyawan
         $where_ket)
        ORDER BY waktu DESC, nama ASC";
$query = mysqli_query($koneksi, $sql);

// Helper for Indonesian Month names
function get_indonesian_months()
{
    return [
        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
        '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
        '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
    ];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Absensi - KSE UINSU</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f4f7fe;
        }
        
        #wrapper {
            display: flex;
            width: 100%;
        }
        
        #page-content-wrapper {
            width: 100%;
            min-height: 100vh;
            padding: 30px;
        }

        /* Responsive toggle */
        @media (max-width: 768px) {
            #sidebar-wrapper {
                margin-left: -280px;
            }
            body.sb-sidenav-toggled #sidebar-wrapper {
                margin-left: 0;
            }
        }
        
        .card-custom {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.03);
            border: none;
            margin-bottom: 25px;
        }
        
        .card-header-custom {
            padding: 20px 25px;
            border-bottom: 1px solid #f8f9fc;
            background: white;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.85rem;
            color: #4e73df;
            margin-bottom: 8px;
        }

        .btn-action {
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .badge-pill-custom {
            padding: 8px 16px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }

        .badge-masuk { background-color: #e3fcef; color: #00875a; }
        .badge-pulang { background-color: #ffebe6; color: #de350b; }
        .badge-izin { background-color: #fffae6; color: #ff8b00; }

        .table thead th {
            background-color: #f8f9fc;
            color: #4e73df;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 1px;
            padding: 15px;
            border: none;
        }

        .table tbody td {
            padding: 15px;
            border-bottom: 1px solid #f8f9fc;
            color: #5a5c69;
            font-size: 0.9rem;
        }

        .map-link {
            display: inline-flex;
            align-items: center;
            color: #4e73df;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .empty-state {
            padding: 60px 20px;
            text-align: center;
            color: #b7b9cc;
        }
    </style>
</head>
<body class="<?php echo isset($_COOKIE['sidebar_toggle']) && $_COOKIE['sidebar_toggle'] == 'true' ? 'sb-sidenav-toggled' : ''; ?>">

<style>
    .fade-in {
        animation: fadeIn 0.5s ease-in-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .stat-card {
        transition: all 0.3s ease;
        border-radius: 12px;
        border: none;
        overflow: hidden;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .icon-box-lg {
        width: 65px;
        height: 65px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    
    .export-card {
        border: 1px solid #e1e8ed !important;
        transition: all 0.3s ease;
        text-decoration: none !important;
        font-family: 'Inter', sans-serif; /* Ensure nice font rendering */
    }
    
    .export-card:hover {
        transform: scale(1.02);
        box-shadow: 0 5px 15px rgba(0,0,0,0.08) !important;
        border-color: #667eea !important;
    }
    
    .export-card-bg {
        position: absolute;
        right: -15px;
        bottom: -15px;
        width: 120px;
        height: 120px;
        border-radius: 50%;
        z-index: 1;
        transition: all 0.5s ease;
    }
    
    .export-card:hover .export-card-bg {
        transform: scale(1.5);
        opacity: 0.2;
    }
    
    .opacity-10 { opacity: 0.1; }
    .icon-box {
        width: 45px;
        height: 45px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }
</style>

<div id="wrapper">
    <!-- Sidebar -->
    <?php include 'includes/sidebar_admin.php'; ?>

    <!-- Page Content -->
    <div id="page-content-wrapper" class="fade-in">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom px-4 py-3 mb-4 shadow-sm">
            <button class="btn btn-light" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <div class="ms-auto d-flex align-items-center">
                <span class="text-muted small me-3 d-none d-md-inline">Halo, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></span>
                <span class="badge bg-primary rounded-pill px-3"><?php echo strtoupper($_SESSION['level']); ?></span>
                <div class="dropdown ms-3">
                    <a class="btn btn-light rounded-circle d-flex align-items-center justify-content-center" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false" style="width: 40px; height: 40px;">
                        <i class="fas fa-user"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="dropdownMenuLink">
                        <li><a class="dropdown-item" href="#" onclick="confirmLogout()"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container-fluid px-4">
            <!-- Header Section -->
            <div class="row align-items-center mb-4 g-3">
                <div class="col-md-6">
                    <h2 class="fw-bold text-dark mb-1">Data Absensi</h2>
                    <p class="text-muted mb-0">Manajemen kehadiran beswan periode <?php echo $filter_label; ?></p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                        <button type="button" class="btn btn-primary btn-action px-4 w-100 w-md-auto shadow-sm" data-bs-toggle="modal" data-bs-target="#exportModal">
                            <i class="fas fa-file-export me-2"></i>Cetak Laporan
                        </button>
                    </div>
                </div>
            </div>

            <!-- Summary Stats -->
                        <?php
$count_total = mysqli_num_rows($query);
$count_masuk = 0;
$count_pulang = 0;
$count_ket = 0;
if ($count_total > 0) {
    mysqli_data_seek($query, 0);
    while ($row = mysqli_fetch_assoc($query)) {
        $tipe = strtolower($row['tipe']);
        if ($tipe == 'masuk') {
            $count_masuk++;
        }
        elseif ($tipe == 'pulang') {
            $count_pulang++;
        }
        elseif (in_array($tipe, ['sakit', 'izin', 'cuti'])) {
            $count_ket++;
        }
    }
    mysqli_data_seek($query, 0); // Reset for table
}
?>
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="card stat-card shadow-sm h-100">
                        <div class="card-body p-3 d-flex align-items-center">
                            <div class="icon-box bg-primary text-white me-2 d-none d-sm-flex">
                                <i class="fas fa-users"></i>
                            </div>
                            <div>
                                <h6 class="text-muted small mb-0">Total</h6>
                                <h4 class="fw-bold mb-0"><?php echo $count_total; ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card stat-card shadow-sm h-100">
                        <div class="card-body p-3 d-flex align-items-center">
                            <div class="icon-box bg-success text-white me-2 d-none d-sm-flex">
                                <i class="fas fa-sign-in-alt"></i>
                            </div>
                            <div>
                                <h6 class="text-muted small mb-0">Masuk</h6>
                                <h4 class="fw-bold mb-0 text-success"><?php echo $count_masuk; ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card stat-card shadow-sm h-100">
                        <div class="card-body p-3 d-flex align-items-center">
                            <div class="icon-box bg-danger text-white me-2 d-none d-sm-flex">
                                <i class="fas fa-sign-out-alt"></i>
                            </div>
                            <div>
                                <h6 class="text-muted small mb-0">Pulang</h6>
                                <h4 class="fw-bold mb-0 text-danger"><?php echo $count_pulang; ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card stat-card shadow-sm h-100">
                        <div class="card-body p-3 d-flex align-items-center">
                            <div class="icon-box bg-warning text-white me-2 d-none d-sm-flex">
                                <i class="fas fa-envelope-open-text"></i>
                            </div>
                            <div>
                                <h6 class="text-muted small mb-0">Izin/Sakit</h6>
                                <h4 class="fw-bold mb-0 text-warning"><?php echo $count_ket; ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Card -->
            <div class="card card-custom">
                <div class="card-body p-4">
                    <form action="" method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">BULAN</label>
                            <select name="bulan" class="form-select shadow-sm border-0 bg-light" onchange="this.form.submit()">
                                <?php foreach (get_indonesian_months() as $val => $label): ?>
                                    <option value="<?php echo $val; ?>" <?php echo($filter_month == $val) ? 'selected' : ''; ?>>
                                        <?php echo $label; ?>
                                    </option>
                                <?php
endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">TAHUN</label>
                            <select name="tahun" class="form-select shadow-sm border-0 bg-light" onchange="this.form.submit()">
                                <?php
for ($y = $now_year; $y >= $now_year - 5; $y--) {
    $selected = ($filter_year == $y) ? 'selected' : '';
    echo "<option value='$y' $selected>$y</option>";
}
?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">CARI TANGGAL SPESIFIK</label>
                            <div class="input-group shadow-sm">
                                <span class="input-group-text border-0 bg-light"><i class="fas fa-calendar-alt text-muted"></i></span>
                                <input type="date" name="tanggal" class="form-control border-0 bg-light" 
                                       value="<?php echo $filter_date; ?>" onchange="this.form.submit()">
                            </div>
                        </div>
                        <div class="col-md-3 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary shadow-sm w-100 btn-action">
                                <i class="fas fa-search me-2"></i>Filter
                            </button>
                            <a href="data_absensi_modern.php" class="btn btn-light shadow-sm btn-action">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Data Table -->
            <div class="card card-custom">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-list me-2 text-primary"></i>
                        Data: <?php echo $filter_label; ?>
                    </h5>
                    <span class="badge bg-secondary"><?php echo mysqli_num_rows($query); ?> Data Ditemukan</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">No</th>
                                    <th>Nama Anggota</th>
                                    <th>Waktu</th>
                                    <th>Tipe</th>
                                    <th>Kegiatan</th>
                                    <th>Keterangan</th>
                                    <th>Lokasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($query) > 0): ?>
                                    <?php
    $no = 1;
    while ($row = mysqli_fetch_array($query)):
        $tipe = strtolower($row['tipe']);
        $tipe_class = 'badge-masuk';
        if ($tipe == 'pulang')
            $tipe_class = 'badge-pulang';
        if (in_array($tipe, ['sakit', 'izin', 'cuti']))
            $tipe_class = 'badge-izin';
?>
                                    <tr>
                                        <td class="ps-4"><?php echo $no++; ?></td>
                                        <td class="fw-bold"><?php echo htmlspecialchars($row['nama']); ?></td>
                                        <td>
                                            <?php echo date('d M Y H:i', strtotime($row['waktu'])); ?>
                                        </td>
                                        <td>
                                            <span class="badge-pill-custom <?php echo $tipe_class; ?>">
                                                <?php echo strtoupper($row['tipe']); ?>
                                            </span>
                                        </td>
                                        <td class="small"><?php echo htmlspecialchars($row['kegiatan'] ?: '-'); ?></td>
                                        <td class="small"><?php echo htmlspecialchars($row['keterangan'] ?: '-'); ?></td>
                                        <td>
                                            <?php if (!empty($row['lokasi']) && $row['lokasi'] !== '-'): ?>
                                                <a href="https://maps.google.com/?q=<?php echo $row['lokasi']; ?>" target="_blank" class="text-decoration-none">
                                                    <i class="fas fa-map-marker-alt text-danger me-1"></i> Lihat Maps
                                                </a>
                                            <?php
        else: ?>
                                                -
                                            <?php
        endif; ?>
                                        </td>
                                    </tr>
                                    <?php
    endwhile; ?>
                                <?php
else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="fas fa-calendar-times fa-3x mb-3"></i>
                                            <p class="mb-0">Tidak ada data absensi pada periode ini.</p>
                                        </td>
                                    </tr>
                                <?php
endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Export Modal -->
    <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white border-0 py-3">
                    <h5 class="modal-title fw-bold text-white" id="exportModalLabel">
                        <i class="fas fa-print me-2"></i>Export Laporan Profesional
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted small mb-4 text-center">Data yang diexport akan mengikuti filter yang sedang aktif: <br><strong><?php echo $filter_label; ?></strong></p>
                    
                    <div class="row g-3">
                        <!-- Excel Option -->
                        <div class="col-12">
                            <a href="export_absensi.php?bulan=<?php echo $filter_month; ?>&tahun=<?php echo $filter_year; ?>&tanggal=<?php echo $filter_date; ?>" 
                               class="export-card btn w-100 text-start p-3 border rounded-3 position-relative overflow-hidden">
                                <div class="d-flex align-items-center position-relative" style="z-index: 2;">
                                    <div class="icon-box-lg bg-success text-white rounded-3 me-3">
                                        <i class="fas fa-file-excel fa-2x"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark">Laporan Microsoft Excel</h6>
                                        <p class="text-muted small mb-0">Format .XLS data mentah terstruktur</p>
                                    </div>
                                    <i class="fas fa-chevron-right ms-auto text-muted"></i>
                                </div>
                                <div class="export-card-bg bg-success opacity-10"></div>
                            </a>
                        </div>
                        
                        <!-- PDF Option -->
                        <div class="col-12">
                            <a href="export_absensi_pdf.php?bulan=<?php echo $filter_month; ?>&tahun=<?php echo $filter_year; ?>&tanggal=<?php echo $filter_date; ?>" 
                               class="export-card btn w-100 text-start p-3 border rounded-3 position-relative overflow-hidden mt-2">
                                <div class="d-flex align-items-center position-relative" style="z-index: 2;">
                                    <div class="icon-box-lg bg-danger text-white rounded-3 me-3">
                                        <i class="fas fa-file-pdf fa-2x"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark">Arsip Laporan PDF (Resmi)</h6>
                                        <p class="text-muted small mb-0">Siap cetak dengan Kop & Tanda Tangan</p>
                                    </div>
                                    <i class="fas fa-chevron-right ms-auto text-muted"></i>
                                </div>
                                <div class="export-card-bg bg-danger opacity-10"></div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3">
                    <button type="button" class="btn btn-secondary px-4 h-100" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-outline-primary px-4 h-100" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>Cetak Layar
                    </button>
                </div>
            </div>
        </div>
    </div>

  <!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Swiper or other scripts if needed -->

</body>
</html>
