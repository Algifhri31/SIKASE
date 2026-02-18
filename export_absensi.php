<?php
session_start();
require_once("koneksi.php");

// Cek auth
if (!isset($_SESSION['username']) || !isset($_SESSION['level']) || $_SESSION['level'] !== 'admin') {
    header("location: login.php");
    exit();
}

// Get Filters
$filter_month = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$filter_year = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
$filter_date = isset($_GET['tanggal']) ? $_GET['tanggal'] : '';

// Build Where Clauses
$where_absen = "";
$where_ket = "";
if (!empty($filter_date)) {
    $where_absen = "WHERE DATE(a.waktu) = '$filter_date'";
    $where_ket = "WHERE DATE(k.waktu) = '$filter_date'";
    $filename_suffix = "Harian_" . $filter_date;
    $title = "Rekap Absensi Tanggal " . date('d F Y', strtotime($filter_date));
}
else {
    $where_absen = "WHERE MONTH(a.waktu) = '$filter_month' AND YEAR(a.waktu) = '$filter_year'";
    $where_ket = "WHERE MONTH(k.waktu) = '$filter_month' AND YEAR(k.waktu) = '$filter_year'";
    $filename_suffix = "Bulanan_" . $filter_year . "_" . $filter_month;
    $title = "Rekap Absensi Bulan " . date('F Y', strtotime("$filter_year-$filter_month-01"));
}

// Fetch Combined Data using UNION
$sql = "(SELECT a.id_karyawan as no_kse, a.nama as nama_lengkap, a.waktu, a.tipe, a.keterangan, a.lokasi 
         FROM tb_absen a 
         $where_absen)
        UNION ALL
        (SELECT k.id_karyawan as no_kse, COALESCE(kar.nama, k.nama) as nama_lengkap, k.waktu, k.keterangan as tipe, k.alasan as keterangan, '-' as lokasi 
         FROM tb_keterangan k 
         LEFT JOIN tb_karyawan kar ON k.id_karyawan = kar.id_karyawan
         $where_ket)
        ORDER BY waktu DESC, nama_lengkap ASC";
$query = mysqli_query($koneksi, $sql);

// Set Headers for Excel Download
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Rekap_Absensi_$filename_suffix.xls");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Export Data</title>
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
                <th style="width: 50px;">NO</th>
                <th style="width: 150px;">NO. KSE</th>
                <th style="width: 250px;">NAMA ANGGOTA</th>
                <th style="width: 150px;">WAKTU</th>
                <th style="width: 100px;">TIPE</th>
                <th style="width: 200px;">KETERANGAN</th>
                <th style="width: 200px;">LOKASI</th>
            </tr>
        </thead>
        <tbody>
            <?php
$no = 1;
if (mysqli_num_rows($query) > 0) {
    while ($row = mysqli_fetch_array($query)) {
        $google_maps_link = !empty($row['lokasi']) ? "https://maps.google.com/?q=" . $row['lokasi'] : '-';
?>
            <tr>
                <td style="text-align: center; mso-number-format:'0';"><?php echo $no++; ?></td>
                <td style="mso-number-format:'@';"><?php echo htmlspecialchars($row['no_kse']); ?></td>
                <td><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
                <td style="text-align: center;"><?php echo date('d-m-Y H:i:s', strtotime($row['waktu'])); ?></td>
                <td style="text-align: center;"><?php echo strtoupper($row['tipe']); ?></td>
                <td><?php echo htmlspecialchars($row['keterangan']); ?></td>
                <td><?php echo $google_maps_link; ?></td>
            </tr>
            <?php
    }
}
else {
    echo "<tr><td colspan='7' style='text-align:center'>Tidak ada data absensi untuk periode ini.</td></tr>";
}
?>
        </tbody>
    </table>
</body>
</html>
