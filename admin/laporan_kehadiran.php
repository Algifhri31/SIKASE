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
$id_karyawan = isset($_GET['id_karyawan']) ? $_GET['id_karyawan'] : '';

// Query untuk mengambil data karyawan
$sql_karyawan = "SELECT id_karyawan, nama FROM tb_karyawan ORDER BY nama";
$result_karyawan = mysqli_query($koneksi, $sql_karyawan);

// Generate laporan
$laporan_data = [];
if (!empty($bulan)) {
    $where_clause = "DATE_FORMAT(waktu, '%Y-%m') = '$bulan'";
    if (!empty($id_karyawan)) {
        $where_clause .= " AND tb_absen.id_karyawan = '$id_karyawan'";
    }
    
    // Ambil semua karyawan atau karyawan tertentu
    $karyawan_filter = !empty($id_karyawan) ? "WHERE id_karyawan = '$id_karyawan'" : "";
    $sql_all_karyawan = "SELECT * FROM tb_karyawan $karyawan_filter ORDER BY nama";
    $result_all_karyawan = mysqli_query($koneksi, $sql_all_karyawan);
    
    while ($karyawan = mysqli_fetch_assoc($result_all_karyawan)) {
        $id = $karyawan['id_karyawan'];
        
        // Hitung total masuk
        $sql_masuk = "SELECT COUNT(*) as total FROM tb_absen WHERE id_karyawan = '$id' AND $where_clause AND tipe = 'masuk'";
        $result_masuk = mysqli_query($koneksi, $sql_masuk);
        $total_masuk = $result_masuk ? mysqli_fetch_assoc($result_masuk)['total'] : 0;
        
        // Hitung total pulang
        $sql_pulang = "SELECT COUNT(*) as total FROM tb_absen WHERE id_karyawan = '$id' AND $where_clause AND tipe = 'pulang'";
        $result_pulang = mysqli_query($koneksi, $sql_pulang);
        $total_pulang = $result_pulang ? mysqli_fetch_assoc($result_pulang)['total'] : 0;
        
        // Hitung hari kerja dalam bulan (asumsi 22 hari kerja)
        $hari_kerja = 22;
        $persentase_kehadiran = $total_masuk > 0 ? round(($total_masuk / $hari_kerja) * 100, 1) : 0;
        
        // Ambil absen pertama dan terakhir
        $sql_first_last = "SELECT 
                            MIN(CASE WHEN tipe = 'masuk' THEN waktu END) as first_in,
                            MAX(CASE WHEN tipe = 'pulang' THEN waktu END) as last_out
                           FROM tb_absen 
                           WHERE id_karyawan = '$id' AND $where_clause";
        $result_first_last = mysqli_query($koneksi, $sql_first_last);
        $first_last = $result_first_last ? mysqli_fetch_assoc($result_first_last) : null;
        
        $laporan_data[] = [
            'id_karyawan' => $karyawan['id_karyawan'],
            'nama' => $karyawan['nama'],
            'jabatan' => $karyawan['jabatan'],
            'total_masuk' => $total_masuk,
            'total_pulang' => $total_pulang,
            'persentase_kehadiran' => $persentase_kehadiran,
            'first_in' => $first_last['first_in'],
            'last_out' => $first_last['last_out']
        ];
    }
}

// Export Excel jika diminta
if (isset($_GET['export']) && $_GET['export'] == 'excel' && !empty($laporan_data)) {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Laporan_Kehadiran_' . $bulan . '.xls"');
    header('Cache-Control: max-age=0');
    
    echo "<table border='1'>";
    echo "<tr><th colspan='8' style='text-align: center; font-weight: bold;'>LAPORAN KEHADIRAN BESWAN KSE</th></tr>";
    echo "<tr><th colspan='8' style='text-align: center;'>Periode: " . date('F Y', strtotime($bulan . '-01')) . "</th></tr>";
    echo "<tr><th>No</th><th>ID Beswan</th><th>Nama</th><th>Jabatan</th><th>Total Masuk</th><th>Total Pulang</th><th>Kehadiran (%)</th><th>Status</th></tr>";
    
    $no = 1;
    foreach ($laporan_data as $row) {
        $status = $row['persentase_kehadiran'] >= 80 ? 'Baik' : ($row['persentase_kehadiran'] >= 60 ? 'Cukup' : 'Kurang');
        echo "<tr>";
        echo "<td>" . $no++ . "</td>";
        echo "<td>" . $row['id_karyawan'] . "</td>";
        echo "<td>" . $row['nama'] . "</td>";
        echo "<td>" . $row['jabatan'] . "</td>";
        echo "<td>" . $row['total_masuk'] . "</td>";
        echo "<td>" . $row['total_pulang'] . "</td>";
        echo "<td>" . $row['persentase_kehadiran'] . "%</td>";
        echo "<td>" . $status . "</td>";
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
    <title>Laporan Kehadiran - Admin KSE</title>
    
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
        
        .filter-card, .report-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            padding: 25px;
            margin-bottom: 25px;
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
        
        .summary-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            border-left: 4px solid;
            margin-bottom: 20px;
        }
        
        .summary-card.total { border-left-color: #667eea; }
        .summary-card.hadir { border-left-color: #10b981; }
        .summary-card.tidak-hadir { border-left-color: #f59e0b; }
        .summary-card.rata-rata { border-left-color: #8b5cf6; }
        
        .summary-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .summary-label {
            color: #6c757d;
            font-weight: 500;
        }
        
        .table th {
            background: #f8f9fa;
            border-top: none;
            font-weight: 600;
            color: #495057;
        }
        
        .badge-baik { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        .badge-cukup { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
        .badge-kurang { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
        
        .progress-custom {
            height: 8px;
            border-radius: 4px;
        }
        
        @media print {
            .no-print { display: none !important; }
            .main-header { background: #333 !important; }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="main-header no-print">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2 class="mb-0"><i class="fas fa-file-alt me-3"></i>Laporan Kehadiran</h2>
                    <p class="mb-0 opacity-75">Generate dan analisis laporan kehadiran beswan</p>
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
        <div class="filter-card no-print">
            <h5 class="mb-4"><i class="fas fa-filter me-2"></i>Generate Laporan</h5>
            
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Pilih Bulan</label>
                    <input type="month" class="form-control" name="bulan" value="<?php echo $bulan; ?>" required>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Pilih Beswan (Opsional)</label>
                    <select class="form-select" name="id_karyawan">
                        <option value="">Semua Beswan</option>
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
                
                <div class="col-md-4 d-flex align-items-end">
                    <div class="d-grid gap-2 w-100">
                        <button type="submit" class="btn btn-gradient">
                            <i class="fas fa-chart-bar me-1"></i>Generate Laporan
                        </button>
                        <?php if (!empty($laporan_data)) : ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['export' => 'excel'])); ?>" 
                           class="btn btn-success">
                            <i class="fas fa-file-excel me-1"></i>Export Excel
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>

        <?php if (!empty($laporan_data)) : ?>
        <!-- Summary Statistics -->
        <?php
        $total_beswan = count($laporan_data);
        $total_hadir = array_sum(array_column($laporan_data, 'total_masuk'));
        $rata_rata_kehadiran = $total_beswan > 0 ? round(array_sum(array_column($laporan_data, 'persentase_kehadiran')) / $total_beswan, 1) : 0;
        $beswan_aktif = count(array_filter($laporan_data, function($item) { return $item['total_masuk'] > 0; }));
        ?>
        
        <div class="report-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0">
                    <i class="fas fa-chart-pie me-2"></i>
                    Ringkasan Laporan - <?php echo date('F Y', strtotime($bulan . '-01')); ?>
                </h5>
                <button onclick="window.print()" class="btn btn-outline-primary no-print">
                    <i class="fas fa-print me-2"></i>Print Laporan
                </button>
            </div>
            
            <div class="row">
                <div class="col-md-3">
                    <div class="summary-card total">
                        <div class="summary-number" style="color: #667eea;"><?php echo $total_beswan; ?></div>
                        <div class="summary-label">Total Beswan</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card hadir">
                        <div class="summary-number" style="color: #10b981;"><?php echo $beswan_aktif; ?></div>
                        <div class="summary-label">Beswan Aktif</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card tidak-hadir">
                        <div class="summary-number" style="color: #f59e0b;"><?php echo $total_hadir; ?></div>
                        <div class="summary-label">Total Kehadiran</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card rata-rata">
                        <div class="summary-number" style="color: #8b5cf6;"><?php echo $rata_rata_kehadiran; ?>%</div>
                        <div class="summary-label">Rata-rata Kehadiran</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Report -->
        <div class="report-card">
            <h5 class="mb-4"><i class="fas fa-table me-2"></i>Detail Laporan Kehadiran</h5>
            
            <div class="table-responsive">
                <table class="table table-hover" id="reportTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID Beswan</th>
                            <th>Nama</th>
                            <th>Jabatan</th>
                            <th>Masuk</th>
                            <th>Pulang</th>
                            <th>Kehadiran</th>
                            <th>Progress</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        foreach ($laporan_data as $row) : 
                            $status = $row['persentase_kehadiran'] >= 80 ? 'Baik' : ($row['persentase_kehadiran'] >= 60 ? 'Cukup' : 'Kurang');
                            $badge_class = $row['persentase_kehadiran'] >= 80 ? 'badge-baik' : ($row['persentase_kehadiran'] >= 60 ? 'badge-cukup' : 'badge-kurang');
                            $progress_color = $row['persentase_kehadiran'] >= 80 ? 'success' : ($row['persentase_kehadiran'] >= 60 ? 'warning' : 'danger');
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><strong><?php echo $row['id_karyawan']; ?></strong></td>
                            <td><?php echo $row['nama']; ?></td>
                            <td>
                                <span class="badge bg-info"><?php echo $row['jabatan'] ?: '-'; ?></span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary"><?php echo $row['total_masuk']; ?></span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary"><?php echo $row['total_pulang']; ?></span>
                            </td>
                            <td class="text-center">
                                <strong><?php echo $row['persentase_kehadiran']; ?>%</strong>
                            </td>
                            <td>
                                <div class="progress progress-custom">
                                    <div class="progress-bar bg-<?php echo $progress_color; ?>" 
                                         style="width: <?php echo $row['persentase_kehadiran']; ?>%"></div>
                                </div>
                            </td>
                            <td>
                                <span class="badge <?php echo $badge_class; ?>"><?php echo $status; ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <?php else : ?>
        <div class="report-card">
            <div class="text-center py-5">
                <i class="fas fa-chart-bar" style="font-size: 4rem; color: #dee2e6; margin-bottom: 20px;"></i>
                <h5 class="text-muted">Belum Ada Data Laporan</h5>
                <p class="text-muted">Pilih bulan dan generate laporan untuk melihat data kehadiran</p>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#reportTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json"
                },
                "pageLength": 25,
                "order": [[ 6, "desc" ]],
                "columnDefs": [
                    { "orderable": false, "targets": [0, 7] }
                ]
            });
        });
    </script>
</body>
</html>