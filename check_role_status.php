<?php
include 'koneksi.php';

// Check columns in tb_daftar
$result = mysqli_query($koneksi, "SHOW COLUMNS FROM tb_daftar");
echo "Columns in tb_daftar:\n";
while ($row = mysqli_fetch_assoc($result)) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}

// Check ADMINKECE data
$user = mysqli_query($koneksi, "SELECT * FROM tb_daftar WHERE username = 'ADMINKECE'");
$userData = mysqli_fetch_assoc($user);
echo "\nData ADMINKECE:\n";
print_r($userData);
?>