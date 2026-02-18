<?php
// Session sudah di-start di index.php
$page_title = "Profil Beswan";
include "header.php";

// Ambil data profil
$id = $_SESSION['idsi'];
$sql = "SELECT * FROM tb_karyawan WHERE id_karyawan = ?";
$stmt = mysqli_prepare($koneksi, $sql);
mysqli_stmt_bind_param($stmt, "s", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$r = mysqli_fetch_array($result);

if (!$r) {
    echo "<script>Swal.fire('Error', 'Data profil tidak ditemukan', 'error').then(() => { window.location.href = 'index.php?m=awal'; });</script>";
}

// Parse tanggal lahir
$tmp_tgl_lahir = explode(" / ", $r['tmp_tgl_lahir']);
$tempat_lahir = $tmp_tgl_lahir[0] ?? '-';
$tanggal_lahir = $tmp_tgl_lahir[1] ?? date('d-m-Y');
?>

<style>
    .profile-container {
        max-width: 900px;
        margin: 0 auto;
    }
    
    .profile-header-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 25px;
        padding: 40px;
        text-align: center;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(102,126,234,0.2);
    }
    
    .profile-avatar {
        width: 140px;
        height: 140px;
        border-radius: 50%;
        border: 5px solid rgba(255,255,255,0.2);
        object-fit: cover;
        margin-bottom: 20px;
        background: #fff;
    }

    .info-card {
        background: #fff;
        border-radius: 25px;
        border: none;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        overflow: hidden;
    }

    .info-item {
        padding: 20px 30px;
        border-bottom: 1px solid #f8fafc;
        display: flex;
        align-items: center;
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .info-icon {
        width: 45px;
        height: 45px;
        background: #f1f5f9;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 20px;
        color: #4f46e5;
    }

    .info-label {
        font-size: 0.8rem;
        color: #64748b;
        margin-bottom: 2px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-value {
        font-weight: 600;
        color: #1e293b;
        font-size: 1rem;
    }
</style>

<div class="profile-container">
    <div class="profile-header-card">
        <img src="../images/<?php echo !empty($r['foto']) ? htmlspecialchars($r['foto']) : 'default.jpg'; ?>" alt="Profil" class="profile-avatar">
        <h2 class="fw-bold mb-1"><?php echo htmlspecialchars($r['nama']); ?></h2>
        <p class="opacity-75 mb-3"><?php echo htmlspecialchars($r['jabatan']); ?> • <?php echo htmlspecialchars($r['id_karyawan']); ?></p>
        <div class="d-flex justify-content-center gap-2">
            <span class="badge rounded-pill bg-white text-primary px-3 py-2 fw-bold">Beswan Aktif</span>
            <a href="index.php?m=karyawan&s=edit" class="btn btn-outline-light rounded-pill btn-sm px-4">Edit Profil</a>
        </div>
    </div>

    <div class="info-card">
        <div class="info-item">
            <div class="info-icon"><i class="fas fa-id-badge"></i></div>
            <div>
                <div class="info-label">Nomor KSE</div>
                <div class="info-value"><?php echo htmlspecialchars($r['id_karyawan']); ?></div>
            </div>
        </div>
        <div class="info-item">
            <div class="info-icon"><i class="fas fa-envelope"></i></div>
            <div>
                <div class="info-label">Tempat / Tanggal Lahir</div>
                <div class="info-value"><?php echo htmlspecialchars($tempat_lahir); ?>, <?php echo htmlspecialchars($tanggal_lahir); ?></div>
            </div>
        </div>
        <div class="info-item">
            <div class="info-icon"><i class="fas fa-venus-mars"></i></div>
            <div>
                <div class="info-label">Jenis Kelamin</div>
                <div class="info-value"><?php echo htmlspecialchars($r['jenkel']); ?></div>
            </div>
        </div>
        <div class="info-item">
            <div class="info-icon"><i class="fas fa-phone-alt"></i></div>
            <div>
                <div class="info-label">Nomor Handphone</div>
                <div class="info-value"><?php echo htmlspecialchars($r['no_tel']); ?></div>
            </div>
        </div>
        <div class="info-item">
            <div class="info-icon"><i class="fas fa-map-marked-alt"></i></div>
            <div>
                <div class="info-label">Alamat Lengkap</div>
                <div class="info-value"><?php echo htmlspecialchars($r['alamat']); ?></div>
            </div>
        </div>
        <div class="info-item">
            <div class="info-icon"><i class="fas fa-briefcase"></i></div>
            <div>
                <div class="info-label">Jabatan di Paguyuban</div>
                <div class="info-value"><?php echo htmlspecialchars($r['jabatan']); ?></div>
            </div>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>