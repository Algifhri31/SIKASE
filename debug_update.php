<?php
echo "Starting script...\n";
require 'koneksi.php';
echo "Connected to DB.\n";

$sql = "ALTER TABLE tb_daftar ADD COLUMN role VARCHAR(20) DEFAULT 'admin' AFTER password";
if (mysqli_query($koneksi, $sql)) {
    echo "Column 'role' added.\n";
} else {
    echo "Error adding column (might exist): " . mysqli_error($koneksi) . "\n";
}

$sql_update = "UPDATE tb_daftar SET role='super_admin' WHERE username='ADMINKECE'";
mysqli_query($koneksi, $sql_update);
echo "Updated ADMINKECE.\n";

$sql_check = "SELECT * FROM tb_daftar";
$res = mysqli_query($koneksi, $sql_check);
while($row = mysqli_fetch_assoc($res)) {
    print_r($row);
}
?>