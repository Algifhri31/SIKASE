<?php
session_start();
require_once 'koneksi.php';

// Cek login dan role
if (!isset($_SESSION['username']) || !isset($_SESSION['level']) || $_SESSION['level'] !== 'admin') {
    header("location: login.php");
    exit();
}

// Cek apakah Super Admin
$is_super_admin = (isset($_SESSION['role']) && $_SESSION['role'] === 'super_admin');

if (!$is_super_admin) {
    echo "<script>alert('Anda tidak memiliki akses ke halaman ini!'); window.location.href='admin_dashboard_fixed.php';</script>";
    exit();
}

$username = $_SESSION['username'];

// Handle CRUD dengan session alert
$alert = null;

// Handle Add
if (isset($_POST['add_divisi'])) {
    $jabatan = mysqli_real_escape_string($koneksi, $_POST['jabatan']);

    // Cek duplikasi
    $check = mysqli_query($koneksi, "SELECT * FROM tb_jabatan WHERE jabatan = '$jabatan'");
    if (mysqli_num_rows($check) > 0) {
        $alert = ['success' => false, 'message' => 'Divisi sudah ada!'];
    }
    else {
        $query = "INSERT INTO tb_jabatan (jabatan) VALUES ('$jabatan')";
        if (mysqli_query($koneksi, $query)) {
            $alert = ['success' => true, 'message' => 'Divisi berhasil ditambahkan!'];
        }
        else {
            $alert = ['success' => false, 'message' => 'Gagal menambahkan divisi: ' . mysqli_error($koneksi)];
        }
    }

    $_SESSION['alert'] = $alert;
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle Edit
if (isset($_POST['edit_divisi'])) {
    $id = (int)$_POST['id'];
    $jabatan = mysqli_real_escape_string($koneksi, $_POST['jabatan']);

    $query = "UPDATE tb_jabatan SET jabatan = '$jabatan' WHERE id = $id";
    if (mysqli_query($koneksi, $query)) {
        $alert = ['success' => true, 'message' => 'Divisi berhasil diupdate!'];
    }
    else {
        $alert = ['success' => false, 'message' => 'Gagal mengupdate divisi: ' . mysqli_error($koneksi)];
    }

    $_SESSION['alert'] = $alert;
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    // Cek apakah divisi digunakan
    $check = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_karyawan WHERE jabatan = (SELECT jabatan FROM tb_jabatan WHERE id = $id)");
    $result = mysqli_fetch_assoc($check);

    if ($result['total'] > 0) {
        $alert = ['success' => false, 'message' => 'Divisi tidak dapat dihapus karena masih digunakan oleh ' . $result['total'] . ' beswan!'];
    }
    else {
        $query = "DELETE FROM tb_jabatan WHERE id = $id";
        if (mysqli_query($koneksi, $query)) {
            $alert = ['success' => true, 'message' => 'Divisi berhasil dihapus!'];
        }
        else {
            $alert = ['success' => false, 'message' => 'Gagal menghapus divisi: ' . mysqli_error($koneksi)];
        }
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

// Fetch Divisi
$divisi_list = mysqli_query($koneksi, "SELECT j.*, COUNT(k.id_karyawan) as jumlah_beswan 
                                        FROM tb_jabatan j 
                                        LEFT JOIN tb_karyawan k ON j.jabatan = k.jabatan 
                                        GROUP BY j.id 
                                        ORDER BY j.jabatan ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Divisi - Admin KSE</title>
    
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
        
        .bg-soft-primary {
            background: #eef2ff;
        }
        
        .bg-light-blue {
            background: #f0f7ff;
        }

        .hover-lift {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .hover-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
        }

        .icon-shape {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        @media (max-width: 768px) {
            .container-fluid {
                padding-left: 15px;
                padding-right: 15px;
            }
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
        
        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .divisi-card {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }
        
        .divisi-card:hover {
            border-color: #667eea;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.1);
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
                        <li><a class="dropdown-item" href="#" onclick="confirmLogout(event)"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container-fluid px-4">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <div>
                    <h2 class="fw-bold text-dark mb-1">Data Divisi</h2>
                    <p class="text-muted mb-0">Kelola divisi/jabatan untuk beswan KSE</p>
                </div>
                <div>
                    <button class="btn btn-primary px-4 shadow-sm w-100 w-md-auto" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="fas fa-plus me-2"></i>Tambah Divisi
                    </button>
                </div>
            </div>

            <!-- Info Card -->
            <div class="card border-0 bg-light-blue shadow-sm mb-4">
                <div class="card-body py-2 px-3">
                    <small class="text-primary fw-bold">
                        <i class="fas fa-info-circle me-1"></i>
                        Divisi yang ditambahkan di sini akan muncul sebagai pilihan saat pendaftaran beswan baru.
                    </small>
                </div>
            </div>

        <!-- Data Cards -->
        <div class="row g-3">
            <div class="col-12">
                <div class="d-flex align-items-center mb-1">
                    <h5 class="fw-bold text-dark mb-0"><i class="fas fa-list me-2 text-primary"></i>Daftar Divisi</h5>
                    <span class="ms-auto badge bg-light text-dark shadow-sm border"><?php echo mysqli_num_rows($divisi_list); ?> Divisi</span>
                </div>
            </div>
            
            <?php if (mysqli_num_rows($divisi_list) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($divisi_list)): ?>
                <div class="col-sm-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100 position-relative hover-lift">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="icon-shape bg-soft-primary text-primary rounded-3 p-2 me-3">
                                    <i class="fas fa-folder-open fa-lg"></i>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm rounded-circle shadow-sm" type="button" data-bs-toggle="dropdown" style="width: 32px; height: 32px; padding: 0;">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                        <li>
                                            <a class="dropdown-item" href="#" onclick="editDivisi(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['jabatan']); ?>')">
                                                <i class="fas fa-edit me-2 text-warning"></i>Edit
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item delete-btn text-danger" href="?delete=<?php echo $row['id']; ?>" 
                                               data-id="<?php echo $row['id']; ?>"
                                               data-nama="<?php echo htmlspecialchars($row['jabatan']); ?>"
                                               data-jumlah="<?php echo $row['jumlah_beswan']; ?>">
                                                <i class="fas fa-trash me-2"></i>Hapus
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <h6 class="fw-bold mb-1 text-truncate"><?php echo htmlspecialchars($row['jabatan']); ?></h6>
                            <div class="d-flex align-items-center text-muted small">
                                <i class="fas fa-users me-2"></i>
                                <span><?php echo $row['jumlah_beswan']; ?> Beswan Terdaftar</span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
    endwhile; ?>
            <?php
else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-sitemap fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Belum ada divisi</h5>
                    <p class="text-muted">Klik tombol "Tambah Divisi" untuk menambahkan divisi baru</p>
                </div>
            <?php
endif; ?>
        </div>
    </div>
</div>

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Tambah Divisi Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Divisi/Jabatan</label>
                            <input type="text" name="jabatan" class="form-control" placeholder="Contoh: Mahasiswa S1, Mahasiswa S2, Staff, dll" required>
                            <small class="text-muted">Divisi ini akan muncul di form pendaftaran beswan</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="add_divisi" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Divisi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Divisi/Jabatan</label>
                            <input type="text" name="jabatan" id="edit_jabatan" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="edit_divisi" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
        
        // Edit function
        function editDivisi(id, jabatan) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_jabatan').value = jabatan;
            new bootstrap.Modal(document.getElementById('editModal')).show();
        }
        
        // Handle delete with SweetAlert
        document.querySelectorAll('.delete-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                
                const nama = this.dataset.nama;
                const jumlah = parseInt(this.dataset.jumlah);
                const url = this.href;
                
                if (jumlah > 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Tidak Dapat Dihapus!',
                        html: `Divisi <strong>${nama}</strong> masih digunakan oleh <strong>${jumlah} beswan</strong>.<br><br>Silakan pindahkan beswan ke divisi lain terlebih dahulu.`,
                        confirmButtonColor: '#667eea'
                    });
                } else {
                    Swal.fire({
                        title: 'Hapus Divisi?',
                        html: `Apakah Anda yakin ingin menghapus divisi:<br><strong>${nama}</strong>?`,
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
                                title: 'Menghapus Divisi...',
                                text: 'Mohon tunggu sebentar',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            
                            window.location.href = url;
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
