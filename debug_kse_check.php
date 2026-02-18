<?php
require_once 'koneksi.php';

$sample = isset($argv[1]) ? $argv[1] : (isset($_GET['sample']) ? $_GET['sample'] : 'KSE.2490.14762');

echo "Sample input: $sample\n\n";

// Column type
$col_res = mysqli_query($koneksi, "SHOW COLUMNS FROM tb_karyawan LIKE 'id_karyawan'");
$col = $col_res ? mysqli_fetch_assoc($col_res) : null;
$col_type = $col['Type'] ?? '(not found)';
echo "Column type for tb_karyawan.id_karyawan: $col_type\n\n";

// List current ids
$res = mysqli_query($koneksi, "SELECT id_karyawan FROM tb_karyawan ORDER BY id_karyawan LIMIT 50");
if ($res) {
    echo "Current id_karyawan (first 50):\n";
    while ($r = mysqli_fetch_assoc($res)) {
        echo "- " . $r['id_karyawan'] . "\n";
    }
} else {
    echo "Error fetching current ids: " . mysqli_error($koneksi) . "\n";
}

echo "\nFormat check:\n";
if (stripos($col_type, 'int') !== false) {
    echo "PERINGATAN: id_karyawan masih INT. Ubah ke VARCHAR agar format KSE.xx.xxx tetap utuh.\n";
} else {
    echo "Kolom sudah string; simpan apa adanya (dengan titik).\n";
}

// Cek sample persis sesuai input
$stmt = mysqli_prepare($koneksi, "SELECT id_karyawan FROM tb_karyawan WHERE id_karyawan = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 's', $sample);
mysqli_stmt_execute($stmt);
$r = mysqli_stmt_get_result($stmt);
if ($r && mysqli_num_rows($r) > 0) {
    echo "Sample EXISTS di DB (dengan titik).\n";
} else {
    echo "Sample TIDAK ditemukan di DB (dengan titik).\n";
}
mysqli_stmt_close($stmt);

?>
