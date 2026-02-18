<?php
// Session sudah di-start di index.php
$page_title = "Dashboard Beswan";
include "header.php";

// Ambil data user untuk riwayat
$id_user = $_SESSION['idsi'] ?? '';
$nama_user = $_SESSION['namasi'] ?? 'Beswan';
$bulan_ini = date('Y-m');

// Statistik Hadir Bulan Ini
$sql_masuk = "SELECT COUNT(*) as total FROM tb_absen WHERE id_karyawan = '$id_user' AND DATE_FORMAT(waktu, '%Y-%m') = '$bulan_ini' AND tipe = 'masuk'";
$result_masuk = mysqli_query($koneksi, $sql_masuk);
$total_hadir_bulan = $result_masuk ? mysqli_fetch_assoc($result_masuk)['total'] : 0;

// Statistik Izin/Sakit Bulan Ini
$sql_ket = "SELECT COUNT(*) as total FROM tb_keterangan WHERE id_karyawan = '$id_user' AND DATE_FORMAT(waktu, '%Y-%m') = '$bulan_ini'";
$result_ket = mysqli_query($koneksi, $sql_ket);
$total_izin_bulan = $result_ket ? mysqli_fetch_assoc($result_ket)['total'] : 0;

// Cek status hari ini
$tgl_hari_ini = date('Y-m-d');
$sql_hari_ini = "SELECT * FROM tb_absen WHERE id_karyawan = '$id_user' AND DATE(waktu) = '$tgl_hari_ini' ORDER BY id DESC LIMIT 1";
$result_hari = mysqli_query($koneksi, $sql_hari_ini);
$absen_hari_ini = $result_hari ? mysqli_fetch_assoc($result_hari) : null;

// Ambil riwayat gabungan (5 terakhir)
$sql_combined = "(SELECT 'hadir' as source, tipe, waktu, kegiatan as info FROM tb_absen WHERE id_karyawan = '$id_user')
                 UNION 
                 (SELECT 'ket' as source, keterangan as tipe, waktu, alasan as info FROM tb_keterangan WHERE id_karyawan = '$id_user')
                 ORDER BY waktu DESC LIMIT 5";
$result_riwayat = mysqli_query($koneksi, $sql_combined);
?>

<style>
    .welcome-card {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        color: white;
        border-radius: 20px;
        padding: 40px;
        position: relative;
        overflow: hidden;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(79, 70, 229, 0.2);
    }
    
    .welcome-card::after {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 200px;
        height: 200px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
    }

    .stat-card {
        border-radius: 20px;
        border: none;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        transition: transform 0.3s;
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }

    .icon-box {
        width: 55px;
        height: 55px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
    }

    .quick-action-card {
        background: #fff;
        border-radius: 20px;
        padding: 25px;
        text-align: center;
        text-decoration: none;
        color: #334155;
        border: 1px solid #f1f5f9;
        transition: all 0.3s;
    }

    .quick-action-card:hover {
        background: #f8fafc;
        border-color: #e2e8f0;
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        color: #4f46e5;
    }

    .action-icon {
        width: 65px;
        height: 65px;
        background: #f1f5f9;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        margin: 0 auto 15px;
        transition: all 0.3s;
    }

    .quick-action-card:hover .action-icon {
        background: #e0e7ff;
        color: #4f46e5;
    }

    .table-modern thead th {
        background: #f8fafc;
        border: none;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        padding: 15px;
    }

    .table-modern tbody td {
        padding: 20px 15px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }

    .badge-soft-success { background: #dcfce7; color: #166534; }
    .badge-soft-warning { background: #fef9c3; color: #854d0e; }
    .badge-soft-primary { background: #e0e7ff; color: #3730a3; }
</style>

<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="welcome-card">
        <div class="row align-items-center">
            <div class="col-md-9">
                <h2 class="fw-bold mb-2">Semangat Hari Ini, <?php echo htmlspecialchars(explode(' ', $nama_user)[0]); ?>! 👋</h2>
                <p class="mb-0 opacity-90 h6 fw-normal">Sudahkah anda mengisi absensi hari ini? Tetap semangat dan berikan yang terbaik.</p>
            </div>
            <div class="col-md-4 text-md-end d-none d-md-block">
                <i class="fas fa-quote-right fa-3x opacity-25"></i>
            </div>
        </div>
    </div>

    <!-- Stats Section -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-4 mb-md-0">
            <div class="card stat-card p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="icon-box bg-primary text-white me-3">
                        <i class="fas fa-calendar-check text-white"></i>
                    </div>
                    <div>
                        <h6 class="text-muted small mb-0">Hadir (Bulan Ini)</h6>
                        <h4 class="fw-bold mb-0 text-primary"><?php echo $total_hadir_bulan; ?></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-4 mb-md-0">
            <div class="card stat-card p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="icon-box bg-success text-white me-3">
                        <i class="fas fa-file-medical text-white"></i>
                    </div>
                    <div>
                        <h6 class="text-muted small mb-0">Izin/Sakit</h6>
                        <h4 class="fw-bold mb-0 text-success"><?php echo $total_izin_bulan; ?></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-4 mb-sm-0">
            <div class="card stat-card p-3">
                <div class="d-flex align-items-center">
                    <div class="icon-box bg-info text-white me-3">
                        <i class="fas fa-clock text-white"></i>
                    </div>
                    <div>
                        <h6 class="text-muted small mb-0">Hari Ini</h6>
                        <h4 class="fw-bold mb-0"><?php echo $absen_hari_ini ? ($absen_hari_ini['tipe'] == 'masuk' ? 'Sudah Masuk' : 'Sudah Pulang') : 'Belum Absen'; ?></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card stat-card p-3">
                <div class="d-flex align-items-center">
                    <div class="icon-box bg-warning text-white me-3">
                        <i class="fas fa-award text-white"></i>
                    </div>
                    <div>
                        <h6 class="text-muted small mb-0">Status</h6>
                        <h4 class="fw-bold mb-0">Aktif</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Quick Actions -->
            <h5 class="fw-bold mb-3 d-flex align-items-center">
                <span class="me-2">⚡</span> Menu Cepat
            </h5>
            <div class="row g-3 mb-5">
                <div class="col-md-4">
                    <a href="index.php?m=presensi" class="quick-action-card d-block">
                        <div class="action-icon">
                            <i class="fas fa-camera"></i>
                        </div>
                        <h6 class="fw-bold mb-1">Presensi Digital</h6>
                        <p class="text-muted small mb-0">Scan & Foto Lokasi</p>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="index.php?m=karyawan&s=riwayat" class="quick-action-card d-block">
                        <div class="action-icon">
                            <i class="fas fa-history"></i>
                        </div>
                        <h6 class="fw-bold mb-1">Riwayat Absen</h6>
                        <p class="text-muted small mb-0">Log Kehadiran Saya</p>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="index.php?m=karyawan&s=profil" class="quick-action-card d-block">
                        <div class="action-icon">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <h6 class="fw-bold mb-1">Profil Beswan</h6>
                        <p class="text-muted small mb-0">Identitas & Akun</p>
                    </a>
                </div>
            </div>

            <!-- Recent History -->
            <div class="card border-0 shadow-sm p-4" style="border-radius: 20px;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0">Riwayat Terakhir</h5>
                    <a href="index.php?m=karyawan&s=riwayat" class="btn btn-link btn-sm text-decoration-none fw-bold">Lihat Semua →</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Tipe</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result_riwayat) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($result_riwayat)): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold"><?php echo date('d M Y', strtotime($row['waktu'])); ?></div>
                                        <small class="text-muted"><?php echo date('H:i', strtotime($row['waktu'])); ?> WIB</small>
                                    </td>
                                    <td>
                                        <?php
        $type = strtolower($row['tipe']);
        $badge_class = 'badge-soft-primary';
        if ($type == 'masuk')
            $badge_class = 'badge-soft-success';
        elseif ($type == 'izin' || $type == 'sakit')
            $badge_class = 'badge-soft-warning';
?>
                                        <span class="badge rounded-pill <?php echo $badge_class; ?> px-3 py-2">
                                            <?php echo strtoupper($row['tipe']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 150px;">
                                            <?php echo htmlspecialchars($row['info'] ?: '-'); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-light rounded-circle"><i class="fas fa-chevron-right"></i></button>
                                    </td>
                                </tr>
                                <?php
    endwhile; ?>
                            <?php
else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">Belum ada data absensi</td>
                                </tr>
                            <?php
endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar Dashboard Info -->
        <div class="col-lg-4 mt-4 mt-lg-0">
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px; background: #fff;">
                <div class="card-body p-4 text-center">
                    <h5 class="fw-bold mb-4">Status Akun</h5>
                    <div class="mb-4 position-relative d-inline-block">
                        <div style="padding: 10px; background: #fff; border-radius: 50%; box-shadow: 0 5px 15px rgba(0,0,0,0.05); display: inline-block;">
                            <img src="../images/<?php echo (string)($foto_user_header ?? 'default.jpg'); ?>" class="rounded-pill" width="100" height="100" style="object-fit: cover; border: 4px solid #f8fafc !important;">
                        </div>
                        <span class="position-absolute" style="bottom: 15px; right: 15px; width: 15px; height: 15px; background: #10b981; border: 3px solid #fff; border-radius: 50%;" title="Online"></span>
                    </div>
                    <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($nama_user); ?></h6>
                    <p class="text-muted small mb-4">ID: <?php echo $_SESSION['idsi']; ?></p>
                    
                    <div class="d-grid gap-2">
                        <a href="index.php?m=karyawan&s=profil" class="btn btn-outline-primary rounded-pill btn-sm">Buka Profil</a>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm" style="border-radius: 20px; background: #faf5ff;">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3 d-flex align-items-center">
                        <i class="fas fa-bullhorn text-primary me-2"></i> Info Paguyuban
                    </h6>
                    <p class="small text-muted mb-0">Pastikan anda mengisi kegiataan saat absensi Masuk dan Pulang agar terdokumentasi dengan baik.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>