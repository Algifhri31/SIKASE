<?php
// Cek session admin
if (!isset($_SESSION['username']) || !isset($_SESSION['level']) || $_SESSION['level'] !== 'admin') {
    header("location: ../login.php");
    exit();
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : "Admin Dashboard"; ?> - KSE UINSU</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        
        .sidebar {
            width: 280px;
            height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            box-shadow: 4px 0 15px rgba(0,0,0,0.1);
        }
        
        .sidebar-header {
            padding: 25px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-align: center;
        }
        
        .sidebar-header h3 {
            color: white;
            font-weight: 700;
            margin: 0;
            font-size: 24px;
        }
        
        .sidebar-header p {
            color: rgba(255,255,255,0.8);
            margin: 5px 0 0 0;
            font-size: 14px;
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .sidebar-menu a {
            display: block;
            padding: 15px 25px;
            color: rgba(255,255,255,0.9);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }
        
        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: white;
            transform: translateX(5px);
        }
        
        .sidebar-menu i {
            width: 20px;
            margin-right: 12px;
            text-align: center;
        }
        
        .main-content {
            margin-left: 280px;
            padding: 0;
            min-height: 100vh;
        }
        
        .top-navbar {
            background: white;
            padding: 20px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        
        .content-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 25px;
        }
        
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }
        
        .table {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .table thead th {
            background: #f8f9fa;
            border: none;
            font-weight: 600;
            color: #374151;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .top-navbar {
                padding: 15px 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div style="width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 15px; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                <i class="fas fa-graduation-cap" style="color: white; font-size: 28px;"></i>
            </div>
            <h3>Admin KSE</h3>
            <p>Sistem Kehadiran Beasiswa</p>
        </div>
        
        <div class="sidebar-menu">
            <a href="../admin2.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'admin2.php') ? 'class="active"' : ''; ?>>
                <i class="fas fa-tachometer-alt"></i>Dashboard
            </a>
            <a href="../datakaryawan.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'datakaryawan.php') ? 'class="active"' : ''; ?>>
                <i class="fas fa-users"></i>Data Beswan
            </a>
            <a href="../data_absen.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'data_absen.php') ? 'class="active"' : ''; ?>>
                <i class="fas fa-calendar-check"></i>Data Absensi
            </a>
            <a href="../datauser.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'datauser.php') ? 'class="active"' : ''; ?>>
                <i class="fas fa-user-shield"></i>Data Admin
            </a>
            <a href="../datajabatan.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'datajabatan.php') ? 'class="active"' : ''; ?>>
                <i class="fas fa-sitemap"></i>Data Divisi
            </a>
            <a href="../data_keterangan.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'data_keterangan.php') ? 'class="active"' : ''; ?>>
                <i class="fas fa-clipboard-list"></i>Data Keterangan
            </a>
            <a href="../export.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'export.php') ? 'class="active"' : ''; ?>>
                <i class="fas fa-download"></i>Export Data
            </a>
            <div style="margin-top: 30px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 20px;">
                <a href="../logout.php" style="color: #fca5a5;">
                    <i class="fas fa-sign-out-alt"></i>Logout
                </a>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <div class="top-navbar">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="fw-bold text-dark mb-0"><?php echo isset($page_title) ? $page_title : "Dashboard Admin"; ?></h2>
                <div class="d-flex align-items-center">
                    <span class="text-muted me-3"><?php echo date('d M Y, H:i'); ?></span>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-user-circle text-primary me-2" style="font-size: 24px;"></i>
                        <span class="fw-medium"><?php echo htmlspecialchars($username); ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="container-fluid px-4">