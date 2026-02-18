<?php
$page_title = "Edit Profil Beswan";
include "header.php";

$id = $_SESSION['idsi'];
$sql = "SELECT * FROM tb_karyawan WHERE id_karyawan = '$id'";
$query = mysqli_query($koneksi, $sql);
$r = mysqli_fetch_array($query);

if (isset($_POST['simpan'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $tempat_lahir = mysqli_real_escape_string($koneksi, $_POST['tempat_lahir']);
    $tanggal_lahir = mysqli_real_escape_string($koneksi, $_POST['tanggal_lahir']);
    $jenis_kelamin = mysqli_real_escape_string($koneksi, $_POST['jenis_kelamin']);
    $agama = mysqli_real_escape_string($koneksi, $_POST['agama']);
    $no_hp = mysqli_real_escape_string($koneksi, $_POST['no_hp']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);

    // Handle file upload
    $foto = $r['foto'];
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $filename = $_FILES['foto']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $new_filename = date('dmYHis') . "_" . $id . "." . $ext;
            if (move_uploaded_file($_FILES['foto']['tmp_name'], "../images/" . $new_filename)) {
                $foto = $new_filename;
                if ($r['foto'] != 'default.jpg' && !empty($r['foto']) && file_exists("../images/" . $r['foto'])) {
                    unlink("../images/" . $r['foto']);
                }
            }
        }
    }

    $tmp_tgl_lahir = $tempat_lahir . " / " . $tanggal_lahir;

    $sql_update = "UPDATE tb_karyawan SET 
            nama = '$nama',
            tmp_tgl_lahir = '$tmp_tgl_lahir',
            jenkel = '$jenis_kelamin',
            agama = '$agama',
            no_tel = '$no_hp',
            alamat = '$alamat',
            foto = '$foto'
            WHERE id_karyawan = '$id'";

    if (mysqli_query($koneksi, $sql_update)) {
        echo "<script>
            Swal.fire({
                title: 'Berhasil!',
                text: 'Profil Anda telah diperbarui.',
                icon: 'success',
                confirmButtonColor: '#4f46e5'
            }).then(() => {
                window.location.href = '?m=karyawan&s=profil';
            });
        </script>";
    }
    else {
        echo "<script>Swal.fire('Gagal', 'Terjadi kesalahan saat menyimpan data', 'error');</script>";
    }
}
?>

<style>
    .edit-container {
        max-width: 900px;
        margin: 0 auto;
    }
    .edit-card {
        background: #fff;
        border-radius: 25px;
        border: none;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        overflow: hidden;
    }
    .edit-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 30px;
        color: #fff;
        text-align: center;
    }
    .edit-body {
        padding: 40px;
    }
    .photo-preview-container {
        position: relative;
        width: 150px;
        height: 150px;
        margin: 0 auto 30px;
    }
    .photo-preview {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #fff;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .photo-upload-btn {
        position: absolute;
        bottom: 5px;
        right: 5px;
        background: #4f46e5;
        color: #fff;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border: 3px solid #fff;
        transition: all 0.2s;
    }
    .photo-upload-btn:hover {
        transform: scale(1.1);
        background: #4338ca;
    }
    .form-label {
        font-weight: 600;
        color: #475569;
        font-size: 0.9rem;
        margin-bottom: 8px;
    }
    .form-control, .form-select {
        border-radius: 12px;
        padding: 12px 15px;
        border: 1px solid #e2e8f0;
        color: #1e293b;
    }
    .form-control:focus, .form-select:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
    }
</style>

<div class="edit-container">
    <div class="edit-card">
        <div class="edit-header">
            <h4 class="fw-bold mb-0">Perbarui Profil</h4>
            <p class="opacity-75 mb-0 small">Pastikan data yang Anda masukkan sudah benar</p>
        </div>
        
        <div class="edit-body">
            <form action="" method="post" enctype="multipart/form-data">
                <div class="photo-preview-container">
                    <img src="../images/<?php echo !empty($r['foto']) ? $r['foto'] : 'default.jpg'; ?>" id="imgPreview" class="photo-preview" alt="Preview">
                    <label for="fotoInput" class="photo-upload-btn">
                        <i class="fas fa-camera"></i>
                    </label>
                    <input type="file" name="foto" id="fotoInput" class="d-none" accept="image/*" onchange="previewImg(this)">
                </div>

                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label">Nomor KSE (NIK)</label>
                        <input type="text" class="form-control bg-light" value="<?php echo $r['id_karyawan']; ?>" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" value="<?php echo htmlspecialchars($r['nama']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" class="form-control" value="<?php echo htmlspecialchars(explode(" / ", $r['tmp_tgl_lahir'])[0] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" class="form-control" value="<?php
$tgl_raw = explode(" / ", $r['tmp_tgl_lahir'])[1] ?? '';
if (!empty($tgl_raw)) {
    echo date('Y-m-d', strtotime(str_replace('-', '/', $tgl_raw)));
}
?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-select" required>
                            <option value="Laki-laki" <?php echo($r['jenkel'] == 'Laki-laki') ? 'selected' : ''; ?>>Laki-laki</option>
                            <option value="Perempuan" <?php echo($r['jenkel'] == 'Perempuan') ? 'selected' : ''; ?>>Perempuan</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Agama</label>
                        <select name="agama" class="form-select" required>
                            <?php
$agamas = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'];
foreach ($agamas as $a) {
    $sel = ($r['agama'] == $a) ? 'selected' : '';
    echo "<option value=\"$a\" $sel>$a</option>";
}
?>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Nomor Handphone (WhatsApp)</label>
                        <input type="tel" name="no_hp" class="form-control" value="<?php echo htmlspecialchars($r['no_tel']); ?>" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Alamat Lengkap</label>
                        <textarea name="alamat" class="form-control" rows="3" required><?php echo htmlspecialchars($r['alamat']); ?></textarea>
                    </div>
                </div>

                <div class="mt-5 d-flex gap-3">
                    <button type="submit" name="simpan" class="btn btn-primary px-5 py-3 fw-bold rounded-pill">Simpan Perubahan</button>
                    <a href="?m=karyawan&s=profil" class="btn btn-outline-secondary px-5 py-3 fw-bold rounded-pill">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function previewImg(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imgPreview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php include "footer.php"; ?>