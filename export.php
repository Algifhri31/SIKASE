<?php 
session_start();
require_once("koneksi.php");
error_reporting(0);

// Cek Login
if (!isset($_SESSION['username']) || !isset($_SESSION['level']) || $_SESSION['level'] !== 'admin') {
    header("location: login.php");
    exit();
}

// Cek Role Super Admin
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'admin';
if ($role !== 'super_admin') {
    echo "<script>alert('Anda tidak memiliki akses ke halaman ini! Halaman ini hanya untuk Super Admin.'); window.location.href='admin_dashboard_fixed.php';</script>";
    exit();
}
?>
<html>
<head>
  <title>Rekap Absen</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.6.5/css/buttons.dataTables.min.css">
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js"></script>
  <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
  <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
  <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.html5.min.js"></script>
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.print.min.js"></script>
</head>

<body>
<div class="container">
    <h2>Rekap Absensi PAGUYUBAN KSE UINSU</h2>
    <h4>Jl. Letda Sujono Gg. Kapuk, Bandar Selamat, Kec. Medan Tembung, Kota Medan, Sumatera Utara</h4>
    <div class="data-tables datatable-dark">
        <table id="mauexport" class="table table-borderless table-striped table-earning">
            <thead>
                <tr>
                    <th>No</th>
                    <th>No.KSE</th>
                    <th>Nama</th>
                    <th>Waktu</th>
                    <th>Keterangan</th>
                    <th>Kegiatan</th>
                    <th>Lokasi</th>
                    <th>Tipe</th>
                    <!-- <th>Aksi</th> -->
                </tr>
            </thead>
            <tbody>
                <?php 
                include 'koneksi.php';
                $sql = "SELECT * FROM tb_absen";
                $query = mysqli_query($koneksi, $sql);

                $no = 1;
                while ($row = mysqli_fetch_array($query)) {
                ?>
                <tr>
                    <td><?php echo $no; ?></td>
                    <td><?php echo $row['id_karyawan']; ?></td>
                    <td><?php echo $row['nama']; ?></td>
                    <td><?php echo $row['waktu']; ?></td>
                    <td><?php echo $row['keterangan']; ?></td>
                    <td><?php echo $row['kegiatan']; ?></td>
                    <td><?php echo $row['lokasi']; ?></td>
                    <td><?php echo $row['tipe']; ?></td>
                    <!-- <td>
                        <a href="absen/hapus_absen.php?id=<?php echo $row['id']; ?>">
                            <button class="btn btn-danger" onclick="return confirm('Yakin ingin dihapus?');">Hapus</button>
                        </a>
                    </td> -->
                </tr>
                <?php 
                    $no++;
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#mauexport').DataTable({
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
});
</script>

</body>
</html>