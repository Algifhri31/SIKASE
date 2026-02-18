<?php
$page_title = "Profil Beswan";
include "header.php";

// Ambil data profil
$id = $_SESSION['idsi'];
$sql = "SELECT 
    id_karyawan as nik,
    nama,
    SUBSTRING_INDEX(tmp_tgl_lahir, ' / ', 1) as tempat_lahir,
    SUBSTRING_INDEX(tmp_tgl_lahir, ' / ', -1) as tanggal_lahir,
    jenkel as jenis_kelamin,
    agama,
    no_tel as no_hp,
    COALESCE(email, '-') as email,
    alamat,
    jabatan,
    foto,
    status
FROM tb_karyawan WHERE id_karyawan = '$id'";
$query = mysqli_query($koneksi, $sql);
$r = mysqli_fetch_array($query);

// Set default value jika data kosong
$r['nik'] = isset($r['nik']) ? $r['nik'] : '-';
$r['nama'] = isset($r['nama']) ? $r['nama'] : $_SESSION['namasi'];
$r['tempat_lahir'] = isset($r['tempat_lahir']) ? $r['tempat_lahir'] : '-';
$r['tanggal_lahir'] = isset($r['tanggal_lahir']) ? $r['tanggal_lahir'] : date('Y-m-d');
$r['jenis_kelamin'] = isset($r['jenis_kelamin']) ? $r['jenis_kelamin'] : '-';
$r['agama'] = isset($r['agama']) ? $r['agama'] : '-';
$r['no_hp'] = isset($r['no_hp']) ? $r['no_hp'] : '-';
$r['email'] = '-'; // Email tidak ada di tabel
$r['alamat'] = isset($r['alamat']) ? $r['alamat'] : '-';
$r['jabatan'] = isset($r['jabatan']) ? $r['jabatan'] : '-';
$r['foto'] = isset($r['foto']) && !empty($r['foto']) ? $r['foto'] : 'default.jpg';
?>

<div class="main-content">
    <div class="section__content section__content--p30">
        <div class="container-fluid">
            <div class="card" style="border-radius: 15px; border: none; box-shadow: 0 0 20px rgba(0,0,0,0.08);">
                <div class="card-header bg-white" style="border-bottom: 1px solid #eef0f7; padding: 20px 25px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0" style="color: #2b3144; font-weight: 600;">Profil Beswan</h4>
                        <a href="?m=karyawan&s=edit" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Edit Profil
                        </a>
                    </div>
                </div>
                <div class="card-body" style="padding: 25px;">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <div class="position-relative d-inline-block mb-4">
                                <img src="../images/<?php echo $r['foto']; ?>" alt="Foto Profil" class="img-fluid rounded-circle" style="width: 200px; height: 200px; object-fit: cover; border: 4px solid #fff; box-shadow: 0 0 20px rgba(0,0,0,0.1);">
                                <div class="position-absolute bottom-0 end-0 bg-success rounded-circle p-1" style="width: 20px; height: 20px; border: 3px solid #fff;"></div>
                            </div>
                            <h5 class="mb-2" style="color: #2b3144; font-weight: 600;"><?php echo $r['nama']; ?></h5>
                            <p class="text-muted mb-2"><?php echo $r['jabatan']; ?></p>
                            <span class="badge bg-info text-white" style="font-weight: 500;"><?php echo $r['status']; ?></span>
                        </div>
                        <div class="col-md-8">
                            <div class="table-responsive">
                                <table class="table table-borderless" style="color: #4f5d77;">
                                    <colgroup>
                                        <col width="30%">
                                        <col width="70%">
                                    </colgroup>
                                    <tr>
                                        <th class="fw-500">NIK</th>
                                        <td class="fw-600" style="color: #2b3144;"><?php echo $r['nik']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Nama</th>
                                        <td>: <?php echo $r['nama']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Tempat Lahir</th>
                                        <td>: <?php echo $r['tempat_lahir']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Lahir</th>
                                        <td>: <?php echo isset($r['tanggal_lahir']) ? date('d-m-Y', strtotime($r['tanggal_lahir'])) : '-'; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Jenis Kelamin</th>
                                        <td>: <?php echo $r['jenis_kelamin']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Agama</th>
                                        <td>: <?php echo $r['agama']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>No. HP</th>
                                        <td>: <?php echo $r['no_hp']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td>: <?php echo $r['email']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Alamat</th>
                                        <td>: <?php echo $r['alamat']; ?></td>
                                    </tr>
                                </table>
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
    .fw-500 {
        font-weight: 500 !important;
    }
    
    .fw-600 {
        font-weight: 600 !important;
    }
    
    .table > tbody > tr > th,
    .table > tbody > tr > td {
        padding: 12px 0;
        border: none;
        vertical-align: middle;
    }
    
    .table > tbody > tr > td {
        padding-left: 15px;
    }
    
    .badge {
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
    }
    
    .btn-primary {
        background: #3b82f6;
        border: none;
        padding: 8px 16px;
        font-size: 14px;
        font-weight: 500;
        border-radius: 6px;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        background: #2563eb;
        transform: translateY(-1px);
    }
    
    .btn-primary i {
        margin-right: 6px;
    }
</style>