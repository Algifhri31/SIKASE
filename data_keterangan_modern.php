<?php
session_start();
require_once 'koneksi.php';

function formatWaktuKeterangan($waktu)
{
    $timestamp = strtotime($waktu);
    if ($timestamp) {
        return date('d/m/Y H:i', $timestamp);
    }

    $bulan = [
        'Januari' => 'January',
        'Februari' => 'February',
        'Maret' => 'March',
        'April' => 'April',
        'Mei' => 'May',
        'Juni' => 'June',
        'Juli' => 'July',
        'Agustus' => 'August',
        'September' => 'September',
        'Oktober' => 'October',
        'November' => 'November',
        'Desember' => 'December'
    ];

    $waktu_normalized = preg_replace('/^[^0-9]+,\s*/u', '', $waktu);
    foreach ($bulan as $id => $en) {
        $waktu_normalized = str_replace($id, $en, $waktu_normalized);
    }

    $timestamp = strtotime($waktu_normalized);
    if ($timestamp) {
        return date('d/m/Y H:i', $timestamp);
    }

    return $waktu;
}

// Cek login dan role
if (!isset($_SESSION['username']) || !isset($_SESSION['level']) || $_SESSION['level'] !== 'admin') {
    header("location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Handle CRUD dengan session alert
$alert = null;

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    $query = "DELETE FROM tb_keterangan WHERE id = $id";
    if (mysqli_query($koneksi, $query)) {
        $alert = ['success' => true, 'message' => 'Data keterangan berhasil dihapus!'];
    }
    else {
        $alert = ['success' => false, 'message' => 'Gagal menghapus data: ' . mysqli_error($koneksi)];
    }

    $_SESSION['alert'] = $alert;
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Ambil alert dari session
if (isset($_SESSION['alert'])) {
    $alert = $_SESSION['alert'];
    unset($_SESSION['alert']);
}

// Pagination
$limit = 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Filters
$now_month = date('m');
$now_year = date('Y');
$filter_month = isset($_GET['bulan']) ? $_GET['bulan'] : '';
$filter_year = isset($_GET['tahun']) ? $_GET['tahun'] : $now_year;
$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';

// Build Where Clause
$conditions = [];
if (!empty($search)) {
    $conditions[] = "(k.nama LIKE '%$search%' OR k.id_karyawan LIKE '%$search%' OR ket.keterangan LIKE '%$search%')";
}
if (!empty($filter_month)) {
    $conditions[] = "MONTH(ket.waktu) = '$filter_month'";
}
if (!empty($filter_year)) {
    $conditions[] = "YEAR(ket.waktu) = '$filter_year'";
}

$where_clause = "";
if (count($conditions) > 0) {
    $where_clause = "WHERE " . implode(" AND ", $conditions);
}

// Get total records
$count_sql = "SELECT COUNT(*) as total FROM tb_keterangan ket 
              LEFT JOIN tb_karyawan k ON ket.id_karyawan = k.id_karyawan 
              $where_clause";
$count_result = mysqli_query($koneksi, $count_sql);
$total_records = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_records / $limit);

// Fetch Data
$sql = "SELECT ket.*, k.nama as nama_karyawan 
        FROM tb_keterangan ket
        LEFT JOIN tb_karyawan k ON ket.id_karyawan = k.id_karyawan
        $where_clause
        ORDER BY ket.waktu DESC 
        LIMIT $limit OFFSET $offset";
$result = mysqli_query($koneksi, $sql);

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
    <title>Data Keterangan - Admin KSE</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f8f9fa;
        }
        
        /* Wrapper & Layout */
        #wrapper {
            display: flex;
            align-items: stretch;
            width: 100%;
            overflow-x: hidden;
        }
        
        #page-content-wrapper {
            width: 100%;
            min-height: 100vh;
            flex-grow: 1;
            padding: 20px;
            transition: all 0.3s;
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
        
        .page-header {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .page-header h2 {
            margin: 0;
            color: #2c3e50;
            font-weight: 700;
        }
        
        .page-header p {
            margin: 5px 0 0 0;
            color: #6c757d;
        }
        
        .data-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            padding: 25px;
            margin-bottom: 25px;
        }
        
        .table th {
            background: #f8f9fa;
            border-top: none;
            font-weight: 600;
            color: #495057;
        }
        
        .badge-sakit {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }
        
        .badge-izin {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }
        
        .badge-cuti {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        }
        
        .search-box {
            position: relative;
        }
        
        .search-box .form-control {
            padding-left: 40px;
        }
        
        .search-box .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }
        

    </style>
</head>
<body class="<?php echo isset($_COOKIE['sidebar_toggle']) && $_COOKIE['sidebar_toggle'] == 'true' ? 'sb-sidenav-toggled' : ''; ?>">

<div class="d-flex" id="wrapper">
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
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <div>
                    <h2 class="fw-bold text-dark mb-1">Data Keterangan</h2>
                    <p class="text-muted mb-0">Kelola data ketidakhadiran (Sakit, Izin, Cuti)</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#exportModal">
                         <i class="fas fa-file-export me-2"></i>Cetak Laporan
                    </button>
                    <span class="badge bg-primary rounded-pill px-4 py-2 d-none d-md-inline-block" style="font-size: 14px; height: fit-content;">
                        <i class="fas fa-clipboard-list me-2"></i><?php echo $total_records; ?> Record
                    </span>
                </div>
            </div>

            <!-- Search & Filter Section -->
            <div class="card card-custom mb-4">
                <div class="card-body p-4">
                    <h5 class="mb-4"><i class="fas fa-filter me-2 text-primary"></i>Filter & Pencarian</h5>
                    <form method="GET" class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label text-muted small fw-bold">PENCARIAN</label>
                            <div class="search-box">
                                <i class="fas fa-search search-icon"></i>
                                <input type="text" class="form-control border-0 bg-light shadow-sm" name="search" 
                                       placeholder="Nama, ID, atau kata kunci..." 
                                       value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-muted small fw-bold">BULAN</label>
                            <select name="bulan" class="form-select border-0 bg-light shadow-sm">
                                <option value="">Semua</option>
                                <?php foreach (get_indonesian_months() as $val => $label): ?>
                                    <option value="<?php echo $val; ?>" <?php echo($filter_month == $val) ? 'selected' : ''; ?>>
                                        <?php echo $label; ?>
                                    </option>
                                <?php
endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-muted small fw-bold">TAHUN</label>
                            <select name="tahun" class="form-select border-0 bg-light shadow-sm">
                                <?php
for ($y = $now_year; $y >= $now_year - 5; $y--) {
    echo "<option value='$y' " . ($filter_year == $y ? 'selected' : '') . ">$y</option>";
}
?>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary w-100 shadow-sm" style="height: 38px;">
                                <i class="fas fa-search me-1"></i>Filter
                            </button>
                            <a href="data_keterangan_modern.php" class="btn btn-light shadow-sm" style="height: 38px;">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>

        <!-- Data Table -->
        <div class="data-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0">
                    <i class="fas fa-table me-2"></i>Daftar Keterangan
                    <?php if (!empty($search)): ?>
                        - Hasil pencarian: "<?php echo htmlspecialchars($search); ?>"
                    <?php
endif; ?>
                </h5>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID Beswan</th>
                            <th>Nama</th>
                            <th>Keterangan</th>
                            <th>Alasan</th>
                            <th>Waktu</th>
                            <th>Bukti</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
if (mysqli_num_rows($result) > 0):
    $no = $offset + 1;
    while ($row = mysqli_fetch_assoc($result)):
        $badge_class = 'badge-izin';
        if ($row['keterangan'] == 'Sakit')
            $badge_class = 'badge-sakit';
        elseif ($row['keterangan'] == 'Cuti')
            $badge_class = 'badge-cuti';
        $waktu_display = formatWaktuKeterangan($row['waktu']);
        $row['waktu_display'] = $waktu_display;
?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><strong><?php echo $row['id_karyawan']; ?></strong></td>
                            <td><?php echo $row['nama_karyawan'] ?: '-'; ?></td>
                            <td>
                                <span class="badge <?php echo $badge_class; ?>">
                                    <?php echo $row['keterangan']; ?>
                                </span>
                            </td>
                            <td><?php echo substr($row['alasan'], 0, 50) . (strlen($row['alasan']) > 50 ? '...' : ''); ?></td>
                            <td><?php echo $waktu_display; ?></td>
                            <td>
                                <?php if (!empty($row['bukti'])): ?>
                                    <a href="uploads/<?php echo $row['bukti']; ?>" target="_blank" class="btn btn-sm btn-info">
                                        <i class="fas fa-file"></i> Lihat
                                    </a>
                                <?php
        else: ?>
                                    <span class="text-muted">-</span>
                                <?php
        endif; ?>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-primary me-1" 
                                        onclick="viewDetail(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="?delete=<?php echo $row['id']; ?>" 
                                   class="btn btn-sm btn-danger delete-btn"
                                   data-id="<?php echo $row['id']; ?>"
                                   data-nama="<?php echo htmlspecialchars($row['nama_karyawan']); ?>">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php
    endwhile;
else:
?>
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Tidak ada data keterangan</h5>
                            </td>
                        </tr>
                        <?php
endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">Previous</a>
                        </li>
                        
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php
    endfor; ?>
                        
                        <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">Next</a>
                        </li>
                    </ul>
                </nav>
            <?php
endif; ?>
        </div>
</div>

    <!-- Detail Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-info-circle me-2"></i>Detail Keterangan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>ID Beswan:</strong> <span id="detail_id_karyawan"></span></p>
                            <p><strong>Nama:</strong> <span id="detail_nama"></span></p>
                            <p><strong>Keterangan:</strong> <span id="detail_keterangan"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Waktu:</strong> <span id="detail_waktu"></span></p>
                            <p><strong>Bukti:</strong> <span id="detail_bukti"></span></p>
                        </div>
                        <div class="col-12">
                            <p><strong>Alasan:</strong></p>
                            <p id="detail_alasan" class="border p-3 rounded bg-light"></p>
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
                    <h5 class="modal-title fw-bold" id="exportModalLabel text-white">
                        <i class="fas fa-print me-2 text-white"></i>Export Rekap Keterangan
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted small mb-4 text-center">Data yang diexport akan mengikuti filter periode: <br><strong><?php echo(isset($_GET['bulan']) && $_GET['bulan'] != '' ? date('F', mktime(0, 0, 0, $_GET['bulan'], 10)) : 'Semua Bulan') . ' ' . $filter_year; ?></strong></p>
                    
                    <div class="row g-3">
                        <!-- Excel Option -->
                        <div class="col-12">
                            <a href="export_keterangan.php?bulan=<?php echo $filter_month; ?>&tahun=<?php echo $filter_year; ?>&search=<?php echo urlencode($search); ?>" 
                               class="export-card btn w-100 text-start p-3 border rounded-3 position-relative overflow-hidden">
                                <div class="d-flex align-items-center position-relative" style="z-index: 2;">
                                    <div class="icon-box-lg bg-success text-white rounded-3 me-3">
                                        <i class="fas fa-file-excel fa-2x"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark">Rekap Excel (.XLS)</h6>
                                        <p class="text-muted small mb-0">Data sakit, izin & cuti beswan</p>
                                    </div>
                                    <i class="fas fa-chevron-right ms-auto text-muted"></i>
                                </div>
                                <div class="export-card-bg bg-success opacity-10"></div>
                            </a>
                        </div>
                        
                        <!-- PDF Option -->
                        <div class="col-12">
                            <a href="export_keterangan_pdf.php?bulan=<?php echo $filter_month; ?>&tahun=<?php echo $filter_year; ?>&search=<?php echo urlencode($search); ?>" 
                               class="export-card btn w-100 text-start p-3 border rounded-3 position-relative overflow-hidden mt-2">
                                <div class="d-flex align-items-center position-relative" style="z-index: 2;">
                                    <div class="icon-box-lg bg-danger text-white rounded-3 me-3">
                                        <i class="fas fa-file-pdf fa-2x"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark">Laporan PDF (Resmi)</h6>
                                        <p class="text-muted small mb-0">Format surat resmi dengan tanda tangan</p>
                                    </div>
                                    <i class="fas fa-chevron-right ms-auto text-muted"></i>
                                </div>
                                <div class="export-card-bg bg-danger opacity-10"></div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<style>
    .export-card {
        border: 1px solid #e1e8ed !important;
        transition: all 0.3s ease;
        text-decoration: none !important;
        background: white;
    }
    .export-card:hover {
        transform: scale(1.02);
        box-shadow: 0 5px 15px rgba(0,0,0,0.08) !important;
        border-color: #667eea !important;
    }
    .icon-box-lg {
        width: 60px; height: 60px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .export-card-bg {
        position: absolute; right: -15px; bottom: -15px;
        width: 120px; height: 120px; border-radius: 50%;
        z-index: 1; transition: all 0.5s ease;
    }
    .export-card:hover .export-card-bg { transform: scale(1.5); opacity: 0.2; }
    .opacity-10 { opacity: 0.1; }
</style>

<!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Show SweetAlert if there's an alert message
        <?php if ($alert): ?>
            Swal.fire({
                icon: '<?php echo $alert['success'] ? 'success' : 'error'; ?>',
                title: '<?php echo $alert['success'] ? 'Berhasil!' : 'Gagal!'; ?>',
                text: '<?php echo addslashes($alert['message']); ?>',
                confirmButtonColor: '#667eea',
                timer: 3000
            });
        <?php
endif; ?>
        
        // Handle delete with SweetAlert
        document.querySelectorAll('.delete-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                
                const nama = this.dataset.nama;
                const url = this.href;
                
                Swal.fire({
                    title: 'Hapus Data Keterangan?',
                    html: `Apakah Anda yakin ingin menghapus keterangan dari:<br><strong>${nama}</strong>?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-trash me-2"></i>Ya, Hapus!',
                    cancelButtonText: '<i class="fas fa-times me-2"></i>Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Menghapus Data...',
                            text: 'Mohon tunggu sebentar',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        
                        window.location.href = url;
                    }
                });
            });
        });
        
        // View detail function
        function viewDetail(data) {
            document.getElementById('detail_id_karyawan').textContent = data.id_karyawan;
            document.getElementById('detail_nama').textContent = data.nama_karyawan || '-';
            document.getElementById('detail_keterangan').innerHTML = `<span class="badge bg-primary">${data.keterangan}</span>`;
            document.getElementById('detail_waktu').textContent = data.waktu_display || data.waktu;
            document.getElementById('detail_alasan').textContent = data.alasan;
            
            if (data.bukti) {
                document.getElementById('detail_bukti').innerHTML = `<a href="uploads/${data.bukti}" target="_blank" class="btn btn-sm btn-info"><i class="fas fa-file me-1"></i>Lihat Bukti</a>`;
            } else {
                document.getElementById('detail_bukti').textContent = '-';
            }
            
            new bootstrap.Modal(document.getElementById('detailModal')).show();
        }
    </script>
</body>
</html>
