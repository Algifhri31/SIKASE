<?php
session_start();

// Cek apakah user sudah login sebagai admin
if (!isset($_SESSION['username']) || !isset($_SESSION['level']) || $_SESSION['level'] !== 'admin') {
    header("location: ../login.php");
    exit();
}

include '../koneksi.php';

// Ambil parameter filter
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('Y-m');
$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : '';
$id_karyawan = isset($_GET['id_karyawan']) ? $_GET['id_karyawan'] : '';

// Query untuk mengambil data karyawan
$sql_karyawan = "SELECT id_karyawan, nama FROM tb_karyawan ORDER BY nama";
$result_karyawan = mysqli_query($koneksi, $sql_karyawan);

// Query untuk data absensi berdasarkan filter
$where_conditions = [];
if (!empty($bulan)) {
    $where_conditions[] = "DATE_FORMAT(waktu, '%Y-%m') = '$bulan'";
}
if (!empty($tanggal)) {
    $where_conditions[] = "DATE(waktu) = '$tanggal'";
}
if (!empty($id_karyawan)) {
    $where_conditions[] = "tb_absen.id_karyawan = '$id_karyawan'";
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

$sql_absen = "SELECT tb_absen.*, tb_karyawan.nama as nama_karyawan 
              FROM tb_absen 
              LEFT JOIN tb_karyawan ON tb_absen.id_karyawan = tb_karyawan.id_karyawan 
              $where_clause 
              ORDER BY tb_absen.waktu DESC";
$result_absen = mysqli_query($koneksi, $sql_absen);

// Export Excel jika diminta
if (isset($_GET['export']) && $_GET['export'] == 'excel') {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Data_Absensi_' . ($tanggal ? $tanggal : $bulan) . '.xls"');
    header('Cache-Control: max-age=0');
    
    echo "<table border='1'>";
    echo "<tr><th>No</th><th>ID Karyawan</th><th>Nama</th><th>Waktu</th><th>Tipe</th><th>Kegiatan</th><th>Keterangan</th><th>Lokasi</th></tr>";
    
    $no = 1;
    mysqli_data_seek($result_absen, 0);
    while ($row = mysqli_fetch_assoc($result_absen)) {
        echo "<tr>";
        echo "<td>" . $no++ . "</td>";
        echo "<td>" . $row['id_karyawan'] . "</td>";
        echo "<td>" . ($row['nama_karyawan'] ?: $row['nama']) . "</td>";
        echo "<td>" . $row['waktu'] . "</td>";
        echo "<td>" . ucfirst($row['tipe']) . "</td>";
        echo "<td>" . $row['kegiatan'] . "</td>";
        echo "<td>" . $row['keterangan'] . "</td>";
        echo "<td>" . $row['lokasi'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Absensi - Admin KSE</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f8f9fa;
        }
        
        .main-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 0;
            margin-bottom: 30px;
        }
        
        .filter-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            padding: 25px;
            margin-bottom: 25px;
        }
        
        .data-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            padding: 25px;
        }
        
        .btn-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
        }
        
        .btn-gradient:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
            color: white;
        }
        
        .stats-row {
            margin-bottom: 20px;
        }
        
        .stat-box {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            border-left: 4px solid #667eea;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 0.9rem;
            color: #6c757d;
            font-weight: 500;
        }
        
        .table th {
            background: #f8f9fa;
            border-top: none;
            font-weight: 600;
            color: #495057;
        }
        
        .badge-masuk {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        
        .badge-pulang {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="main-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2 class="mb-0"><i class="fas fa-chart-line me-3"></i>Data Absensi Beswan</h2>
                    <p class="mb-0 opacity-75">Kelola dan pantau data kehadiran beswan KSE</p>
                </div>
                <div class="col-md-6 text-end">
                    <a href="../admin_dashboard_modern.php" class="btn btn-light">
                        <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Filter Section -->
        <div class="filter-card">
            <h5 class="mb-4"><i class="fas fa-filter me-2"></i>Filter Data Absensi</h5>
            
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Pilih Bulan</label>
                    <input type="month" class="form-control" name="bulan" value="<?php echo $bulan; ?>">
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Pilih Tanggal Spesifik</label>
                    <input type="date" class="form-control" name="tanggal" value="<?php echo $tanggal; ?>">
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Pilih Karyawan</label>
                    <select class="form-select" name="id_karyawan">
                        <option value="">Semua Karyawan</option>
                        <?php 
                        mysqli_data_seek($result_karyawan, 0);
                        while ($karyawan = mysqli_fetch_assoc($result_karyawan)) : 
                        ?>
                            <option value="<?php echo $karyawan['id_karyawan']; ?>" 
                                    <?php echo ($id_karyawan == $karyawan['id_karyawan']) ? 'selected' : ''; ?>>
                                <?php echo $karyawan['id_karyawan'] . ' - ' . $karyawan['nama']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-gradient me-2">
                        <i class="fas fa-search me-1"></i>Filter
                    </button>
                    <a href="?" class="btn btn-outline-secondary">
                        <i class="fas fa-refresh me-1"></i>Reset
                    </a>
                </div>
            </form>
            
            <?php if (!empty($bulan) || !empty($tanggal)) : ?>
            <div class="mt-3">
                <a href="?<?php echo http_build_query(array_merge($_GET, ['export' => 'excel'])); ?>" 
                   class="btn btn-success">
                    <i class="fas fa-file-excel me-2"></i>Export Excel
                </a>
            </div>
            <?php endif; ?>
        </div>

        <!-- Statistics -->
        <?php
        // Hitung statistik
        $total_records = mysqli_num_rows($result_absen);
        $total_masuk = 0;
        $total_pulang = 0;
        
        mysqli_data_seek($result_absen, 0);
        while ($row = mysqli_fetch_assoc($result_absen)) {
            if ($row['tipe'] == 'masuk') $total_masuk++;
            if ($row['tipe'] == 'pulang') $total_pulang++;
        }
        ?>
        
        <div class="stats-row">
            <div class="row">
                <div class="col-md-3">
                    <div class="stat-box">
                        <div class="stat-number"><?php echo $total_records; ?></div>
                        <div class="stat-label">Total Record</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-box">
                        <div class="stat-number"><?php echo $total_masuk; ?></div>
                        <div class="stat-label">Absen Masuk</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-box">
                        <div class="stat-number"><?php echo $total_pulang; ?></div>
                        <div class="stat-label">Absen Pulang</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-box">
                        <div class="stat-number">
                            <?php echo !empty($tanggal) ? date('d', strtotime($tanggal)) : date('d'); ?>
                        </div>
                        <div class="stat-label">
                            <?php echo !empty($tanggal) ? date('M Y', strtotime($tanggal)) : date('M Y'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="data-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0">
                    <i class="fas fa-table me-2"></i>
                    Data Absensi 
                    <?php if (!empty($tanggal)) : ?>
                        - <?php echo date('d F Y', strtotime($tanggal)); ?>
                    <?php elseif (!empty($bulan)) : ?>
                        - <?php echo date('F Y', strtotime($bulan . '-01')); ?>
                    <?php endif; ?>
                </h5>
                <span class="badge bg-primary"><?php echo $total_records; ?> Record</span>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover" id="dataTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID Karyawan</th>
                            <th>Nama</th>
                            <th>Waktu</th>
                            <th>Tipe</th>
                            <th>Kegiatan</th>
                            <th>Keterangan</th>
                            <th>Lokasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        mysqli_data_seek($result_absen, 0);
                        while ($row = mysqli_fetch_assoc($result_absen)) : 
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><strong><?php echo $row['id_karyawan']; ?></strong></td>
                            <td><?php echo $row['nama_karyawan'] ?: $row['nama']; ?></td>
                            <td>
                                <small class="text-muted">
                                    <?php echo date('d/m/Y H:i', strtotime($row['waktu'])); ?>
                                </small>
                            </td>
                            <td>
                                <span class="badge <?php echo $row['tipe'] == 'masuk' ? 'badge-masuk' : 'badge-pulang'; ?>">
                                    <?php echo ucfirst($row['tipe']); ?>
                                </span>
                            </td>
                            <td>
                                <small><?php echo substr($row['kegiatan'], 0, 50) . (strlen($row['kegiatan']) > 50 ? '...' : ''); ?></small>
                            </td>
                            <td>
                                <small><?php echo substr($row['keterangan'], 0, 50) . (strlen($row['keterangan']) > 50 ? '...' : ''); ?></small>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <?php 
                                    if (!empty($row['lokasi'])) {
                                        $coords = explode(', ', $row['lokasi']);
                                        if (count($coords) == 2) {
                                            echo "📍 " . number_format($coords[0], 4) . ", " . number_format($coords[1], 4);
                                        } else {
                                            echo $row['lokasi'];
                                        }
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </small>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json"
                },
                "pageLength": 25,
                "order": [[ 3, "desc" ]],
                "columnDefs": [
                    { "orderable": false, "targets": 0 }
                ]
            });
        });
    </script>
</body>
</html>