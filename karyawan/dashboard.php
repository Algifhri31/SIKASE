<?php
// Sertakan header agar template dan elemen UI konsisten
include "header.php";

// Pastikan session aktif (index.php biasanya sudah memanggil session_start)
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Pastikan koneksi database tersedia. Jika belum, coba include koneksi.php secara langsung
if (!isset($koneksi) || !($koneksi instanceof mysqli)) {
    $koneksi_path = __DIR__ . '/../koneksi.php';
    if (file_exists($koneksi_path)) {
        include_once $koneksi_path;
    }
}

// Jika koneksi masih belum tersedia, hentikan dengan pesan yang jelas
if (!isset($koneksi) || !($koneksi instanceof mysqli)) {
    http_response_code(500);
    echo '<div style="font-family: Arial, sans-serif; padding:20px;">';
    echo '<h2 style="color:#c53030;">Kesalahan koneksi database</h2>';
    echo '<p>Variabel <code>$koneksi</code> tidak tersedia. Pastikan file <code>koneksi.php</code> diexist dan dapat di-include.</p>';
    echo '</div>';
    exit;
}

// Ambil data statistik untuk dashboard
$id_user = $_SESSION['idsi'] ?? '';
$nama_user = $_SESSION['namasi'] ?? 'User';

// Ambil statistik kehadiran dari tb_absen
$bulan_ini = date('Y-m');
$hari_ini = date('d-m-Y');

// Hitung total absen masuk bulan ini
$sql_masuk = "SELECT COUNT(*) as total FROM tb_absen WHERE id_karyawan = '$id_user' AND DATE_FORMAT(waktu, '%Y-%m') = '$bulan_ini' AND tipe = 'masuk'";
$result_masuk = mysqli_query($koneksi, $sql_masuk);
$total_masuk = $result_masuk ? mysqli_fetch_assoc($result_masuk)['total'] : 0;

// Hitung total absen pulang bulan ini
$sql_pulang = "SELECT COUNT(*) as total FROM tb_absen WHERE id_karyawan = '$id_user' AND DATE_FORMAT(waktu, '%Y-%m') = '$bulan_ini' AND tipe = 'pulang'";
$result_pulang = mysqli_query($koneksi, $sql_pulang);
$total_pulang = $result_pulang ? mysqli_fetch_assoc($result_pulang)['total'] : 0;

// Cek status absen hari ini
$sql_hari_ini = "SELECT * FROM tb_absen WHERE id_karyawan = '$id_user' AND waktu LIKE '%$hari_ini%' ORDER BY id DESC LIMIT 1";
$result_hari_ini = mysqli_query($koneksi, $sql_hari_ini);
$absen_hari_ini = $result_hari_ini ? mysqli_fetch_assoc($result_hari_ini) : null;
$next_tipe = ($absen_hari_ini && $absen_hari_ini['tipe'] === 'masuk') ? 'pulang' : 'masuk';
$sudah_pulang = ($absen_hari_ini && $absen_hari_ini['tipe'] === 'pulang');

// Ambil 5 riwayat absen terakhir
$sql_riwayat = "SELECT * FROM tb_absen WHERE id_karyawan = '$id_user' ORDER BY id DESC LIMIT 5";
$result_riwayat = mysqli_query($koneksi, $sql_riwayat);

// Hitung persentase kehadiran
$hari_kerja_bulan_ini = 22; // Asumsi 22 hari kerja per bulan
$persentase_kehadiran = $hari_kerja_bulan_ini > 0 ? round(($total_masuk / $hari_kerja_bulan_ini) * 100) : 0;

// Quick attendance submit
$alert_success = '';
$alert_error = '';
if (isset($_POST['absen_submit']) && !$sudah_pulang) {
    $lokasi = trim($_POST['lokasi'] ?? '');
    $kegiatan = trim($_POST['kegiatan'] ?? '');
    $keterangan = trim($_POST['keterangan'] ?? '');
    if ($lokasi === '' || $kegiatan === '') {
        $alert_error = 'Lokasi dan kegiatan wajib diisi.';
    } else {
        $waktu_now = date('d-m-Y H:i:s');
        $stmt = mysqli_prepare($koneksi, "INSERT INTO tb_absen (id_karyawan, nama, waktu, lokasi, kegiatan, keterangan, tipe) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'sssssss', $id_user, $nama_user, $waktu_now, $lokasi, $kegiatan, $keterangan, $next_tipe);
            if (mysqli_stmt_execute($stmt)) {
                $alert_success = 'Absen ' . $next_tipe . ' berhasil disimpan.';
                // Refresh ringkasan
                $result_hari_ini = mysqli_query($koneksi, $sql_hari_ini);
                $absen_hari_ini = $result_hari_ini ? mysqli_fetch_assoc($result_hari_ini) : null;
                $next_tipe = ($absen_hari_ini && $absen_hari_ini['tipe'] === 'masuk') ? 'pulang' : 'masuk';
                $sudah_pulang = ($absen_hari_ini && $absen_hari_ini['tipe'] === 'pulang');
                $result_riwayat = mysqli_query($koneksi, $sql_riwayat);
                $result_masuk = mysqli_query($koneksi, $sql_masuk);
                $total_masuk = $result_masuk ? mysqli_fetch_assoc($result_masuk)['total'] : $total_masuk;
                $result_pulang = mysqli_query($koneksi, $sql_pulang);
                $total_pulang = $result_pulang ? mysqli_fetch_assoc($result_pulang)['total'] : $total_pulang;
            } else {
                $alert_error = 'Gagal simpan absen: ' . mysqli_error($koneksi);
            }
            mysqli_stmt_close($stmt);
        } else {
            $alert_error = 'Gagal menyiapkan query: ' . mysqli_error($koneksi);
        }
    }
}
?>

<style>
    .content-wrapper {
        padding: 20px 10px 40px;
        background: #f6f7fb;
    }

    .hero-card {
        background: #fff;
        color: #111827;
        border-radius: 20px;
        padding: 24px;
        margin-bottom: 20px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 10px 30px rgba(0,0,0,0.06);
    }

    .hero-title {
        font-size: 22px;
        font-weight: 700;
        margin: 0;
    }

    .hero-meta {
        color: #6b7280;
        font-size: 14px;
        margin: 6px 0 12px 0;
    }

    .quick-action-btn {
        background: #2563eb;
        border: none;
        color: #fff;
        border-radius: 10px;
        padding: 10px 16px;
        text-decoration: none;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        font-weight: 600;
        margin-right: 8px;
        font-size: 14px;
    }
    .quick-action-btn:hover { background: #1d4ed8; color: #fff; text-decoration: none; }
    
    .stats-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 18px;
        text-align: center;
        transition: all 0.2s ease;
        height: 100%;
        box-shadow: 0 8px 20px rgba(0,0,0,0.05);
    }
    .stats-card:hover { transform: translateY(-4px); }
    
    .menu-card {
        background: #fff;
        border-radius: 16px;
        padding: 22px 18px;
        text-align: center;
        box-shadow: 0 8px 22px rgba(0,0,0,0.06);
        transition: all 0.2s ease;
        height: 100%;
        text-decoration: none;
        color: inherit;
        border: 1px solid #e5e7eb;
    }
    .menu-card:hover { transform: translateY(-6px); text-decoration: none; }
    .icon-box { width: 64px; height: 64px; border-radius: 14px; display: flex; align-items: center; justify-content: center; margin: 0 auto 14px; }
    
    .activity-item {
        display: flex;
        align-items: center;
        padding: 15px 0;
        border-bottom: 1px solid #f1f3f4;
    }
    
    .activity-item:last-child {
        border-bottom: none;
    }
    
    .activity-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        font-weight: 600;
        color: #374151;
        font-size: 14px;
    }
    
    .activity-content h6 {
        margin: 0;
        font-weight: 600;
        color: #1f2937;
        font-size: 14px;
    }
    
    .activity-content p {
        margin: 2px 0 0 0;
        color: #6b7280;
        font-size: 12px;
    }
    
    .activity-time {
        margin-left: auto;
        color: #9ca3af;
        font-size: 11px;
    }
    
    @media (max-width: 768px) {
        .hero-card {
            padding: 30px 20px;
        }
        
        .hero-card h1 {
            font-size: 1.8rem !important;
        }
        
        .menu-card {
            padding: 25px 20px;
            margin-bottom: 15px;
        }
        
        .stats-card {
            padding: 20px 15px;
            margin-bottom: 15px;
        }
        
        .quick-action-btn {
            padding: 8px 15px;
            font-size: 13px;
            margin-bottom: 10px;
        }
    }
</style>

<div class="main-content">
    <div class="section__content section__content--p30">
        <div class="container-fluid">
            <div class="content-wrapper">
                <!-- Hero / Welcome -->
                <div class="hero-card">
                    <div class="d-flex flex-wrap align-items-center justify-content-between">
                        <div>
                            <p class="hero-meta mb-1">Beasiswa KSE UINSU</p>
                            <h1 class="hero-title mb-1">Selamat Datang, <?php echo htmlspecialchars($nama_user); ?>!</h1>
                            <div class="hero-meta">
                                <?php 
                                $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                                $bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                echo $hari[date('w')] . ', ' . date('d') . ' ' . $bulan[date('n')-1] . ' ' . date('Y');
                                ?> • <span class="current-time"></span>
                            </div>
                            <div class="d-flex align-items-center flex-wrap">
                                <a href="?m=presensi" class="quick-action-btn me-2 mb-2"><i class="fas fa-clock me-2"></i>Presensi</a>
                                <a href="?m=karyawan&s=profil" class="quick-action-btn me-2 mb-2" style="background:#10b981;"><i class="fas fa-user me-2"></i>Profil</a>
                                <span class="badge <?php echo $absen_hari_ini ? 'bg-success' : 'bg-warning text-dark'; ?> mb-2">
                                    <?php echo $absen_hari_ini ? 'Sudah absen ' . $absen_hari_ini['tipe'] : 'Belum absen hari ini'; ?>
                                </span>
                            </div>
                        </div>
                        <div class="text-end">
                            <div style="font-size:14px; color:#6b7280;">Status Hari Ini</div>
                            <div class="fw-bold" style="font-size:32px;"><?php echo $absen_hari_ini ? ucfirst($absen_hari_ini['tipe']) : 'Belum'; ?></div>
                            <?php if ($absen_hari_ini): ?>
                                <div class="text-muted" style="font-size:14px;">Jam <?php echo date('H:i', strtotime($absen_hari_ini['waktu'])); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Statistik Cards -->
                <div class="row g-4 mb-4">
                    <div class="col-md-3">
                        <div class="stats-card">
                            <div class="icon-box mx-auto mb-3" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);">
                                <i class="fas fa-sign-in-alt text-primary" style="font-size: 24px;"></i>
                            </div>
                            <h3 class="fw-bold text-primary mb-1"><?php echo $total_masuk; ?></h3>
                        <p class="text-muted mb-0">Absen Masuk</p>
                        <small class="text-muted">Bulan ini</small>
                    </div>
                </div>
                    
                    <div class="col-md-3">
                        <div class="stats-card">
                            <div class="icon-box mx-auto mb-3" style="background: linear-gradient(135deg, #e8f5e8 0%, #c8e6c9 100%);">
                                <i class="fas fa-sign-out-alt text-success" style="font-size: 24px;"></i>
                            </div>
                            <h3 class="fw-bold text-success mb-1"><?php echo $total_pulang; ?></h3>
                        <p class="text-muted mb-0">Absen Pulang</p>
                        <small class="text-muted">Bulan ini</small>
                    </div>
                </div>
                    
                    <div class="col-md-3">
                        <div class="stats-card">
                            <div class="icon-box mx-auto mb-3" style="background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%);">
                                <i class="fas fa-percentage text-warning" style="font-size: 24px;"></i>
                            </div>
                            <h3 class="fw-bold text-warning mb-1"><?php echo $persentase_kehadiran; ?>%</h3>
                        <p class="text-muted mb-0">Kehadiran</p>
                        <small class="text-muted">Tingkat kehadiran</small>
                    </div>
                </div>
                    
                    <div class="col-md-3">
                        <div class="stats-card">
                            <div class="icon-box mx-auto mb-3" style="background: linear-gradient(135deg, #f3e5f5 0%, #e1bee7 100%);">
                                <i class="fas fa-calendar-day text-info" style="font-size: 24px;"></i>
                            </div>
                            <h3 class="fw-bold text-info mb-1"><?php echo date('d'); ?></h3>
                        <p class="text-muted mb-0">Hari Ini</p>
                        <small class="text-muted"><?php echo date('M Y'); ?></small>
                    </div>
                    </div>
                </div>

                <!-- Menu Cards -->
                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <a href="?m=presensi" class="menu-card d-block">
                            <div class="icon-box" style="background: linear-gradient(135deg, #e3f2fd 0%, #1976d2 100%); box-shadow: 0 4px 15px rgba(25, 118, 210, 0.3);">
                                <i class="fas fa-clock text-white" style="font-size: 32px;"></i>
                            </div>
                            <h4 class="fw-bold text-dark mb-2">Presensi</h4>
                        <p class="text-muted mb-3">Absen Masuk & Pulang</p>
                        <div class="d-flex justify-content-center">
                            <span class="badge" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); color: #1976d2; padding: 6px 12px;">
                                <i class="fas fa-arrow-right me-1"></i>Mulai Absen
                            </span>
                        </div>
                    </a>
                    </div>

                    <div class="col-md-4">
                        <a href="?m=karyawan&s=profil" class="menu-card d-block">
                            <div class="icon-box" style="background: linear-gradient(135deg, #f3e5f5 0%, #7b1fa2 100%); box-shadow: 0 4px 15px rgba(123, 31, 162, 0.3);">
                                <i class="fas fa-user-circle text-white" style="font-size: 32px;"></i>
                            </div>
                            <h4 class="fw-bold text-dark mb-2">Profil Saya</h4>
                        <p class="text-muted mb-3">Lihat & Edit Profil</p>
                        <div class="d-flex justify-content-center">
                            <span class="badge" style="background: linear-gradient(135deg, #f3e5f5 0%, #e1bee7 100%); color: #7b1fa2; padding: 6px 12px;">
                                <i class="fas fa-edit me-1"></i>Kelola Profil
                            </span>
                        </div>
                    </a>
                    </div>
                    
                    <div class="col-md-4">
                        <a href="?m=karyawan&s=riwayat" class="menu-card d-block">
                            <div class="icon-box" style="background: linear-gradient(135deg, #e8f5e9 0%, #2e7d32 100%); box-shadow: 0 4px 15px rgba(46, 125, 50, 0.3);">
                                <i class="fas fa-history text-white" style="font-size: 32px;"></i>
                            </div>
                            <h4 class="fw-bold text-dark mb-2">Riwayat Absen</h4>
                            <p class="text-muted mb-3">Lihat Riwayat Presensi</p>
                            <div class="d-flex justify-content-center">
                                <span class="badge" style="background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%); color: #2e7d32; padding: 6px 12px;">
                                    <i class="fas fa-eye me-1"></i>Lihat Riwayat
                                </span>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Quick Attendance Form -->
                <div class="card border-0 shadow-sm mb-5" style="border-radius: 20px;">
                    <div class="card-header bg-white border-0" style="border-radius: 20px 20px 0 0; padding: 20px;">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div class="d-flex align-items-center mb-2">
                                <div style="width: 46px; height: 46px; background: linear-gradient(135deg,#3b82f6 0%,#2563eb 100%); border-radius: 12px; display:flex; align-items:center; justify-content:center; margin-right:12px;">
                                    <i class="fas fa-clipboard-check text-white"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-0">Form Absen Cepat</h5>
                                    <small class="text-muted">Tanggal & waktu otomatis, lokasi wajib GPS</small>
                                </div>
                            </div>
                            <span class="badge <?php echo $sudah_pulang ? 'bg-success' : 'bg-warning'; ?> text-dark mb-2">
                                Status: <?php echo $sudah_pulang ? 'Sudah pulang' : 'Belum pulang'; ?>
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <?php if ($alert_success): ?>
                            <div class="alert alert-success"><?php echo htmlspecialchars($alert_success); ?></div>
                        <?php endif; ?>
                        <?php if ($alert_error): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($alert_error); ?></div>
                        <?php endif; ?>
                        <?php if ($sudah_pulang): ?>
                            <div class="alert alert-info mb-0">Anda sudah absen pulang hari ini.</div>
                        <?php else: ?>
                        <form method="post" autocomplete="off" onsubmit="return validateQuickForm();">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">No. KSE</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($id_user); ?>" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nama</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($nama_user); ?>" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Tanggal</label>
                                    <input type="text" class="form-control" id="qtanggal" value="<?php echo date('d-m-Y'); ?>" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Waktu</label>
                                    <input type="text" class="form-control" id="qwaktu" readonly>
                                </div>
                            <div class="col-md-4">
                                <label class="form-label">Tipe Absen</label>
                                <input type="text" class="form-control" value="<?php echo $next_tipe; ?>" readonly>
                            </div>
                                <div class="col-md-12">
                                    <label class="form-label">Lokasi (GPS)</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="lokasi" id="qlokasi" placeholder="Mengambil lokasi..." readonly required>
                                        <button class="btn btn-outline-secondary" type="button" onclick="getQuickLocation()">Ambil Lokasi</button>
                                    </div>
                                    <small id="qlokasi-status" class="text-muted">Aktifkan izin lokasi di browser.</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Kegiatan</label>
                                    <input type="text" class="form-control" name="kegiatan" placeholder="Misal: rapat, kuliah" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Keterangan</label>
                                    <input type="text" class="form-control" name="keterangan" placeholder="Opsional">
                                </div>
                            </div>
                            <div class="mt-3 text-end">
                                <button type="submit" name="absen_submit" class="btn btn-primary">
                                    <i class="fas fa-check-circle me-2"></i>Simpan Absen <?php echo $next_tipe; ?>
                                </button>
                            </div>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>

            <!-- Riwayat Absen Terbaru -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                        <div class="card-header bg-white border-0" style="border-radius: 20px 20px 0 0; padding: 25px;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                        <i class="fas fa-history text-white" style="font-size: 20px;"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold text-dark mb-0">Riwayat Absen Terbaru</h5>
                                        <small class="text-muted">5 aktivitas terakhir</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <?php if ($result_riwayat && mysqli_num_rows($result_riwayat) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($result_riwayat)): ?>
                                    <div class="activity-item">
                                        <div class="activity-avatar">
                                            <?php echo strtoupper(substr($row['tipe'], 0, 1)); ?>
                                        </div>
                                        <div class="activity-content">
                                            <h6><?php echo ucfirst($row['tipe']); ?> - <?php echo date('d/m/Y', strtotime($row['waktu'])); ?></h6>
                                            <p><?php echo htmlspecialchars($row['kegiatan'] ?? 'Tidak ada kegiatan'); ?></p>
                                        </div>
                                        <div class="activity-time">
                                            <?php echo date('H:i', strtotime($row['waktu'])); ?>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-calendar-times text-muted" style="font-size: 48px;"></i>
                                    <p class="text-muted mt-3">Belum ada riwayat absensi</p>
                                    <a href="?m=presensi" class="btn btn-primary">
                                        <i class="fas fa-clock me-2"></i>Mulai Absensi
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Add fade-in animation to elements
    document.addEventListener('DOMContentLoaded', function() {
        const elements = document.querySelectorAll('.card, .menu-card, .hero-card, .stats-card');
        elements.forEach((element, index) => {
            element.style.opacity = '0';
            element.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                element.style.transition = 'all 0.6s ease';
                element.style.opacity = '1';
                element.style.transform = 'translateY(0)';
            }, index * 100);
        });
    });
    
    // Add hover effect to stats cards
    document.querySelectorAll('.stats-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
    
    // Update waktu real-time di hero card
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('id-ID');
        const timeElements = document.querySelectorAll('.current-time');
        timeElements.forEach(element => {
            element.textContent = timeString;
        });
    }
    
    // Update setiap detik
    setInterval(updateTime, 1000);
    updateTime();

    // Quick attendance helpers
    function updateQuickTime() {
        const now = new Date();
        const jam = String(now.getHours()).padStart(2, '0');
        const menit = String(now.getMinutes()).padStart(2, '0');
        const detik = String(now.getSeconds()).padStart(2, '0');
        const el = document.getElementById('qwaktu');
        if (el) el.value = `${jam}:${menit}:${detik} WIB`;
    }
    setInterval(updateQuickTime, 1000);
    updateQuickTime();

    function getQuickLocation() {
        const status = document.getElementById('qlokasi-status');
        const input = document.getElementById('qlokasi');
        if (!navigator.geolocation) {
            status.textContent = 'Browser tidak mendukung geolocation.';
            status.className = 'text-danger';
            return;
        }
        status.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Mengambil lokasi...';
        status.className = 'text-muted';
        navigator.geolocation.getCurrentPosition(function(pos) {
            const coords = pos.coords.latitude + ', ' + pos.coords.longitude;
            input.value = coords;
            status.textContent = 'Lokasi berhasil diambil.';
            status.className = 'text-success';
        }, function(err) {
            status.textContent = 'Gagal mendapatkan lokasi: ' + err.message;
            status.className = 'text-danger';
        }, {enableHighAccuracy:true, timeout:10000});
    }

    function validateQuickForm() {
        const loc = document.getElementById('qlokasi');
        if (loc && !loc.value.trim()) {
            alert('Lokasi wajib diisi (aktifkan izin lokasi).');
            return false;
        }
        return true;
    }

    // Auto fetch location on load
    document.addEventListener('DOMContentLoaded', () => {
        getQuickLocation();
    });
</script>
