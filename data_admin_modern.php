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

// Handle Add Admin
if (isset($_POST['add_admin'])) {
    $new_username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $new_password = mysqli_real_escape_string($koneksi, $_POST['password']);
    $new_role = mysqli_real_escape_string($koneksi, $_POST['role']);

    // Cek username exist
    $check = mysqli_query($koneksi, "SELECT * FROM tb_daftar WHERE username = '$new_username'");
    if (mysqli_num_rows($check) > 0) {
        $alert = ['success' => false, 'message' => 'Username sudah digunakan!'];
    }
    else {
        $query = "INSERT INTO tb_daftar (username, password, role) VALUES ('$new_username', '$new_password', '$new_role')";
        if (mysqli_query($koneksi, $query)) {
            $alert = ['success' => true, 'message' => 'Admin berhasil ditambahkan!'];
        }
        else {
            $alert = ['success' => false, 'message' => 'Gagal menambahkan admin: ' . mysqli_error($koneksi)];
        }
    }

    $_SESSION['alert'] = $alert;
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle Edit Password
if (isset($_POST['edit_password'])) {
    $id = (int)$_POST['id'];
    $new_password = mysqli_real_escape_string($koneksi, $_POST['password']);

    $query = "UPDATE tb_daftar SET password = '$new_password' WHERE id = $id";
    if (mysqli_query($koneksi, $query)) {
        $alert = ['success' => true, 'message' => 'Password berhasil diubah!'];
    }
    else {
        $alert = ['success' => false, 'message' => 'Gagal mengubah password: ' . mysqli_error($koneksi)];
    }

    $_SESSION['alert'] = $alert;
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    // Prevent deleting self
    if ($id != $_SESSION['admin_id']) {
        $query = "DELETE FROM tb_daftar WHERE id = $id";
        if (mysqli_query($koneksi, $query)) {
            $alert = ['success' => true, 'message' => 'Admin berhasil dihapus!'];
        }
        else {
            $alert = ['success' => false, 'message' => 'Gagal menghapus admin: ' . mysqli_error($koneksi)];
        }
    }
    else {
        $alert = ['success' => false, 'message' => 'Tidak dapat menghapus akun sendiri!'];
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

// Fetch Admins
$admins = mysqli_query($koneksi, "SELECT * FROM tb_daftar ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Data Admin - KSE UINSU</title>
    
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
        
        #page-content-wrapper {
            width: 100%;
            min-height: 100vh;
            flex-grow: 1;
            transition: all 0.3s;
        }

        .bg-soft-primary {
            background: #eef2ff;
        }

        .avatar-sm {
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 768px) {
            #page-content-wrapper {
                padding: 10px;
            }
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
                    <h2 class="fw-bold text-dark mb-1">Kelola Data Admin</h2>
                    <p class="text-muted mb-0">Manajemen akun administrator sistem</p>
                </div>
                <div>
                    <button class="btn btn-primary px-4 shadow-sm w-100 w-md-auto" data-bs-toggle="modal" data-bs-target="#addAdminModal">
                        <i class="fas fa-plus me-2"></i>Tambah Admin
                    </button>
                </div>
            </div>

        <?php if ($alert): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: '<?php echo $alert['success'] ? 'success' : 'error'; ?>',
                    title: '<?php echo $alert['success'] ? 'Berhasil!' : 'Gagal!'; ?>',
                    text: '<?php echo addslashes($alert['message']); ?>',
                    confirmButtonColor: '#667eea',
                    timer: 3000
                });
            });
        </script>
        <?php
endif; ?>

        <div class="card card-custom border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 px-4 border-bottom">
                <div class="d-flex align-items-center">
                    <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-user-shield me-2 text-primary"></i>Daftar Administrator</h5>
                    <span class="ms-auto badge bg-soft-primary text-primary px-3 rounded-pill"><?php echo mysqli_num_rows($admins); ?> Admin</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 text-muted small fw-bold">NO</th>
                                <th class="text-muted small fw-bold">USERNAME</th>
                                <th class="text-muted small fw-bold">PASSWORD</th>
                                <th class="text-muted small fw-bold text-center">ROLE</th>
                                <th class="text-end pe-4 text-muted small fw-bold">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
$no = 1;
mysqli_data_seek($admins, 0);
while ($row = mysqli_fetch_assoc($admins)):
    $is_self = ($row['id'] == $_SESSION['admin_id']);
    $is_super = ($row['role'] == 'super_admin');
?>
                            <tr>
                                <td class="ps-4"><?php echo $no++; ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-soft-primary text-primary rounded-circle me-3 d-none d-sm-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                            <i class="fas fa-user small"></i>
                                        </div>
                                        <span class="fw-bold"><?php echo htmlspecialchars($row['username']); ?></span>
                                    </div>
                                </td>
                                <td><code class="text-muted small"><?php echo htmlspecialchars($row['password']); ?></code></td>
                                <td class="text-center">
                                    <span class="badge rounded-pill px-3 <?php echo $is_super ? 'bg-primary' : 'bg-light text-dark border'; ?>" style="font-size: 11px;">
                                        <?php echo $is_super ? 'Super Admin' : 'Admin Biasa'; ?>
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-light border shadow-sm me-1" 
                                                onclick="editPassword(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['username']); ?>')"
                                                title="Ubah Password">
                                            <i class="fas fa-key text-warning"></i>
                                        </button>
                                        
                                        <?php if (!$is_self): ?>
                                        <a href="?delete=<?php echo $row['id']; ?>" 
                                           class="btn btn-sm btn-light border shadow-sm delete-btn"
                                           data-id="<?php echo $row['id']; ?>"
                                           data-username="<?php echo htmlspecialchars($row['username']); ?>"
                                           title="Hapus Admin">
                                            <i class="fas fa-trash text-danger"></i>
                                        </a>
                                        <?php
    endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php
endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Modal Tambah Admin -->
    <div class="modal fade" id="addAdminModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Admin Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="text" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-select">
                                <option value="admin">Admin Biasa (Hanya Absensi)</option>
                                <option value="super_admin">Super Admin (Akses Penuh)</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="add_admin" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Password -->
    <div class="modal fade" id="editPasswordModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Ubah Password - <span id="editUsername"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="" method="POST">
                    <input type="hidden" name="id" id="editId">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Password Baru</label>
                            <input type="text" name="password" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="edit_password" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        function editPassword(id, username) {
            document.getElementById('editId').value = id;
            document.getElementById('editUsername').textContent = username;
            new bootstrap.Modal(document.getElementById('editPasswordModal')).show();
        }
        
        // Handle delete with SweetAlert
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.delete-btn').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const username = this.dataset.username;
                    const url = this.href;
                    
                    Swal.fire({
                        title: 'Hapus Admin?',
                        html: `Apakah Anda yakin ingin menghapus admin:<br><strong>${username}</strong>?`,
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
                                title: 'Menghapus Admin...',
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
        });
    </script>
</body>
</html>
