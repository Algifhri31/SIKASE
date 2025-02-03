<?php 
error_reporting(0);
include '../koneksi.php';
 ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="au theme template">
    <meta name="author" content="Hau Nguyen">
    <meta name="keywords" content="au theme template">

    <!-- Title Page-->
    <title>Beranda Beswan</title>

       
  


    <!-- Fontfaces CSS-->
    <link href="css/font-face.css" rel="stylesheet" media="all">
    <link href="vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
    <link href="vendor/font-awesome-5/css/fontawesome-all.min.css" rel="stylesheet" media="all">
    <link href="vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">

    <!-- Bootstrap CSS-->
    <link href="vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet" media="all">

    <!-- Vendor CSS-->
    <link href="vendor/animsition/animsition.min.css" rel="stylesheet" media="all">
    <link href="vendor/bootstrap-progressbar/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet" media="all">
    <link href="vendor/wow/animate.css" rel="stylesheet" media="all">
    <link href="vendor/css-hamburgers/hamburgers.min.css" rel="stylesheet" media="all">
    <link href="vendor/slick/slick.css" rel="stylesheet" media="all">
    <link href="vendor/select2/select2.min.css" rel="stylesheet" media="all">
    <link href="vendor/perfect-scrollbar/perfect-scrollbar.css" rel="stylesheet" media="all">

    <!-- Main CSS-->
    <link href="css/theme.css" rel="stylesheet" media="all">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js" integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

</head>
<?php date_default_timezone_set('Asia/Jakarta'); ?>
<body class="animsition" >
    <div class="page-wrapper">
        <!-- HEADER MOBILE-->
        <header class="header-mobile d-block d-lg-none">
            <div class="header-mobile__bar">
                <div class="container-fluid">
                    <div class="header-mobile-inner">
                        <a class="logo" href="#">
                            
                        </a>
                        <button class="hamburger hamburger--slider" type="button">
                            <span class="hamburger-box">
                                <span class="hamburger-inner"></span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
            <nav class="navbar-mobile">
                <div class="container-fluid">
                    <ul class="navbar-mobile__list list-unstyled">
                        <li class="has-sub">
                            <a class="js-arrow" href="#">
                                <i class="fas fa-tachometer-alt"></i>Dashboard</a>
                           
                        </li>
                                              
                    </ul>
                </div>
            </nav>
        </header>
        <!-- END HEADER MOBILE-->

        <!-- MENU SIDEBAR-->
        <aside class="menu-sidebar d-none d-lg-block">
            <div class="logo">
                <a href="#">
<h1>Beswan</h1>
                </a>
            </div>
            <div class="menu-sidebar__content js-scrollbar1">
                <nav class="navbar-sidebar">
                    <ul class="list-unstyled navbar__list">
                        <li class="active has-sub">
                            <a class="js-arrow" href="?m=awal">
                                <i class="fas fa-tachometer-alt"></i>Dashboard</a>
                          
                        </li>
                        
                    </ul>
                </nav>
            </div>
        </aside>
        <!-- END MENU SIDEBAR-->

        <!-- PAGE CONTAINER-->
        <div class="page-container">
            <!-- HEADER DESKTOP-->
            <header class="header-desktop">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="header-wrap">
                            <form class="form-header" action="" method="POST">
                                <input class="au-input au-input--xl" type="text" name="search" value="Absen Beswan" readonly="" />
                                
                            </form>
                            <div class="header-button">
                               
                                 <?php
                                    $id = $_SESSION['idsi'];
                                    include '../koneksi.php';
                                    $sql = "SELECT * FROM tb_karyawan WHERE id_karyawan = '$id'";
                                    $query = mysqli_query($koneksi, $sql);
                                    $r = mysqli_fetch_array($query);

                                     ?>

                                <div class="account-wrap">
                                    <div class="account-item clearfix js-item-menu">
                                        <div class="image">
                                            <img src="../images/<?php echo $r['foto'];?>" class="img-circle" alt="<?php echo $r['nama'];?>" />
                                        </div>
                                        <div class="content">
                                            <a class="js-acc-btn" href="#"><?php echo $r['nama']; ?></a>
                                        </div>
                                        <div class="account-dropdown js-dropdown">
                                            <div class="info clearfix">
                                                <div class="image">
                                                    <a href="#">
                                                         <img src="../images/<?php echo $r['foto'];?>" class="img-circle" \ />
                                                    </a>
                                                </div>
                                                <div class="content">
                                                    <h5 class="name">
                                                        <a href="#"><?php echo $r['nama']; ?></a>
                                                    </h5>
                                                    
                                                </div>
                                            </div>
                                            <div class="account-dropdown__body">
                                                <div class="account-dropdown__item">
                                                    <a href="?m=karyawan&s=profil">
                                                        <i class="zmdi zmdi-account"></i>Account</a>
                                                </div>
                                                <!--<div class="account-dropdown__item">
                                                    <a href="#">
                                                        <i class="zmdi zmdi-settings"></i>Setting</a>
                                                </div>
                                                <div class="account-dropdown__item">
                                                    <a href="#">
                                                        <i class="zmdi zmdi-money-box"></i>Billing</a>
                                                </div>
                                            </div>-->
                                            <div class="account-dropdown__footer">
                                                <a href="logout.php">
                                                    <i class="zmdi zmdi-power"></i>Logout</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            <!-- HEADER DESKTOP-->

            <!-- MAIN CONTENT-->
            <?php 
                $sqls = "SELECT * FROM tb_absen WHERE id_karyawan = '$id' AND waktu LIKE '".date('l, d-m-Y')."%' ORDER BY waktu DESC LIMIT 1";
                $querys = mysqli_query($koneksi, $sqls);
                $rz = mysqli_fetch_array($querys);

                $sudah_absen = !empty($rz); // Cek apakah sudah ada data absen hari ini
                $tipe_absen = $sudah_absen ? $rz['tipe'] : ''; // Jika sudah absen, ambil tipe absen
            ?>

            <div class="main-content">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="overview-wrap">
                                    <h2 class="title-1" style="text-align: center;">Selamat Datang <?php echo $_SESSION['namasi']; ?>, 
                                        <?php if ($tipe_absen != 'pulang') : ?>
                                            Silahkan Absen Terlebih Dahulu!
                                            <?php else : ?>
                                            Anda sudah absen hari ini.
                                        <?php endif; ?>
                                    </h2>
                                    <button class="au-btn au-btn-icon au-btn--blue">
                                        
                                </div>
                            </div>
                        </div>


                        <!-- FORM -->
                        <div class="row">
                           <div class="table-responsive table--no-card m-b-30">
                           <?php 
                           if (isset($_POST['simpan'])) {
                                $id_karyawan = $_POST['id_karyawan'];
                                $nama = $_POST['nama'];
                                $waktu = $_POST['waktu'];
                                $tipe = $_POST['tipe'];
                                $kegiatan = $_POST['kegiatan'];
                                $alasan = $_POST['alasan'];
                                $lokasi = $_POST['lokasi'];
                                if($lokasi == "" || $lokasi == null || empty($lokasi)){} else {

                                    $save = "INSERT INTO tb_absen SET id_karyawan='$id_karyawan', nama='$nama', waktu='$waktu', lokasi = '$lokasi', kegiatan='$kegiatan', keterangan='$alasan', tipe='$tipe'";
                                    mysqli_query($koneksi, $save);
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
                                                    <input type="text" class="form-control" value="<?php echo date('l, d-m-Y h:i:s a' ); ?>" name="waktu" readonly>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>Lokasi</td>
                                                <td>
                                                    <input type="text" class="form-control" name="lokasi" id="lokasi" readonly="true" required placeholder="Pastikan anda mengijinkan akses lokasi!">
                                                </td>
                                            </tr>

                                            <!-- Jika absen terakhir "masuk", tampilkan form absen pulang -->
                                            <?php if ($tipe_absen != 'pulang') : ?>
                                                <tr>
                                                    <td>Tipe Absen</td>
                                                    <td>
                                                        <input type="text" name="tipe" required readonly value="<?php echo ($sudah_absen && $tipe_absen == 'masuk') ? 'Pulang' : 'Masuk'; ?>" class="form-control">
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
                                            <?php if ($tipe_absen != 'pulang') : ?>
                                                <tr>
                                                    <td colspan="2">
                                                        <button type="submit" name="simpan" class="btn btn-primary w-100" onclick="return cekLokasi(event);">
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
                        <?php if ($tipe_absen != 'pulang') : ?>
                            <div class="row">
                            <div class="table-responsive table--no-card m-b-30">

                                    
                                    <table class="table table-borderless table-striped table-earning" >
                                            
                                            <tbody>
                                                <tr>
                                                
                                                    <td>
                                                    
                                                <a href="?m=karyawan&s=title"><button class="btn btn-warning">Klik Tombol ini jika tidak hadir / absen</button></a>
                                                </td>
                                                </tr>
                                            
                                            
                                                
                                        </tbody>
                                        </table>
                                        
                                
                                        
                                    </div>    
                            </div>
                        <?php endif; ?>

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
            </div>
            <!-- END MAIN CONTENT-->
            <!-- END PAGE CONTAINER-->
        </div>

    </div>

    <!-- Jquery JS-->
    <script src="vendor/jquery-3.2.1.min.js"></script>
    <!-- Bootstrap JS-->
    <script src="vendor/bootstrap-4.1/popper.min.js"></script>
    <script src="vendor/bootstrap-4.1/bootstrap.min.js"></script>
    <!-- Vendor JS       -->
    <script src="vendor/slick/slick.min.js">
    </script>
    <script src="vendor/wow/wow.min.js"></script>
    <script src="vendor/animsition/animsition.min.js"></script>
    <script src="vendor/bootstrap-progressbar/bootstrap-progressbar.min.js">
    </script>
    <script src="vendor/counter-up/jquery.waypoints.min.js"></script>
    <script src="vendor/counter-up/jquery.counterup.min.js">
    </script>
    <script src="vendor/circle-progress/circle-progress.min.js"></script>
    <script src="vendor/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="vendor/chartjs/Chart.bundle.min.js"></script>
    <script src="vendor/select2/select2.min.js">
    </script>

    <!-- Main JS-->
    <script src="js/main.js"></script>

    <script>
        function getLocation() {
            if (navigator.geolocation) {
                // Ambil elemen input lokasi
                var lokasiInput = document.getElementById("lokasi");

                // Coba ambil lokasi pertama kali
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        lokasiInput.value = position.coords.latitude + ", " + position.coords.longitude;
                    },
                    showError,
                    {
                        enableHighAccuracy: true,
                        timeout: 15000,  // Naikkan timeout jadi 15 detik
                        maximumAge: 0
                    }
                );

                // Pantau perubahan lokasi
                navigator.geolocation.watchPosition(
                    function(position) {
                        lokasiInput.value = position.coords.latitude + ", " + position.coords.longitude;
                    },
                    showError,
                    {
                        enableHighAccuracy: true,
                        timeout: 30000, // Timeout lebih lama untuk pemantauan lokasi
                        maximumAge: 0
                    }
                );

            } else {
                swal({
                    icon: "error",
                    title: "Geolocation tidak didukung oleh browser ini",
                });
            }
        }

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

</body>

</html>
<!-- end document-->
