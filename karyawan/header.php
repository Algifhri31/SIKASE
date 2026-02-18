<?php
// Tampilkan error supaya halaman kosong mudah dilacak saat ada masalah
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include project koneksi.php menggunakan path absolut
$project_koneksi = __DIR__ . '/../koneksi.php';
if (file_exists($project_koneksi)) {
    include_once $project_koneksi;
}

// Ambil data user untuk profil di topbar
$id_user_header = $_SESSION['idsi'] ?? '';
$nama_user_header = $_SESSION['namasi'] ?? 'Beswan';
$foto_user_header = 'default.jpg'; // Default

if ($id_user_header) {
    $sql_header = "SELECT foto, nama FROM tb_karyawan WHERE id_karyawan = '$id_user_header'";
    $query_header = mysqli_query($koneksi, $sql_header);
    if ($query_header && mysqli_num_rows($query_header) > 0) {
        $r_header = mysqli_fetch_array($query_header);
        $foto_user_header = $r_header['foto'] ?: 'default.jpg';
        $nama_user_header = $r_header['nama'];
    }
}

// Ambil statistik ringkas untuk topbar jika perlu
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : "Beswan Dashboard"; ?> - KSE UINSU</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --sidebar-width: 260px;
            --topbar-height: 70px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8f9fa;
            margin: 0;
            overflow-x: hidden;
        }
        
        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            background: #fff;
            box-shadow: 4px 0 15px rgba(0,0,0,0.05);
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1050;
            transition: all 0.3s ease;
        }
        
        .sidebar-header {
            padding: 25px 20px;
            border-bottom: 1px solid #f1f3f9;
            display: flex;
            align-items: center;
        }
        
        .brand-logo {
            width: 40px;
            height: 40px;
            background: var(--primary-gradient);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            box-shadow: 0 4px 10px rgba(102, 126, 234, 0.3);
        }
        
        .brand-name {
            font-weight: 700;
            font-size: 1.2rem;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 0;
        }
        
        .sidebar-menu {
            padding: 25px 15px;
        }
        
        .nav-link-custom {
            display: flex;
            align-items: center;
            padding: 12px 18px;
            color: #64748b;
            text-decoration: none;
            border-radius: 12px;
            margin-bottom: 8px;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        .nav-link-custom i {
            font-size: 1.1rem;
            margin-right: 14px;
            width: 24px;
            text-align: center;
            transition: all 0.2s ease;
        }
        
        .nav-link-custom:hover {
            background: #f1f5f9;
            color: #1e293b;
        }
        
        .nav-link-custom.active {
            background: #eef2ff;
            color: #4f46e5;
        }
        
        .nav-link-custom.active i {
            color: #4f46e5;
        }

        .nav-link-logout {
            color: #ef4444;
            margin-top: 20px;
            border-top: 1px solid #f1f3f9;
            padding-top: 20px;
        }

        .nav-link-logout:hover {
            background: #fef2f2;
            color: #dc2626;
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.6; }
            100% { opacity: 1; }
        }
        .animated-pulse {
            animation: pulse 1.5s infinite;
        }
        
        /* Main Content wrapper */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: all 0.3s ease;
        }
        
        /* Topbar Style */
        .topbar {
            height: var(--topbar-height);
            background: #fff;
            padding: 0 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1040;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        }
        
        .page-content {
            padding: 30px;
        }
        
        .mobile-toggle {
            display: none;
            font-size: 1.5rem;
            color: #64748b;
            cursor: pointer;
        }

        /* Profile Dropdown */
        .profile-btn {
            display: flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 50px;
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            cursor: pointer;
            transition: all 0.2s;
        }

        .profile-btn:hover {
            background: #f1f5f9;
        }

        .profile-img {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
            border: 2px solid #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .profile-name {
            font-size: 0.9rem;
            font-weight: 600;
            color: #334155;
        }

        @media (max-width: 991px) {
            .sidebar {
                left: calc(-1 * var(--sidebar-width));
            }
            .sidebar.show {
                left: 0;
            }
            .main-wrapper {
                margin-left: 0;
            }
            .mobile-toggle {
                display: block;
            }
            .page-content {
                padding: 15px;
            }
        }

        /* Overlay for mobile */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1045;
        }
        .sidebar-overlay.show {
            display: block;
        }
    </style>
</head>
<body>
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="brand-logo">
                <i class="fas fa-graduation-cap text-white"></i>
            </div>
            <h1 class="brand-name">Beswan KSE</h1>
        </div>
        
        <nav class="sidebar-menu">
            <?php
$current_m = isset($_GET['m']) ? $_GET['m'] : 'awal';
$current_s = isset($_GET['s']) ? $_GET['s'] : '';
?>
            <a href="index.php?m=awal" class="nav-link-custom <?php echo($current_m == 'awal') ? 'active' : ''; ?>">
                <i class="fas fa-th-large"></i>
                <span>Dashboard</span>
            </a>
            <a href="index.php?m=presensi" class="nav-link-custom <?php echo($current_m == 'presensi') ? 'active' : ''; ?>">
                <i class="fas fa-camera"></i>
                <span>Presensi Digital</span>
            </a>
            <a href="index.php?m=karyawan&s=profil" class="nav-link-custom <?php echo($current_s == 'profil') ? 'active' : ''; ?>">
                <i class="fas fa-user-circle"></i>
                <span>Profil Beswan</span>
            </a>
            <a href="index.php?m=karyawan&s=riwayat" class="nav-link-custom <?php echo($current_s == 'riwayat') ? 'active' : ''; ?>">
                <i class="fas fa-history"></i>
                <span>Riwayat Absen</span>
            </a>
            
            <a href="#" class="nav-link-custom nav-link-logout" onclick="confirmLogout(event)">
                <i class="fas fa-sign-out-alt"></i>
                <span>Keluar Sistem</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content Wrapper -->
    <main class="main-wrapper">
        <!-- Topbar -->
        <header class="topbar">
            <div class="mobile-toggle" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </div>
            
            <div class="d-flex align-items-center">
                <div class="d-none d-md-flex align-items-center me-4 bg-light px-3 py-2 rounded-pill shadow-sm" style="border: 1px solid #eef2ff;">
                    <i class="fas fa-clock text-primary me-2 animated-pulse"></i>
                    <span class="text-dark small fw-bold" id="currentDate" style="min-width: 220px;">
                        <?php echo date('d M Y'); ?>
                    </span>
                </div>
                
                <div class="dropdown">
                    <div class="profile-btn dropdown-toggle" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="../images/<?php echo $foto_user_header; ?>" class="profile-img" alt="Foto Profil">
                        <span class="profile-name d-none d-sm-block"><?php echo htmlspecialchars($nama_user_header); ?></span>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" aria-labelledby="profileDropdown" style="border-radius: 12px; min-width: 200px;">
                        <li class="p-3 d-flex align-items-center border-bottom mb-2">
                             <img src="../images/<?php echo $foto_user_header; ?>" class="rounded-circle me-3" width="40" height="40" style="object-fit: cover;">
                             <div>
                                 <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($nama_user_header); ?></h6>
                                 <small class="text-muted">Beswan KSE</small>
                             </div>
                        </li>
                        <li>
                            <a class="dropdown-item py-2" href="index.php?m=karyawan&s=profil">
                                <i class="fas fa-user-edit me-2 text-primary"></i> Edit Profil
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item py-2 text-danger" href="#" onclick="confirmLogout(event)">
                                <i class="fas fa-sign-out-alt me-2"></i> Keluar
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <div class="page-content">
            <!-- Content of each page will be here -->
