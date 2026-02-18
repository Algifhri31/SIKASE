<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Beswan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 40px;
            margin-bottom: 30px;
        }
        .menu-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            height: 100%;
        }
        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        .icon-box {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
        }
    </style>
</head>
<body>
    <?php
    // Session sudah dimulai di index.php, jadi tidak perlu session_start() lagi
    
    // Cek session
    if (!isset($_SESSION['idsi']) || !isset($_SESSION['level']) || $_SESSION['level'] !== 'beswan') {
        echo "<script>alert('Silakan login terlebih dahulu'); window.location.href='../login.php';</script>";
        exit;
    }
    
    $nama_user = $_SESSION['namasi'] ?? 'User';
    ?>
    
    <div class="container-fluid">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
            <div class="container-fluid">
                <a class="navbar-brand fw-bold" href="#">
                    <i class="fas fa-graduation-cap text-primary me-2"></i>
                    Beswan KSE
                </a>
                <div class="navbar-nav ms-auto">
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i>
                            <?php echo htmlspecialchars($nama_user); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="?m=karyawan&s=profil"><i class="fas fa-user me-2"></i>Profil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <div class="container">
            <!-- Hero Section -->
            <div class="hero-section text-center">
                <h1 class="fw-bold mb-3">Selamat Datang, <?php echo htmlspecialchars($nama_user); ?>! 👋</h1>
                <p class="mb-0 opacity-75">Sistem Informasi Kehadiran Beasiswa KSE</p>
            </div>

            <!-- Menu Cards -->
            <div class="row g-4">
                <div class="col-md-4">
                    <a href="?m=presensi" class="text-decoration-none">
                        <div class="card menu-card">
                            <div class="card-body text-center p-4">
                                <div class="icon-box bg-primary bg-opacity-10">
                                    <i class="fas fa-clock text-primary fs-3"></i>
                                </div>
                                <h5 class="fw-bold text-dark mb-2">Presensi</h5>
                                <p class="text-muted mb-0">Absen Masuk/Pulang</p>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-4">
                    <a href="?m=karyawan&s=profil" class="text-decoration-none">
                        <div class="card menu-card">
                            <div class="card-body text-center p-4">
                                <div class="icon-box bg-success bg-opacity-10">
                                    <i class="fas fa-user-circle text-success fs-3"></i>
                                </div>
                                <h5 class="fw-bold text-dark mb-2">Profil Saya</h5>
                                <p class="text-muted mb-0">Lihat & Edit Profil</p>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-4">
                    <a href="?m=karyawan&s=riwayat" class="text-decoration-none">
                        <div class="card menu-card">
                            <div class="card-body text-center p-4">
                                <div class="icon-box bg-info bg-opacity-10">
                                    <i class="fas fa-history text-info fs-3"></i>
                                </div>
                                <h5 class="fw-bold text-dark mb-2">Riwayat</h5>
                                <p class="text-muted mb-0">Lihat Riwayat Absen</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Info Section -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="fw-bold text-dark mb-3">
                                <i class="fas fa-info-circle text-primary me-2"></i>
                                Informasi
                            </h5>
                            <p class="text-muted mb-0">
                                Selamat datang di Sistem Informasi Kehadiran Beasiswa KSE. 
                                Gunakan menu di atas untuk mengakses fitur-fitur yang tersedia seperti presensi, 
                                melihat profil, dan riwayat kehadiran Anda.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>