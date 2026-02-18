<?php
$page_title = "Dashboard Beswan";

// Cek session
if (!isset($_SESSION['idsi']) || !isset($_SESSION['level']) || $_SESSION['level'] !== 'beswan') {
    echo "<script>alert('Silakan login terlebih dahulu'); window.location.href='../login.php';</script>";
    exit;
}

$nama_user = $_SESSION['namasi'] ?? 'User';
$id_user = $_SESSION['idsi'] ?? '';

include "header.php";
?>

<!-- Simple Top Bar -->
<div style="background: #fff; padding: 15px 30px; margin-bottom: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
    <div class="d-flex justify-content-between align-items-center">
        <h5 style="margin: 0; color: #2c3e50; font-weight: 600;">
            <i class="fas fa-tachometer-alt" style="color: #3498db; margin-right: 8px;"></i>
            Dashboard
        </h5>
        <div class="d-flex align-items-center">
            <span style="color: #7f8c8d; margin-right: 15px; font-size: 14px;">
                <?php echo date('d M Y, H:i'); ?>
            </span>
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none" style="color: #2c3e50;">
                    <i class="fas fa-user-circle" style="font-size: 24px; margin-right: 8px; color: #3498db;"></i>
                    <span style="font-weight: 500;"><?php echo htmlspecialchars($nama_user); ?></span>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="main-content">
    <div class="section__content section__content--p30">
        <div class="container-fluid">
            <!-- Welcome Hero -->
            <div class="row mb-4">
                <div class="col-12">
                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px; padding: 40px; color: white; text-align: center;">
                        <h1 style="font-weight: 700; margin-bottom: 15px; font-size: 32px;">
                            Selamat Datang, <?php echo htmlspecialchars($nama_user); ?>! 👋
                        </h1>
                        <p style="font-size: 18px; opacity: 0.9; margin: 0;">
                            Sistem Informasi Kehadiran Beasiswa KSE
                        </p>
                    </div>
                </div>
            </div>

            <!-- Quick Menu -->
            <div class="row">
                <div class="col-md-4 mb-4">
                    <a href="?m=presensi" class="text-decoration-none">
                        <div class="card h-100" style="border-radius: 15px; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.08); transition: all 0.3s ease;">
                            <div class="card-body text-center p-4">
                                <div style="width: 70px; height: 70px; background: #e3f2fd; border-radius: 15px; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                                    <i class="fas fa-clock" style="font-size: 28px; color: #1976d2;"></i>
                                </div>
                                <h4 style="font-weight: 600; color: #2c3e50; margin-bottom: 10px;">Presensi</h4>
                                <p style="color: #7f8c8d; margin: 0;">Absen Masuk & Pulang</p>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-4 mb-4">
                    <a href="?m=karyawan&s=profil" class="text-decoration-none">
                        <div class="card h-100" style="border-radius: 15px; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.08); transition: all 0.3s ease;">
                            <div class="card-body text-center p-4">
                                <div style="width: 70px; height: 70px; background: #f3e5f5; border-radius: 15px; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                                    <i class="fas fa-user-circle" style="font-size: 28px; color: #7b1fa2;"></i>
                                </div>
                                <h4 style="font-weight: 600; color: #2c3e50; margin-bottom: 10px;">Profil Saya</h4>
                                <p style="color: #7f8c8d; margin: 0;">Lihat & Edit Profil</p>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-4 mb-4">
                    <a href="?m=karyawan&s=riwayat" class="text-decoration-none">
                        <div class="card h-100" style="border-radius: 15px; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.08); transition: all 0.3s ease;">
                            <div class="card-body text-center p-4">
                                <div style="width: 70px; height: 70px; background: #e8f5e8; border-radius: 15px; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                                    <i class="fas fa-history" style="font-size: 28px; color: #388e3c;"></i>
                                </div>
                                <h4 style="font-weight: 600; color: #2c3e50; margin-bottom: 10px;">Riwayat</h4>
                                <p style="color: #7f8c8d; margin: 0;">Lihat Riwayat Absen</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Info Section -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card" style="border-radius: 15px; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.08);">
                        <div class="card-body p-4">
                            <h5 style="color: #2c3e50; font-weight: 600; margin-bottom: 15px;">
                                <i class="fas fa-info-circle" style="color: #3498db; margin-right: 10px;"></i>
                                Panduan Penggunaan
                            </h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="d-flex align-items-start mb-3">
                                        <div style="width: 30px; height: 30px; background: #e3f2fd; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 15px; flex-shrink: 0;">
                                            <span style="color: #1976d2; font-weight: bold; font-size: 14px;">1</span>
                                        </div>
                                        <div>
                                            <h6 style="color: #2c3e50; font-weight: 600; margin-bottom: 5px;">Presensi</h6>
                                            <p style="color: #7f8c8d; font-size: 14px; margin: 0;">Klik menu Presensi untuk melakukan absen masuk atau pulang</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex align-items-start mb-3">
                                        <div style="width: 30px; height: 30px; background: #f3e5f5; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 15px; flex-shrink: 0;">
                                            <span style="color: #7b1fa2; font-weight: bold; font-size: 14px;">2</span>
                                        </div>
                                        <div>
                                            <h6 style="color: #2c3e50; font-weight: 600; margin-bottom: 5px;">Profil</h6>
                                            <p style="color: #7f8c8d; font-size: 14px; margin: 0;">Lihat dan edit informasi profil pribadi Anda</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex align-items-start mb-3">
                                        <div style="width: 30px; height: 30px; background: #e8f5e8; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 15px; flex-shrink: 0;">
                                            <span style="color: #388e3c; font-weight: bold; font-size: 14px;">3</span>
                                        </div>
                                        <div>
                                            <h6 style="color: #2c3e50; font-weight: 600; margin-bottom: 5px;">Riwayat</h6>
                                            <p style="color: #7f8c8d; font-size: 14px; margin: 0;">Cek riwayat kehadiran dan absensi sebelumnya</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>

<style>
.card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.15) !important;
}

.card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

@media (max-width: 768px) {
    .card-body {
        padding: 25px !important;
    }
    
    h1 {
        font-size: 24px !important;
    }
    
    h4 {
        font-size: 18px !important;
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.card {
    animation: fadeInUp 0.6s ease-out;
}

.card:nth-child(1) { animation-delay: 0.1s; }
.card:nth-child(2) { animation-delay: 0.2s; }
.card:nth-child(3) { animation-delay: 0.3s; }
</style>