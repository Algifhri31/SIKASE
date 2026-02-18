<?php
if (!isset($_SESSION['idsi'])) {
    header("Location: ../../../login.php");
    exit;
}

// Include koneksi database
require_once('C:/laragon/www/KSEHADIR/koneksi.php');

$id = $_SESSION['idsi'];
$sql = "SELECT * FROM tb_karyawan WHERE id_karyawan = '$id'";
$query = mysqli_query($koneksi, $sql);

if (!$query) {
    die("Query error: " . mysqli_error($koneksi));
}

$r = mysqli_fetch_array($query);
?>

<!-- MAIN CONTENT-->
<div class="main-content">
    <div class="section__content section__content--p30">
        <div class="container-fluid">
            <!-- Card Profil -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title text-center">Profil Beswan</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Foto Profil -->
                                <div class="col-md-4 text-center mb-4">
                                    <div class="profile-image-container">
                                        <img src="../../../images/<?php echo $r['foto']; ?>" 
                                             alt="<?php echo $r['nama']; ?>" 
                                             class="rounded-circle"
                                             style="width: 200px; height: 200px; object-fit: cover; border: 5px solid #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                                    </div>
                                    <h5 class="mt-3 mb-1"><?php echo $r['nama']; ?></h5>
                                    <p class="text-muted"><?php echo $r['jabatan']; ?></p>
                                </div>

                                <!-- Informasi Profil -->
                                <div class="col-md-8">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <tbody>
                                                <tr>
                                                    <td width="200px">No. KSE</td>
                                                    <td><?php echo $r['id_karyawan']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Nama Lengkap</td>
                                                    <td><?php echo $r['nama']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Tempat & Tanggal Lahir</td>
                                                    <td><?php echo $r['tmp_tgl_lahir']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Jenis Kelamin</td>
                                                    <td><?php echo $r['jenkel']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Agama</td>
                                                    <td><?php echo isset($r['agama']) ? $r['agama'] : '-'; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Alamat</td>
                                                    <td><?php echo $r['alamat']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Nomor Telepon</td>
                                                    <td><?php echo $r['no_tel']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Divisi</td>
                                                    <td><?php echo $r['jabatan']; ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <!-- Tombol Edit -->
                                    <div class="text-right mt-4">
                                        <a href="?m=karyawan&s=edit&id_karyawan=<?php echo $id; ?>" 
                                           class="btn btn-primary">
                                            <i class="fas fa-edit mr-2"></i>Edit Profil
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

if (!$query) {
    die("Query error: " . mysqli_error($koneksi));
}

$r = mysqli_fetch_array($query);

$id = $_SESSION['idsi'];
$sql = "SELECT * FROM tb_karyawan WHERE id_karyawan = '$id'";
$query = mysqli_query($koneksi, $sql);

if (!$query) {
    die("Query error: " . mysqli_error($koneksi));
}

$r = mysqli_fetch_array($query);
?>

<div class="main-content">
    <div class="section__content section__content--p30">
        <div class="container-fluid">
            <!-- Card Profil -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title text-center">Profil Beswan</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Foto Profil -->
                                <div class="col-md-4 text-center mb-4">
                                    <div class="profile-image-container">
                                        <img src="../../../images/<?php echo $r['foto']; ?>" 
                                             alt="Foto Profil" 
                                             class="rounded-circle profile-image"
                                             style="width: 200px; height: 200px; object-fit: cover; border: 5px solid #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                                    </div>
                                    <h5 class="mt-3 mb-1"><?php echo $r['nama']; ?></h5>
                                    <p class="text-muted"><?php echo $r['jabatan']; ?></p>
                                </div>

                                <!-- Informasi Profil -->
                                <div class="col-md-8">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <tbody>
                                                <tr>
                                                    <td width="150px"><strong>No. KSE</strong></td>
                                                    <td><?php echo $r['id_karyawan']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Nama Lengkap</strong></td>
                                                    <td><?php echo $r['nama']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>TTL</strong></td>
                                                    <td><?php echo $r['tmp_tgl_lahir']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Jenis Kelamin</strong></td>
                                                    <td><?php echo $r['jenkel']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Agama</strong></td>
                                                    <td><?php echo isset($r['agama']) ? $r['agama'] : '-'; ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>No. Telepon</strong></td>
                                                    <td><?php echo $r['no_tel']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Divisi</strong></td>
                                                    <td><?php echo $r['jabatan']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Alamat</strong></td>
                                                    <td><?php echo $r['alamat']; ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <!-- Tombol Edit -->
                                    <div class="text-right mt-4">
                                        <a href="edit.php?id_karyawan=<?php echo $id; ?>" 
                                           class="btn btn-primary">
                                            <i class="fas fa-edit mr-2"></i>Edit Profil
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="row">
                <div class="col-md-12">
                    <div class="copyright">
                        <p>© KSE UINSU 2025</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border-radius: 15px;
    border: none;
    box-shadow: 0 0 20px rgba(0,0,0,0.08);
    margin-bottom: 30px;
}

.card-header {
    background: #fff;
    border-bottom: 1px solid #eef0f7;
    padding: 20px;
    border-radius: 15px 15px 0 0;
}

.card-title {
    color: #2b3144;
    font-weight: 600;
    margin: 0;
    font-size: 1.5rem;
}

.card-body {
    padding: 30px;
}

.profile-image-container {
    position: relative;
    display: inline-block;
}

.table td {
    padding: 15px;
    vertical-align: middle;
    border-top: 1px solid #eef0f7;
    color: #4f5d77;
}

.table td strong {
    color: #2b3144;
    font-weight: 600;
}

.btn-primary {
    background: #3b82f6;
    border: none;
    padding: 12px 25px;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background: #2563eb;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(59, 130, 246, 0.3);
}

.text-muted {
    color: #64748b !important;
}

.copyright {
    padding: 20px 0;
    text-align: center;
    color: #64748b;
    font-size: 14px;
}
</style>

<head>
    <!-- Required meta tags-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="au theme template">
    <meta name="author" content="Hau Nguyen">
    <meta name="keywords" content="au theme template">

    <!-- Title Page-->
    <title>Profil Beswan</title>

    <style>
        .card {
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0,0,0,0.05);
            border: none;
        }
        .card-body {
            padding: 2rem;
        }
        .table td {
            padding: 1rem;
            vertical-align: middle;
            border-top: 1px solid #f1f5f9;
        }
        .table td:first-child {
            font-weight: 600;
            color: #475569;
            width: 200px;
        }
        .table td:last-child {
            color: #1e293b;
        }
        .btn-primary {
            padding: 0.75rem 2rem;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(59, 130, 246, 0.3);
        }
        .profile-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
            text-align: center;
            margin-bottom: 2rem;
        }
        .img-fluid {
            border: 4px solid #fff;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
    </style>


    <!-- Fontfaces CSS-->
    <link href="css/font-face.css" rel="stylesheet" media="all">
    <link href="vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
    <link href="vendor/font-awesome-5/css/fontawesome-all.min.css" rel="stylesheet" media="all">
    <link href="vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">

    <!-- Bootstrap CSS-->
    <link href="../../../vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet" media="all">

    <!-- Vendor CSS-->
    <link href="../../vendor/animsition/animsition.min.css" rel="stylesheet" media="all">
    <link href="../../vendor/bootstrap-progressbar/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet" media="all">
    <link href="../../vendor/wow/animate.css" rel="stylesheet" media="all">
    <link href="../../vendor/css-hamburgers/hamburgers.min.css" rel="stylesheet" media="all">
    <link href="../../vendor/slick/slick.css" rel="stylesheet" media="all">
    <link href="../../vendor/select2/select2.min.css" rel="stylesheet" media="all">
    <link href="../../vendor/perfect-scrollbar/perfect-scrollbar.css" rel="stylesheet" media="all">

    <!-- Main CSS-->
    <link href="../../../css/theme.css" rel="stylesheet" media="all">

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
                            <a class="js-arrow" href="?m=awal">
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
                <a href="?m=awal">
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
                                <input class="au-input au-input--xl" type="text" name="search" value="Profil" readonly="" />
                           
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
                </div>
            </header>
            <!-- HEADER DESKTOP-->

            <!-- MAIN CONTENT-->
            <div class="main-content">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="overview-wrap">
                                    <h2 class="title-1" style="text-align: center;">Profil anda <?php echo $_SESSION['namasi']; ?></h2>
                                    <button class="au-btn au-btn-icon au-btn--blue">
                                        
                                </div>
                            </div>
                        </div>


                        <!-- FORM -->
                        <div class="row">
                           <div class="table-responsive table--no-card m-b-30">
                            <form action="modul/karyawan/keterangan_sv.php" method="post" enctype="multipart/form-data">
                                <div class="form-group">
                                	
                                	<?php
                                	$id = $_SESSION['idsi'];
                                	$sql = "SELECT * FROM tb_karyawan WHERE id_karyawan = '$id'";
                                	$query = mysqli_query($koneksi, $sql);
                                	$r = mysqli_fetch_array($query);

                                	 ?>

                                <div class="card">
                                    <div class="card-body">
                                        <table class="table table-borderless table-striped table-earning">
                                        <tbody>
                                              
                                            <tr>
                                                <td>No.KSE</td>
                                                <td>
                                                
                                                <?php echo $r['id_karyawan'];?>
                                                
                                            </td>
                                            </tr>
                                           
                                            <tr>
                                                <td>Nama</td>
                                                <td><?php echo $r['nama'];?></td>
                                            </tr>

                                            <tr>
                                            	<td>Tempat & tanggal lahir</td>
                                            	<td><?php echo $r['tmp_tgl_lahir'];?></td>
                                            </tr>

                                            <TR>
                                            	<td>Jenis Kelamin</td>
                                            	<td><?php echo $r['jenkel'];?></td>
                                            </TR>

                                             <tr>
                                                <td>Agama</td>
                                                <td><?php echo isset($r['agama']) ? $r['agama'] : '-';?></td>
                                            </tr>

                                           <tr>
                                              <td>Alamat</td>
                                              <td><?php echo $r['alamat'];?></td>
                                           </tr>

                                           <tr>
                                           	<td>Nomor telepon</td>
                                           	<td><?php echo $r['no_tel'];?></td>
                                           </tr>

                                           <tr>
                                           	<td>Divisi</td>
                                           	<td><?php echo $r['jabatan'];?> </td>
                                           </tr>

                                           <tr>
                                           	<td>Foto</td>
                                           	<td><img src="images/<?php echo $r['foto'];?>" class="img-fluid rounded" style="width: 128px;height: 128px;object-fit:cover;"></td>
                                           </tr>

                                        </tbody>
                                    </table>
                                    <div class="text-center mt-4">
                                        <a href="?m=karyawan&s=edit&id_karyawan=<?php echo $id;?>" class="btn btn-primary">
                                            <i class="fas fa-edit mr-2"></i>Edit Profil
                                        </a>
                                    </div>
                                </div>
                            </div>
                                        </div>
                            </form>
                                    
                                </div>    
                        </div>
                     

                        <!-- END FORM -->
                        
                        <div class="header-desktop">
                            <div class="col-md-12">
                                <div class="copyright">
                                    <p>KSE UINSU 2025 <a href="https://colorlib.com">Colorlib</a>.</p>
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
    <script src="../../../vendor/jquery-3.2.1.min.js"></script>
    <!-- Bootstrap JS-->
    <script src="../../vendor/bootstrap-4.1/popper.min.js"></script>
    <script src="../../vendor/bootstrap-4.1/bootstrap.min.js"></script>
    <!-- Vendor JS       -->
    <script src="../../vendor/slick/slick.min.js">
    </script>
    <script src="../../vendor/wow/wow.min.js"></script>
    <script src="../../vendor/animsition/animsition.min.js"></script>
    <script src="../../vendor/bootstrap-progressbar/bootstrap-progressbar.min.js">
    </script>
    <script src="../../vendor/counter-up/jquery.waypoints.min.js"></script>
    <script src="../../vendor/counter-up/jquery.counterup.min.js">
    </script>
    <script src="../../vendor/circle-progress/circle-progress.min.js"></script>
    <script src="../../vendor/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../../vendor/chartjs/Chart.bundle.min.js"></script>
    <script src="../../vendor/select2/select2.min.js">
    </script>

    <!-- Main JS-->
    <script src="../../js/main.js"></script>

</body>

</html>
<!-- end document-->
