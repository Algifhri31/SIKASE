<?php
session_start();
require_once("koneksi.php");

// Cek auth
if (!isset($_SESSION['username']) || !isset($_SESSION['level']) || $_SESSION['level'] !== 'admin') {
    header("location: login.php");
    exit();
}

// Get Filters
$filter_month = isset($_GET['bulan']) ? $_GET['bulan'] : '';
$filter_year = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';

// Build Query
$where_clause = "WHERE YEAR(tb_keterangan.tanggal) = '$filter_year'";
$filename_suffix = "Keterangan_" . $filter_year;
$title = "Rekap Data Keterangan Tahun " . $filter_year;

if (!empty($filter_month)) {
    $where_clause .= " AND MONTH(tb_keterangan.tanggal) = '$filter_month'";
    $filename_suffix .= "_" . $filter_month;
    $title = "Rekap Data Keterangan Bulan " . date('F', mktime(0, 0, 0, $filter_month, 10)) . " " . $filter_year;
}

if (!empty($search)) {
    $where_clause .= " AND (tb_keterangan.nama LIKE '%$search%' OR tb_keterangan.id_karyawan LIKE '%$search%')";
}

// Set Headers for Excel Download
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Rekap_Keterangan_$filename_suffix.xls");

// Fetch Data
$sql = "SELECT tb_keterangan.*, tb_karyawan.nama as nama_asli 
        FROM tb_keterangan 
        LEFT JOIN tb_karyawan ON tb_keterangan.id_karyawan = tb_karyawan.id_karyawan 
        $where_clause 
        ORDER BY tb_keterangan.tanggal DESC";
$query = mysqli_query($koneksi, $sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Export Data Keterangan</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        h2, h3 { text-align: center; }
    </style>
</head>
<body>
    <center>
        <h2>PAGUYUBAN KSE UINSU</h2>
        <h3><?php echo $title; ?></h3>
    </center>
    <br>
    <table>
        <thead>
            <tr>
                <th>NO</th>
                <th>ID BESWAN</th>
                <th>NAMA</th>
                <th>TANGGAL</th>
                <th>KETERANGAN</th>
                <th>ALASAN</th>
                <th>BUKTI (FOTO)</th>
            </tr>
        </thead>
        <tbody>
            <?php
$no = 1;
if (mysqli_num_rows($query) > 0) {
    while ($row = mysqli_fetch_array($query)) {
        $bukti = !empty($row['bukti']) ? "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/uploads/" . $row['bukti'] : '-';
?>
            <tr>
                <td style="text-align: center;"><?php echo $no++; ?></td>
                <td><?php echo htmlspecialchars($row['id_karyawan']); ?></td>
                <td><?php echo htmlspecialchars($row['nama']); ?></td>
                <td><?php echo date('d-m-Y', strtotime($row['tanggal'])); ?></td>
                <td><?php echo strtoupper($row['keterangan']); ?></td>
                <td><?php echo htmlspecialchars($row['alasan']); ?></td>
                <td><?php echo $bukti; ?></td>
            </tr>
            <?php
    }
}
else {
    echo "<tr><td colspan='7' style='text-align:center'>Tidak ada data.</td></tr>";
}
?>
        </tbody>
    </table>
</body>
</html>
