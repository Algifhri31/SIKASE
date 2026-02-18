<?php
require 'koneksi.php';

// 1. Add role column if it doesn't exist
$check_col = mysqli_query($koneksi, "SHOW COLUMNS FROM tb_daftar LIKE 'role'");
if (mysqli_num_rows($check_col) == 0) {
    echo "Adding 'role' column...\n";
    $sql = "ALTER TABLE tb_daftar ADD COLUMN role VARCHAR(20) DEFAULT 'admin' AFTER password";
    if (mysqli_query($koneksi, $sql)) {
        echo "Column 'role' added successfully.\n";
    } else {
        echo "Error adding column: " . mysqli_error($koneksi) . "\n";
    }
} else {
    echo "Column 'role' already exists.\n";
}

// 2. Set ADMINKECE as super_admin
echo "Updating ADMINKECE role...\n";
$sql_update = "UPDATE tb_daftar SET role='super_admin' WHERE username='ADMINKECE'";
if (mysqli_query($koneksi, $sql_update)) {
    echo "ADMINKECE set to super_admin.\n";
} else {
    echo "Error updating ADMINKECE: " . mysqli_error($koneksi) . "\n";
}

// 3. Set others as admin (if null or empty)
$sql_update_others = "UPDATE tb_daftar SET role='admin' WHERE role IS NULL OR role = ''";
mysqli_query($koneksi, $sql_update_others);

echo "\n---Updated tb_daftar---\n";
$res = mysqli_query($koneksi, "SELECT * FROM tb_daftar");
while ($row = mysqli_fetch_assoc($res)) {
    print_r($row);
}
?>