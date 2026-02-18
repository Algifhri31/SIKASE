<?php
require 'koneksi.php';

echo "<h2>Schema for tb_karyawan</h2>\n<pre>";
$res = mysqli_query($koneksi, "DESCRIBE tb_karyawan");
if (!$res) {
    echo "Error: " . mysqli_error($koneksi);
    exit;
}
while ($row = mysqli_fetch_assoc($res)) {
    print_r($row);
    echo "\n";
}
echo "</pre>";

// Also show CREATE TABLE
echo "<h2>CREATE TABLE tb_karyawan</h2>\n<pre>";
$res2 = mysqli_query($koneksi, "SHOW CREATE TABLE tb_karyawan");
if ($res2 && $row2 = mysqli_fetch_assoc($res2)) {
    echo htmlspecialchars($row2['Create Table']);
} else {
    echo "Error: " . mysqli_error($koneksi);
}

echo "</pre>";
?>