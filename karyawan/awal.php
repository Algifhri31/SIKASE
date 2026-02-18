<?php
$page_title = "Dashboard Beswan";
include "header.php"; // header.php includes koneksi.php
include 'helper_tanggal_new.php';

// Inisialisasi variable
$id = $_SESSION['idsi'];
$save = false;

// Ambil statistik kehadiran
$bulan_ini = date('Y-m');
$hari_ini = date('Y-m-d');

// Hitung total absen masuk bulan ini
$sql_masuk = "SELECT COUNT(*) as total FROM tb_absen WHERE id_karyawan = '$id' AND DATE_FORMAT(waktu, '%Y-%m') = '$bulan_ini' AND tipe = 'masuk'";
$result_masuk = mysqli_query($koneksi, $sql_masuk);
$total_masuk = $result_masuk ? mysqli_fetch_assoc($result_masuk)['total'] : 0;

// Hitung total absen pulang bulan ini
$sql_pulang = "SELECT COUNT(*) as total FROM tb_absen WHERE id_karyawan = '$id' AND DATE_FORMAT(waktu, '%Y-%m') = '$bulan_ini' AND tipe = 'pulang'";
$result_pulang = mysqli_query($koneksi, $sql_pulang);
$total_pulang = $result_pulang ? mysqli_fetch_assoc($result_pulang)['total'] : 0;

// Hitung total hari kerja dalam bulan (asumsi 22 hari kerja per bulan)
$hari_kerja_bulan = 22;
$persentase_kehadiran = $total_masuk > 0 ? round(($total_masuk / $hari_kerja_bulan) * 100) : 0;
?>


            <!-- MAIN CONTENT-->
            <?php 
                $sqls = "SELECT * FROM tb_absen WHERE id_karyawan = '$id' AND waktu LIKE '".getTanggalIndonesia()."%' ORDER BY waktu DESC LIMIT 1";
                $querys = mysqli_query($koneksi, $sqls);
                $rz = mysqli_fetch_array($querys);
                
                // Cek juga keterangan hari ini
                $sql_ket = "SELECT * FROM tb_keterangan WHERE id_karyawan = '$id' AND waktu LIKE '".getTanggalIndonesia()."%' ORDER BY waktu DESC LIMIT 1";
                $query_ket = mysqli_query($koneksi, $sql_ket);
                $rz_ket = mysqli_fetch_array($query_ket);

                $sudah_absen = !empty($rz); // Cek apakah sudah ada data absen hari ini
                $sudah_keterangan = !empty($rz_ket); // Cek apakah sudah ada keterangan hari ini
                $tipe_absen = $sudah_absen ? $rz['tipe'] : ''; // Jika sudah absen, ambil tipe absen
                $tipe_keterangan = $sudah_keterangan ? $rz_ket['keterangan'] : ''; // Jika sudah keterangan, ambil tipe
            ?>

            <div class="main-content">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row mb-4">
                                    <div class="col-md-12">
                                        <div class="card" style="border-radius: 15px; border: none; box-shadow: 0 0 15px rgba(0,0,0,0.05);">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="flex-shrink-0">
                                                        <div style="width: 50px; height: 50px; background: #e7f0ff; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                                            <i class="fas fa-user" style="font-size: 24px; color: #3b82f6;"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3" style="margin-left: 15px;">
                                                        <h5 class="mb-1" style="font-weight: 600; color: #2b3144;">Selamat Datang, <?php echo $_SESSION['namasi']; ?></h5>
                                                        <p class="mb-0 text-muted" style="font-size: 14px;">
                                                            <?php if ($tipe_absen != 'pulang') : ?>
                                                                Silakan melakukan absensi untuk hari ini
                                                            <?php else : ?>
                                                                Terima kasih, Anda sudah melakukan absensi hari ini
                                                            <?php endif; ?>
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="row mt-4">
                                                    <div class="col-md-3 col-6">
                                                        <div class="info-box" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px; padding: 15px; color: white; text-align: center;">
                                                            <h6 class="mb-2" style="font-size: 12px; opacity: 0.9;">Masuk Bulan Ini</h6>
                                                            <p class="mb-0" style="font-weight: 700; font-size: 24px;"><?php echo $total_masuk; ?></p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 col-6">
                                                        <div class="info-box" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 10px; padding: 15px; color: white; text-align: center;">
                                                            <h6 class="mb-2" style="font-size: 12px; opacity: 0.9;">Pulang Bulan Ini</h6>
                                                            <p class="mb-0" style="font-weight: 700; font-size: 24px;"><?php echo $total_pulang; ?></p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 col-6">
                                                        <div class="info-box" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 10px; padding: 15px; color: white; text-align: center;">
                                                            <h6 class="mb-2" style="font-size: 12px; opacity: 0.9;">Kehadiran</h6>
                                                            <p class="mb-0" style="font-weight: 700; font-size: 24px;"><?php echo $persentase_kehadiran; ?>%</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 col-6">
                                                        <div class="info-box" style="background: #f8fafc; border-radius: 10px; padding: 15px;">
                                                            <h6 class="mb-2" style="font-size: 14px; color: #64748b;">Tanggal</h6>
                                                            <p class="mb-0" style="font-weight: 600; color: #2b3144;"><?php echo getTanggalIndonesia(); ?></p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mt-3">
                                                    <div class="col-md-6">
                                                        <div class="info-box" style="background: #f8fafc; border-radius: 10px; padding: 15px;">
                                                            <h6 class="mb-2" style="font-size: 14px; color: #64748b;">Waktu</h6>
                                                            <p class="mb-0" style="font-weight: 600; color: #2b3144;" id="current-time"></p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="info-box" style="background: #f8fafc; border-radius: 10px; padding: 15px;">
                                                            <h6 class="mb-2" style="font-size: 14px; color: #64748b;">Status Hari Ini</h6>
                                                            <p class="mb-0" style="font-weight: 600; color: <?php echo ($tipe_absen == 'pulang' || $sudah_keterangan) ? '#059669' : '#dc2626'; ?>">
                                                                <?php 
                                                                if ($sudah_keterangan) {
                                                                    echo $tipe_keterangan; // Izin/Sakit
                                                                } elseif ($tipe_absen == 'pulang') {
                                                                    echo 'Sudah Absen';
                                                                } else {
                                                                    echo 'Belum Absen';
                                                                }
                                                                ?>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                        <!-- FORM -->
                        <div class="row">
                           <div class="table-responsive table--no-card m-b-30">
                           <?php 
                           if (isset($_POST['simpan'])) {
                                // Cek apakah sudah ada absensi atau keterangan hari ini
                                $check_absen = mysqli_query($koneksi, "SELECT * FROM tb_absen WHERE id_karyawan = '$id' AND waktu LIKE '".getTanggalIndonesia()."%'");
                                $check_ket = mysqli_query($koneksi, "SELECT * FROM tb_keterangan WHERE id_karyawan = '$id' AND waktu LIKE '".getTanggalIndonesia()."%'");
                                
                                if (mysqli_num_rows($check_absen) > 0 || mysqli_num_rows($check_ket) > 0) {
                                    echo "<script>
                                        swal({
                                            icon: 'warning',
                                            title: 'Sudah Absen!',
                                            text: 'Anda sudah melakukan absensi atau mengirim keterangan untuk hari ini.',
                                            allowOutsideClick: false,
                                            confirmButtonText: 'OK'
                                        }).then(() => {
                                            window.location.href = 'index.php?m=awal';
                                        });
                                    </script>";
                                } else {
                                    $id_karyawan = $_POST['id_karyawan'];
                                $nama = $_POST['nama'];
                                $waktu = $_POST['waktu'];
                                $tipe = $_POST['tipe'];
                                $kegiatan = $_POST['kegiatan'];
                                $alasan = $_POST['alasan'];
                                $lokasi = $_POST['lokasi'];
                                if(!empty($lokasi)) {
                                    $save = mysqli_query($koneksi, "INSERT INTO tb_absen SET 
                                        id_karyawan='$id_karyawan', 
                                        nama='$nama', 
                                        waktu='$waktu', 
                                        lokasi='$lokasi', 
                                        kegiatan='$kegiatan', 
                                        keterangan='$alasan', 
                                        tipe='$tipe'");
                                        
                                    if($save) {
                                        echo "<script>
                                            swal({
                                                icon: 'success',
                                                title: 'Absen " . ($tipe == 'masuk' ? 'Masuk' : 'Pulang') . " Berhasil!',
                                                text: 'Terima kasih telah melakukan absen " . ($tipe == 'masuk' ? 'masuk' : 'pulang') . ".',
                                                allowOutsideClick: false,
                                                confirmButtonText: 'OK'
                                            }).then(() => {
                                                window.location.href = 'index.php?m=awal';
                                            });
                                        </script>";
                                    }
                                }
                                }
                            }
                           ?>
                            <form action="" autocomplete="off" method="post">
                                <div class="form-group">
                                <br>
                                <table class="table table-borderless table-striped table-earning" >
                                        <tbody>
                                            <tr>
                                                <td>No.KSE</td>
                                                <td>
                                                    <input type="text" readonly class="form-control" name="id_karyawan" autocomplete="off" size="25px" maxlength="25px" value="<?php echo $_SESSION['idsi']; ?>">
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>Nama</td>
                                                <td>
                                                    <input type="text" class="form-control" name="nama" autocomplete="off" readonly value="<?php echo $_SESSION['namasi']; ?>">
                                                </td>
                                            </tr>

                                                <tr>
                                                    <td>Waktu</td>
                                                    <td>
                                                        <input type="text" class="form-control" value="<?php 
                                                            $hari = array(
                                                                'Sunday' => 'Minggu',
                                                                'Monday' => 'Senin',
                                                                'Tuesday' => 'Selasa',
                                                                'Wednesday' => 'Rabu',
                                                                'Thursday' => 'Kamis',
                                                                'Friday' => 'Jumat',
                                                                'Saturday' => 'Sabtu'
                                                            );
                                                            $englishDay = date('l');
                                                            echo $hari[$englishDay] . ', ' . date('d-m-Y H:i:s'); 
                                                        ?>" name="waktu" readonly>                                            <tr>
                                                <td>Lokasi</td>
                                                <td>
                                                    <input type="text" class="form-control" name="lokasi" id="lokasi" readonly="true" required placeholder="Mengambil lokasi...">
                                                    <div id="location-status" class="text-muted mt-1" style="font-size: 0.875em;">
                                                        <i class="fa fa-spinner fa-spin"></i> Sedang mendapatkan lokasi...
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Jika absen terakhir "masuk", tampilkan form absen pulang -->
                                            <?php if ($tipe_absen != 'pulang' && !$sudah_keterangan) : ?>
                                                <tr>
                                                    <td>Tipe Absen</td>
                                                    <td>
                                                        <input type="text" name="tipe" required readonly value="<?php echo ($sudah_absen && $tipe_absen == 'masuk') ? 'pulang' : 'masuk'; ?>" class="form-control">
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>Kegiatan</td>
                                                    <td>
                                                        <input type="text" class="form-control" name="kegiatan" autocomplete="off" placeholder="Masukkan kegiatan disini..." required>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>Keterangan</td>
                                                    <td>
                                                        <textarea name="alasan" class="form-control" required placeholder="Masukkan keterangan disini..."></textarea>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>

                                            <!-- Jika absen terakhir "pulang", tombol absen tidak muncul -->
                                            <?php if ($tipe_absen != 'pulang' && !$sudah_keterangan) : ?>
                                                <tr>
                                                    <td colspan="2">
                                                        <button type="submit" name="simpan" class="btn btn-primary w-100" onclick="return validateForm();">
                                                            <?php echo ($sudah_absen && $tipe_absen == 'masuk') ? 'Absen Pulang' : 'Absen Masuk'; ?>
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>

                                        </tbody>

                                    </table>
                                        </div>
                            </form>
                                    
                                </div>    
                        </div>
                        <!-- END FORM -->
                        
                        <div class="header-desktop">
                            <div class="col-md-12">
                                <div class="copyright">
                                    <p> © KSE UINSU 2025 <a href="https://colorlib.com"></a>.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
<?php include "footer.php"; ?>

<script>
    // Initialize Bootstrap dropdown
    document.addEventListener('DOMContentLoaded', function() {
        var dropdownElementList = [].slice.call(document.querySelectorAll('[data-toggle="dropdown"]'))
        dropdownElementList.map(function (dropdownToggleEl) {
            return new bootstrap.Dropdown(dropdownToggleEl)
        });
    });

    // Update waktu real-time dengan format 24 jam
    function updateTime() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const timeString = `${hours}:${minutes}:${seconds} WIB`;
        document.getElementById('current-time').textContent = timeString;
    }
    
    // Update waktu setiap detik
    setInterval(updateTime, 1000);
    updateTime(); // Panggil sekali untuk menampilkan waktu awal

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

    function getLocation(showAlert = true) {
        if (!navigator.geolocation) {
            updateLocationStatus('Browser Anda tidak mendukung GPS. Silakan gunakan browser yang lebih baru.', true);
            return;
        }

        if (showAlert) {
            swal({
                title: "Izin Akses Lokasi",
                text: "Sistem memerlukan akses ke lokasi Anda. Silakan klik 'Izinkan' saat diminta.",
                icon: "info",
                buttons: {
                    confirm: {
                        text: "Lanjutkan",
                        value: true,
                    }
                }
            }).then((value) => {
                if (value) {
                    requestLocation();
                }
            });
        } else {
            requestLocation();
        }
    }

    function requestLocation() {
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
                        message = 'Akses lokasi ditolak. Silakan klik ikon kunci di address bar dan pilih "Izinkan" untuk lokasi.';
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
                        requestLocation();
                    }, 3000);
                } else {
                    swal({
                        title: "Gagal Mendapatkan Lokasi",
                        text: message + "\n\nSilakan:\n1. Refresh halaman\n2. Pastikan GPS aktif\n3. Izinkan akses lokasi di browser",
                        icon: "error",
                        buttons: {
                            retry: {
                                text: "Coba Lagi",
                                value: true,
                            }
                        }
                    }).then((value) => {
                        if (value) {
                            locationAttempts = 0;
                            getLocation(false);
                        }
                    });
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
        const lokasi = document.getElementById("lokasi").value;
        if (!lokasi) {
            swal({
                icon: "warning",
                title: "Lokasi Dibutuhkan",
                text: "Mohon tunggu atau izinkan akses lokasi terlebih dahulu"
            });
            return false;
        }
        return true;
    }

    // Mulai mengambil lokasi saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        getLocation(true);
    });

    function showError(error) {
        switch (error.code) {
            case error.PERMISSION_DENIED:
                swal({
                    icon: "error",
                    title: "Silahkan ijinkan akses lokasi!",
                });
                break;
            case error.POSITION_UNAVAILABLE:
                swal({
                    icon: "error",
                    title: "Informasi lokasi tidak tersedia!",
                });
                break;
            case error.TIMEOUT:
                swal({
                    icon: "error",
                    title: "Permintaan lokasi melebihi waktu tunggu. Coba lagi!",
                });
                break;
            case error.UNKNOWN_ERROR:
                swal({
                    icon: "error",
                    title: "Terjadi kesalahan yang tidak diketahui!",
                });
                break;
        }
    }

    // Panggil fungsi saat halaman dimuat
    getLocation();

    function cekLokasi(event) {
        let lokasi = document.getElementById("lokasi").value;
        
        if (lokasi.trim() === "") {
            event.preventDefault(); // Mencegah form terkirim
            swal({
                icon: 'error',
                title: 'Lokasi tidak ditemukan!',
                text: 'Silakan izinkan akses lokasi dan coba lagi.',
            });
            return false; // Menghentikan eksekusi form
        }

        return true; // Lanjutkan submit jika lokasi terisi
    }

    <?php if ($save) : ?>
        swal({
            icon: 'success',
            <?php if($tipe == 'Masuk') {?>
                title: 'Absen Masuk Berhasil!',
                text: 'Terima kasih telah melakukan absen masuk.',
            <?php } else {?>
                title: 'Absen Pulang Berhasil!',
                text: 'Terima kasih telah melakukan absen pulang.',
            <?php }?>
            allowOutsideClick: false,
            confirmButtonText: 'OK'
        }).then(() => {
            window.location.href = "index.php?m=awal";
        });
    <?php endif; ?>
</script>
