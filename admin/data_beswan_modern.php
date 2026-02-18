<?php
session_start();

// Cek apakah user sudah login sebagai admin
if (!isset($_SESSION['username']) || !isset($_SESSION['level']) || $_SESSION['level'] !== 'admin') {
    header("location: ../login.php");
    exit();
}

include '../koneksi.php';

// Handle CRUD operations
$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $id_karyawan = mysqli_real_escape_string($koneksi, $_POST['id_karyawan']);
                $username = mysqli_real_escape_string($koneksi, $_POST['username']);
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
                $tmp_tgl_lahir = mysqli_real_escape_string($koneksi, $_POST['tmp_tgl_lahir']);
                $jenkel = mysqli_real_escape_string($koneksi, $_POST['jenkel']);
                $agama = mysqli_real_escape_string($koneksi, $_POST['agama']);
                $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
                $no_tel = mysqli_real_escape_string($koneksi, $_POST['no_tel']);
                $jabatan = mysqli_real_escape_string($koneksi, $_POST['jabatan']);

                // Cek apakah ID sudah ada
                $check_sql = "SELECT id_karyawan FROM tb_karyawan WHERE id_karyawan='$id_karyawan'";
                $check_result = mysqli_query($koneksi, $check_sql);

                if (mysqli_num_rows($check_result) > 0) {
                    $response['success'] = false;
                    $response['message'] = "ID Beswan sudah digunakan!";
                }
                else {
                    $sql = "INSERT INTO tb_karyawan (id_karyawan, username, password, nama, tmp_tgl_lahir, jenkel, agama, alamat, no_tel, jabatan) 
                            VALUES ('$id_karyawan', '$username', '$password', '$nama', '$tmp_tgl_lahir', '$jenkel', '$agama', '$alamat', '$no_tel', '$jabatan')";

                    if (mysqli_query($koneksi, $sql)) {
                        $response['success'] = true;
                        $response['message'] = "Data beswan berhasil ditambahkan!";
                    }
                    else {
                        $response['success'] = false;
                        $response['message'] = "Error: " . mysqli_error($koneksi);
                    }
                }
                break;

            case 'edit':
                $id_karyawan = mysqli_real_escape_string($koneksi, $_POST['id_karyawan']);
                $username = mysqli_real_escape_string($koneksi, $_POST['username']);
                $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
                $tmp_tgl_lahir = mysqli_real_escape_string($koneksi, $_POST['tmp_tgl_lahir']);
                $jenkel = mysqli_real_escape_string($koneksi, $_POST['jenkel']);
                $agama = mysqli_real_escape_string($koneksi, $_POST['agama']);
                $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
                $no_tel = mysqli_real_escape_string($koneksi, $_POST['no_tel']);
                $jabatan = mysqli_real_escape_string($koneksi, $_POST['jabatan']);

                $sql = "UPDATE tb_karyawan SET 
                        username='$username', nama='$nama', tmp_tgl_lahir='$tmp_tgl_lahir', 
                        jenkel='$jenkel', agama='$agama', alamat='$alamat', no_tel='$no_tel', jabatan='$jabatan'
                        WHERE id_karyawan='$id_karyawan'";

                if (mysqli_query($koneksi, $sql)) {
                    $response['success'] = true;
                    $response['message'] = "Data beswan berhasil diupdate!";
                }
                else {
                    $response['success'] = false;
                    $response['message'] = "Error: " . mysqli_error($koneksi);
                }
                break;

            case 'delete':
                $id_karyawan = mysqli_real_escape_string($koneksi, $_POST['id_karyawan']);

                // Hapus data absensi terkait terlebih dahulu
                $delete_absen = "DELETE FROM tb_absensi WHERE id_karyawan='$id_karyawan'";
                mysqli_query($koneksi, $delete_absen);

                // Hapus data karyawan
                $sql = "DELETE FROM tb_karyawan WHERE id_karyawan='$id_karyawan'";

                if (mysqli_query($koneksi, $sql)) {
                    $response['success'] = true;
                    $response['message'] = "Data beswan berhasil dihapus!";
                }
                else {
                    $response['success'] = false;
                    $response['message'] = "Error: " . mysqli_error($koneksi);
                }
                break;
        }

        // Set session untuk SweetAlert
        $_SESSION['alert'] = $response;
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Ambil alert dari session
$alert = null;
if (isset($_SESSION['alert'])) {
    $alert = $_SESSION['alert'];
    unset($_SESSION['alert']);
}

// Ambil data karyawan
$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';
$where_clause = '';
if (!empty($search)) {
    $where_clause = "WHERE nama LIKE '%$search%' OR id_karyawan LIKE '%$search%' OR jabatan LIKE '%$search%'";
}

$sql_karyawan = "SELECT * FROM tb_karyawan $where_clause ORDER BY nama";
$result_karyawan = mysqli_query($koneksi, $sql_karyawan);
$total_karyawan = mysqli_num_rows($result_karyawan);

// Ambil data jabatan untuk dropdown
$sql_jabatan = "SELECT DISTINCT jabatan FROM tb_karyawan WHERE jabatan IS NOT NULL AND jabatan != '' ORDER BY jabatan";
$result_jabatan = mysqli_query($koneksi, $sql_jabatan);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Beswan - Admin KSE</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
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
        
        .btn-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
        }
        
        .btn-gradient:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
            color: white;
        }
        
        .table th {
            background: #f8f9fa;
            border-top: none;
            font-weight: 600;
            color: #495057;
        }
        
        .badge-laki {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        }
        
        .badge-perempuan {
            background: linear-gradient(135deg, #ec4899 0%, #be185d 100%);
        }
        
        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
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
    <?php
$base_url = '../';
include '../includes/sidebar_admin.php';
?>

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
                    <h2 class="fw-bold text-dark mb-1">Data Beswan KSE</h2>
                    <p class="text-muted mb-0">Manajemen data beasiswa mahasiswa KSE UINSU</p>
                </div>
                <div>
                     <span class="badge bg-primary rounded-pill px-4 py-2" style="font-size: 14px;">
                        <i class="fas fa-users me-2"></i><?php echo $total_karyawan; ?> Beswan
                    </span>
                </div>
            </div>

            <!-- Action Card -->
            <div class="card border-0 shadow-sm rounded-3 mb-4 overflow-hidden">
                <div class="card-body p-0">
                    <div class="row g-0">
                        <div class="col-lg-8 p-4">
                            <h5 class="mb-3 fw-bold"><i class="fas fa-search me-2 text-primary"></i>Pencarian Beswan</h5>
                            <form method="GET" class="row g-2">
                                <div class="col-md-8">
                                    <div class="search-box position-relative">
                                        <i class="fas fa-search search-icon"></i>
                                        <input type="text" class="form-control border-0 bg-light py-2 ps-5" name="search" 
                                               placeholder="Cari nama, ID, atau jabatan..." 
                                               value="<?php echo htmlspecialchars($search); ?>">
                                    </div>
                                </div>
                                <div class="col-8 col-md-3">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-search me-2"></i>Cari
                                    </button>
                                </div>
                                <div class="col-4 col-md-1">
                                    <a href="data_beswan_modern.php" class="btn btn-light w-100" title="Reset">
                                        <i class="fas fa-redo"></i>
                                    </a>
                                </div>
                            </form>
                        </div>
                        <div class="col-lg-4 bg-primary p-4 d-flex align-items-center justify-content-center">
                            <div class="text-center text-white">
                                <p class="mb-2 opacity-75 small uppercase fw-bold">Aksi Beswan</p>
                                <button class="btn btn-light text-primary px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#addModal">
                                    <i class="fas fa-plus-circle me-2"></i>Tambah Beswan Baru
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <!-- Data Table -->
        <div class="data-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0">
                    <i class="fas fa-table me-2"></i>Daftar Beswan
                    <?php if (!empty($search)): ?>
                        - Hasil pencarian: "<?php echo htmlspecialchars($search); ?>"
                    <?php
endif; ?>
                </h5>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover" id="dataTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID Beswan</th>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Tempat, Tgl Lahir</th>
                            <th>Jenis Kelamin</th>
                            <th>Agama</th>
                            <th>No. Telepon</th>
                            <th>Jabatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
$no = 1;
while ($row = mysqli_fetch_assoc($result_karyawan)):
?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><strong><?php echo $row['id_karyawan']; ?></strong></td>
                            <td><?php echo $row['nama']; ?></td>
                            <td><?php echo $row['username']; ?></td>
                            <td><?php echo $row['tmp_tgl_lahir']; ?></td>
                            <td>
                                <span class="badge <?php echo $row['jenkel'] == 'Laki-laki' ? 'badge-laki' : 'badge-perempuan'; ?>">
                                    <?php echo $row['jenkel']; ?>
                                </span>
                            </td>
                            <td><?php echo $row['agama']; ?></td>
                            <td><?php echo $row['no_tel']; ?></td>
                            <td>
                                <span class="badge bg-info"><?php echo $row['jabatan']; ?></span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-warning me-1" 
                                        onclick="editBeswan('<?php echo $row['id_karyawan']; ?>')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" 
                                        onclick="deleteBeswan('<?php echo $row['id_karyawan']; ?>', '<?php echo $row['nama']; ?>')">
                                    <i class="fas fa-trash"></i>
                                </button>
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

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Tambah Beswan Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">ID Beswan</label>
                                <input type="text" class="form-control" name="id_karyawan" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" name="username" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" name="nama" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tempat, Tanggal Lahir</label>
                                <input type="text" class="form-control" name="tmp_tgl_lahir" placeholder="Jakarta, 01 Januari 1990">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jenis Kelamin</label>
                                <select class="form-select" name="jenkel" required>
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Agama</label>
                                <select class="form-select" name="agama">
                                    <option value="">Pilih Agama</option>
                                    <option value="Islam">Islam</option>
                                    <option value="Kristen">Kristen</option>
                                    <option value="Katolik">Katolik</option>
                                    <option value="Hindu">Hindu</option>
                                    <option value="Buddha">Buddha</option>
                                    <option value="Konghucu">Konghucu</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">No. Telepon</label>
                                <input type="text" class="form-control" name="no_tel">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jabatan</label>
                                <input type="text" class="form-control" name="jabatan" placeholder="Mahasiswa S1, Mahasiswa S2, dll">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Alamat</label>
                                <textarea class="form-control" name="alamat" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-gradient">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Data Beswan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="editForm">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id_karyawan" id="edit_id_karyawan">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">ID Beswan</label>
                                <input type="text" class="form-control" id="edit_id_display" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" name="username" id="edit_username" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" name="nama" id="edit_nama" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tempat, Tanggal Lahir</label>
                                <input type="text" class="form-control" name="tmp_tgl_lahir" id="edit_tmp_tgl_lahir">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jenis Kelamin</label>
                                <select class="form-select" name="jenkel" id="edit_jenkel" required>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Agama</label>
                                <select class="form-select" name="agama" id="edit_agama">
                                    <option value="Islam">Islam</option>
                                    <option value="Kristen">Kristen</option>
                                    <option value="Katolik">Katolik</option>
                                    <option value="Hindu">Hindu</option>
                                    <option value="Buddha">Buddha</option>
                                    <option value="Konghucu">Konghucu</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">No. Telepon</label>
                                <input type="text" class="form-control" name="no_tel" id="edit_no_tel">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Jabatan</label>
                                <input type="text" class="form-control" name="jabatan" id="edit_jabatan">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Alamat</label>
                                <textarea class="form-control" name="alamat" id="edit_alamat" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-gradient">Update Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Form -->
    <form method="POST" id="deleteForm" style="display: none;">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id_karyawan" id="delete_id_karyawan">
    </form>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json"
                },
                "pageLength": 25,
                "order": [[ 2, "asc" ]],
                "columnDefs": [
                    { "orderable": false, "targets": [0, 9] }
                ]
            });
            
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
        });

        // Toggle Sidebar Script
        const sidebarToggle = document.getElementById("sidebarToggle");
        if (sidebarToggle) {
            sidebarToggle.addEventListener("click", function() {
                document.body.classList.toggle("sb-sidenav-toggled");
                const isToggled = document.body.classList.contains("sb-sidenav-toggled");
                document.cookie = "sidebar_toggle=" + isToggled + "; path=/";
            });
        }

        // Handle Add Form Submit
        $('#addModal form').on('submit', function(e) {
            e.preventDefault();
            
            const formData = $(this).serialize();
            
            Swal.fire({
                title: 'Menyimpan Data...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            $.ajax({
                url: '',
                type: 'POST',
                data: formData,
                success: function(response) {
                    window.location.reload();
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Terjadi kesalahan saat menyimpan data',
                        confirmButtonColor: '#667eea'
                    });
                }
            });
        });

        // Handle Edit Form Submit
        $('#editModal form').on('submit', function(e) {
            e.preventDefault();
            
            const formData = $(this).serialize();
            
            Swal.fire({
                title: 'Mengupdate Data...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            $.ajax({
                url: '',
                type: 'POST',
                data: formData,
                success: function(response) {
                    window.location.reload();
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Terjadi kesalahan saat mengupdate data',
                        confirmButtonColor: '#667eea'
                    });
                }
            });
        });

        function editBeswan(id) {
            // Fetch data via AJAX
            $.get('get_beswan_data.php?id=' + id, function(data) {
                const beswan = JSON.parse(data);
                
                $('#edit_id_karyawan').val(beswan.id_karyawan);
                $('#edit_id_display').val(beswan.id_karyawan);
                $('#edit_username').val(beswan.username);
                $('#edit_nama').val(beswan.nama);
                $('#edit_tmp_tgl_lahir').val(beswan.tmp_tgl_lahir);
                $('#edit_jenkel').val(beswan.jenkel);
                $('#edit_agama').val(beswan.agama);
                $('#edit_no_tel').val(beswan.no_tel);
                $('#edit_jabatan').val(beswan.jabatan);
                $('#edit_alamat').val(beswan.alamat);
                
                $('#editModal').modal('show');
            }).fail(function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Tidak dapat mengambil data beswan',
                    confirmButtonColor: '#667eea'
                });
            });
        }

        function deleteBeswan(id, nama) {
            Swal.fire({
                title: 'Hapus Data Beswan?',
                html: `Apakah Anda yakin ingin menghapus data:<br><strong>${nama}</strong>?<br><br><small class="text-danger">Data absensi terkait juga akan dihapus!</small>`,
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
                    
                    $('#delete_id_karyawan').val(id);
                    $('#deleteForm').submit();
                }
            });
                }
            });
        }

    </script>
</body>
</html>