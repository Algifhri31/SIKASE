<?php
require 'koneksi.php';

echo "---tb_daftar structure (Admins)---\n";
$res = mysqli_query($koneksi, "DESCRIBE tb_daftar");
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        echo $row['Field'] . " | " . $row['Type'] . "\n";
    }
} else {
    echo "Error checking tb_daftar: " . mysqli_error($koneksi) . "\n";
}

echo "\n---Existing Admins---\n";
$res2 = mysqli_query($koneksi, "SELECT * FROM tb_daftar");
if ($res2) {
    while ($row2 = mysqli_fetch_assoc($res2)) {
        print_r($row2);
    }
} else {
    echo "Error fetching admins: " . mysqli_error($koneksi) . "\n";
}
?>