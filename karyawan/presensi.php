<?php
// Session sudah di-start di index.php
$page_title = "Presensi Digital";
include "header.php";

// Enable error display untuk debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Koneksi sudah di-include di sesi_karyawan.php
if (!isset($koneksi)) {
    include '../koneksi.php';
}

include 'helper_tanggal_new.php';
date_default_timezone_set('Asia/Jakarta');

// Inisialisasi variable
$id = $_SESSION['idsi'];
$save = false;

$today = date('Y-m-d');
// Query untuk cek absensi hari ini di tb_absen
$sqls = "SELECT * FROM tb_absen WHERE id_karyawan = '$id' AND DATE(waktu) = '$today' ORDER BY waktu DESC LIMIT 1";
$querys = mysqli_query($koneksi, $sqls);
$rz = mysqli_fetch_array($querys);

// Query untuk cek keterangan hari ini di tb_keterangan (Izin/Sakit)
$sql_ket = "SELECT * FROM tb_keterangan WHERE id_karyawan = '$id' AND DATE(waktu) = '$today' ORDER BY waktu DESC LIMIT 1";
$query_ket = mysqli_query($koneksi, $sql_ket);
$rz_ket = mysqli_fetch_array($query_ket);

// Cek apakah sudah ada absensi atau keterangan hari ini
$sudah_absen = !empty($rz);

$sudah_keterangan = !empty($rz_ket);
$tipe_absen = $sudah_absen ? $rz['tipe'] : '';
$tipe_keterangan = $sudah_keterangan ? $rz_ket['keterangan'] : '';

// Proses form jika ada submit
if (isset($_POST['simpan'])) {

    // Cek lagi apakah sudah ada absensi/keterangan hari ini
    $check_absen = mysqli_query($koneksi, "SELECT * FROM tb_absen WHERE id_karyawan = '$id' AND DATE(waktu) = '$today'");
    $check_ket = mysqli_query($koneksi, "SELECT * FROM tb_keterangan WHERE id_karyawan = '$id' AND DATE(waktu) = '$today'");

    if (mysqli_num_rows($check_absen) > 0 || mysqli_num_rows($check_ket) > 0) {
        $_SESSION['alert_success'] = false;
        $_SESSION['alert_message'] = "Gagal!";
        $_SESSION['alert_text'] = "Anda sudah melakukan absensi atau mengirim keterangan untuk hari ini.";
        header("Location: index.php?m=presensi");
        exit();
    }
    else {

        $id_karyawan = $_POST['id_karyawan'];
        $nama = $_POST['nama'];
        $waktu = date('Y-m-d H:i:s');
        $tipe = $_POST['tipe'];
        $status_kehadiran = $_POST['status_kehadiran'];
        $kegiatan = htmlspecialchars($_POST['kegiatan']);
        $alasan = htmlspecialchars($_POST['alasan']);
        $lokasi = isset($_POST['lokasi']) ? $_POST['lokasi'] : '-';

        $save = false;
        $error_msg = '';

        if ($status_kehadiran == 'Izin' || $status_kehadiran == 'Sakit') {
            // Handle upload bukti
            $bukti = '';
            if (isset($_FILES['bukti']) && $_FILES['bukti']['error'] == 0) {
                $target_dir = __DIR__ . "/../uploads/";
                if (!is_dir($target_dir))
                    mkdir($target_dir, 0755, true);

                $file_extension = strtolower(pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION));
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'pdf'];

                if (in_array($file_extension, $allowed_extensions)) {
                    $bukti = $id_karyawan . '_' . time() . '.' . $file_extension;
                    $target_file = $target_dir . $bukti;
                    move_uploaded_file($_FILES['bukti']['tmp_name'], $target_file);
                }
            }

            $stmt = mysqli_prepare($koneksi, "INSERT INTO tb_keterangan (id_karyawan, nama, keterangan, alasan, waktu, bukti) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ssssss", $id_karyawan, $nama, $status_kehadiran, $alasan, $waktu, $bukti);
                $save = mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }
        else {
            // Hadir
            if (!empty($lokasi) && $lokasi != '-') {
                $stmt = mysqli_prepare($koneksi, "INSERT INTO tb_absen (id_karyawan, nama, waktu, lokasi, kegiatan, keterangan, tipe) VALUES (?, ?, ?, ?, ?, ?, ?)");
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "sssssss", $id_karyawan, $nama, $waktu, $lokasi, $kegiatan, $alasan, $tipe);
                    $save = mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                }
            }
        }

        if ($save) {
            $_SESSION['alert_success'] = true;
            $_SESSION['alert_message'] = "Berhasil!";
            $_SESSION['alert_text'] = "Data presensi Anda telah tersimpan.";
        }
        else {
            $_SESSION['alert_success'] = false;
            $_SESSION['alert_message'] = "Gagal!";
            $_SESSION['alert_text'] = "Terjadi kesalahan saat menyimpan data.";
        }
        header("Location: index.php?m=presensi");
        exit();
    }
}

// Tampilkan alert jika ada
$show_alert = false;
if (isset($_SESSION['alert_success'])) {
    $show_alert = true;
    $alert_success = $_SESSION['alert_success'];
    $alert_message = $_SESSION['alert_message'];
    $alert_text = $_SESSION['alert_text'];
    unset($_SESSION['alert_success'], $_SESSION['alert_message'], $_SESSION['alert_text']);
}

?>
<style>
    .presensi-card {
        border-radius: 20px;
        border: none;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        margin-bottom: 25px;
    }
    .status-badge-container {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-bottom: 30px;
    }
    .status-mini-card {
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        border-radius: 15px;
        padding: 15px;
        flex: 1;
        text-align: center;
    }
    .status-mini-label {
        font-size: 0.75rem;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 5px;
    }
    .status-mini-value {
        font-weight: 700;
        color: #1e293b;
    }
</style>

<div class="container-fluid">
    <!-- Header -->
    <div class="card presensi-card">
        <div class="card-body p-4 text-center">
            <h4 class="fw-bold mb-4">Presensi Kehadiran</h4>
            
            <?php if ($show_alert): ?>
            <script>
            Swal.fire({
                icon: '<?php echo $alert_success ? "success" : "error"; ?>',
                title: '<?php echo $alert_message; ?>',
                text: '<?php echo $alert_text; ?>',
                confirmButtonColor: '#4f46e5'
            });
            </script>
            <?php
endif; ?>
            
            <div class="status-badge-container">
                <div class="status-mini-card">
                    <div class="status-mini-label">Hari Ini</div>
                    <div class="status-mini-value"><?php echo date('d M Y'); ?></div>
                </div>
                <div class="status-mini-card">
                    <div class="status-mini-label">Waktu</div>
                    <div class="status-mini-value" id="presensi-time">--:--</div>
                </div>
                <div class="status-mini-card">
                    <div class="status-mini-label">Status</div>
                    <div class="status-mini-value <?php echo($tipe_absen == 'pulang' || $sudah_keterangan) ? 'text-success' : 'text-primary'; ?>">
                        <?php
if ($sudah_keterangan)
    echo $tipe_keterangan;
elseif ($tipe_absen == 'pulang')
    echo 'Lengkap';
elseif ($tipe_absen == 'masuk')
    echo 'Sudah Masuk';
else
    echo 'Belum Absen';
?>
                    </div>
                </div>
            </div>
        </div>
    </div>

            <?php if ($tipe_absen != 'pulang' && !$sudah_keterangan): ?>
        <!-- Form Presensi -->
        <div class="card">
            <div class="card-header bg-white" style="border-bottom: 1px solid #eef0f7; padding: 20px;">
                <h5 class="card-title mb-0 text-center" style="color: #2b3144; font-weight: 600;">
                    Form Absen <?php echo($sudah_absen && $tipe_absen == 'masuk') ? 'Pulang' : 'Masuk'; ?>
                </h5>
            </div>
            <div class="card-body" style="padding: 20px;">
                <form action="" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="return validateForm();">
                    <!-- Hidden fields -->
                    <input type="hidden" name="id_karyawan" value="<?php echo $_SESSION['idsi']; ?>">
                    <input type="hidden" name="nama" value="<?php echo $_SESSION['namasi']; ?>">
                    <input type="hidden" name="waktu" value="<?php echo date('Y-m-d H:i:s'); ?>">
                    <input type="hidden" name="tipe" value="<?php echo($sudah_absen && $tipe_absen == 'masuk') ? 'pulang' : 'masuk'; ?>">
                    
                    <!-- Status Kehadiran -->
                    <div class="form-group mb-3">
                        <label class="form-label">Status Kehadiran <span class="text-danger">*</span></label>
                        <select class="form-control" name="status_kehadiran" id="status_kehadiran" required onchange="toggleBuktiField()">
                            <option value="">-- Pilih Status --</option>
                            <option value="Hadir">Hadir</option>
                            <option value="Izin">Izin</option>
                            <option value="Sakit">Sakit</option>
                        </select>
                        <small class="text-muted">Pilih "Hadir" untuk absensi normal, "Izin" atau "Sakit" jika tidak bisa hadir</small>
                    </div>
                    
                    <!-- Lokasi GPS - hanya untuk Hadir -->
                    <div class="form-group mb-3" id="lokasi-field" style="display: none;">
                        <label class="form-label">Lokasi GPS</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="lokasi" id="lokasi" readonly placeholder="Mengambil lokasi...">
                            <button class="btn btn-outline-secondary" type="button" onclick="getLocation()" style="border-radius: 0 8px 8px 0;">
                                <i class="fas fa-map-marker-alt"></i>
                            </button>
                        </div>
                        <small id="location-status" class="text-muted">
                            Pilih status "Hadir" untuk mengaktifkan GPS
                        </small>
                    </div>
                    
                    <!-- Upload Bukti - hanya untuk Izin/Sakit -->
                    <div class="form-group mb-3" id="bukti-field" style="display: none;">
                        <label class="form-label">Upload Bukti (Surat Izin/Sakit) <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="bukti" id="bukti" accept=".jpg,.jpeg,.png,.pdf">
                        <small class="text-muted">Format: JPG, PNG, atau PDF. Max: 2MB</small>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label class="form-label">Kegiatan/Keperluan</label>
                        <input type="text" class="form-control" name="kegiatan" placeholder="Contoh: Rapat, Kuliah, Berobat, Keperluan Keluarga" required>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label class="form-label">Keterangan/Alasan</label>
                        <textarea class="form-control" name="alasan" rows="3" placeholder="Jelaskan keterangan atau alasan Anda" required></textarea>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" name="simpan" class="btn btn-primary btn-lg">
                            <i class="fas fa-check-circle me-2"></i>
                            Kirim Presensi
                        </button>
                        <a href="index.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>
                            Kembali ke Dashboard
                        </a>
                    </div>
                </form>
            </div>
        </div>
        <?php
else: ?>
        <!-- Sudah Absen Lengkap atau Sudah Kirim Keterangan -->
        <div class="card">
            <div class="card-body text-center" style="padding: 40px 20px;">
                <div style="width: 80px; height: 80px; background: #d1fae5; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                    <i class="fas fa-check-circle text-success" style="font-size: 40px;"></i>
                </div>
                <?php if ($sudah_keterangan): ?>
                    <h4 class="fw-bold text-success mb-2">Keterangan Terkirim!</h4>
                    <p class="text-muted mb-3" style="font-size: 14px;">
                        Anda sudah mengirim keterangan <strong><?php echo $tipe_keterangan; ?></strong> untuk hari ini.
                    </p>
                <?php
    else: ?>
                    <h4 class="fw-bold text-success mb-2">Absensi Lengkap!</h4>
                    <p class="text-muted mb-3" style="font-size: 14px;">Terima kasih telah melakukan absensi hari ini.</p>
                <?php
    endif; ?>
                <a href="index.php" class="btn btn-primary">
                    <i class="fas fa-home me-2"></i>
                    Kembali
                </a>
            </div>
        </div>
        <?php
endif; ?>
    </div>

    <script>
        // Update local time for presensi dashboard card
        function updatePresensiTime() {
            const now = new Date();
            const timeStr = String(now.getHours()).padStart(2, '0') + ':' + 
                            String(now.getMinutes()).padStart(2, '0') + ':' + 
                            String(now.getSeconds()).padStart(2, '0') + ' WIB';
            const el = document.getElementById('presensi-time');
            if (el) el.textContent = timeStr;
        }
        setInterval(updatePresensiTime, 1000);
        
        // Initialize clock when DOM is ready
        document.addEventListener('DOMContentLoaded', updatePresensiTime);

        // Toggle field bukti dan lokasi berdasarkan status kehadiran
        function toggleBuktiField() {
            const status = document.getElementById('status_kehadiran').value;
            const lokasiField = document.getElementById('lokasi-field');
            const buktiField = document.getElementById('bukti-field');
            const buktiInput = document.getElementById('bukti');
            const lokasiInput = document.getElementById('lokasi');
            const locationStatus = document.getElementById('location-status');
            
            if (status === 'Izin' || status === 'Sakit') {
                // Tampilkan field bukti, sembunyikan lokasi
                buktiField.style.display = 'block';
                lokasiField.style.display = 'none';
                buktiInput.required = true;
                lokasiInput.required = false;
                lokasiInput.value = '-'; // Set default value
            } else if (status === 'Hadir') {
                // Tampilkan lokasi, sembunyikan bukti
                buktiField.style.display = 'none';
                lokasiField.style.display = 'block';
                buktiInput.required = false;
                lokasiInput.required = true;
                lokasiInput.value = ''; // Reset value
                locationStatus.className = 'text-muted mt-1';
                locationStatus.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Mengambil lokasi otomatis...';
                getLocation(); // Ambil lokasi otomatis
            } else {
                // Belum pilih, sembunyikan semua
                buktiField.style.display = 'none';
                lokasiField.style.display = 'none';
                buktiInput.required = false;
                lokasiInput.required = false;
            }
        }

        let locationAttempts = 0;
        const maxAttempts = 3;

        function updateLocationStatus(message, isError = false) {
            const statusDiv = document.getElementById('location-status');
            if (isError) {
                statusDiv.className = 'text-danger mt-1';
                statusDiv.innerHTML = `<i class="fa fa-exclamation-circle"></i> ${message}`;
            } else {
                statusDiv.className = 'text-muted mt-1';
                statusDiv.innerHTML = `<i class="fa fa-info-circle"></i> ${message}`;
            }
        }

        function getLocation() {
            if (!navigator.geolocation) {
                updateLocationStatus('Browser Anda tidak mendukung GPS. Silakan gunakan browser yang lebih baru.', true);
                return;
            }

            locationAttempts++;
            updateLocationStatus('Sedang mendapatkan lokasi...');

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    document.getElementById("lokasi").value = position.coords.latitude + ", " + position.coords.longitude;
                    updateLocationStatus('Lokasi berhasil didapatkan');
                    document.getElementById("lokasi").classList.add('is-valid');
                },
                function(error) {
                    let message = "";
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            message = 'Akses lokasi ditolak. Silakan izinkan akses lokasi.';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            message = 'GPS tidak aktif. Mohon aktifkan GPS pada perangkat Anda.';
                            break;
                        case error.TIMEOUT:
                            message = 'Waktu permintaan habis. Pastikan GPS aktif dan ada koneksi internet.';
                            break;
                        default:
                            message = 'Terjadi kesalahan saat mengambil lokasi.';
                    }
                    updateLocationStatus(message, true);
                    
                    if (locationAttempts < maxAttempts) {
                        setTimeout(function() {
                            getLocation();
                        }, 3000);
                    }
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        }

        function validateForm() {
            console.log('=== VALIDATE FORM START ===');
            const status = document.getElementById('status_kehadiran').value;
            console.log('Status:', status);
            
            if (!status) {
                console.log('ERROR: Status tidak dipilih');
                Swal.fire({
                    icon: 'warning',
                    title: 'Status Belum Dipilih',
                    text: 'Mohon pilih status kehadiran terlebih dahulu',
                    confirmButtonColor: '#667eea'
                });
                return false;
            }
            
            if (status === 'Hadir') {
                const lokasi = document.getElementById("lokasi").value;
                console.log('Lokasi:', lokasi);
                if (!lokasi || lokasi === '' || lokasi === 'Mengambil lokasi...') {
                    console.log('ERROR: Lokasi belum terdeteksi');
                    Swal.fire({
                        icon: 'warning',
                        title: 'Lokasi Belum Terdeteksi',
                        text: 'Mohon tunggu atau izinkan akses lokasi terlebih dahulu',
                        confirmButtonColor: '#667eea'
                    });
                    return false;
                }
            } else if (status === 'Izin' || status === 'Sakit') {
                const bukti = document.getElementById("bukti").files[0];
                console.log('Bukti file:', bukti);
                if (!bukti) {
                    console.log('ERROR: Bukti belum diupload');
                    Swal.fire({
                        icon: 'warning',
                        title: 'Bukti Belum Diupload',
                        text: 'Mohon upload bukti ' + status.toLowerCase() + ' terlebih dahulu',
                        confirmButtonColor: '#667eea'
                    });
                    return false;
                }
                
                // Validasi ukuran file (max 2MB)
                console.log('File size:', bukti.size, 'bytes');
                if (bukti.size > 2 * 1024 * 1024) {
                    console.log('ERROR: File terlalu besar');
                    Swal.fire({
                        icon: 'error',
                        title: 'File Terlalu Besar',
                        text: 'Ukuran file maksimal 2MB. File Anda: ' + (bukti.size / 1024 / 1024).toFixed(2) + 'MB',
                        confirmButtonColor: '#667eea'
                    });
                    return false;
                }
                
                // Validasi format file
                console.log('File type:', bukti.type);
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
                if (!allowedTypes.includes(bukti.type)) {
                    console.log('ERROR: Format file tidak valid');
                    Swal.fire({
                        icon: 'error',
                        title: 'Format File Tidak Valid',
                        text: 'Hanya file JPG, PNG, atau PDF yang diperbolehkan',
                        confirmButtonColor: '#667eea'
                    });
                    return false;
                }
            }
            
            console.log('✓ Validation passed, submitting form...');
            
            // Show loading saat submit
            Swal.fire({
                title: 'Mengirim Data...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            return true;
        }

    </script>
<?php include "footer.php"; ?>


