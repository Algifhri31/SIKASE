<?php
require_once 'koneksi.php';

echo "Checking database structure...\n";

// Check if role column exists
$result = mysqli_query($koneksi, "SHOW COLUMNS FROM tb_daftar LIKE 'role'");
if (mysqli_num_rows($result) == 0) {
    echo "Adding role column to tb_daftar...\n";
    mysqli_query($koneksi, "ALTER TABLE tb_daftar ADD COLUMN role VARCHAR(20) DEFAULT 'admin'");
} else {
    echo "Role column exists.\n";
}

// Ensure ADMINKECE exists
$username = 'ADMINKECE';
$password = 'ADMIN2025';
$role = 'super_admin';

$check = mysqli_query($koneksi, "SELECT * FROM tb_daftar WHERE username = '$username'");
if (mysqli_num_rows($check) > 0) {
    echo "Updating ADMINKECE...\n";
    $query = "UPDATE tb_daftar SET password = '$password', role = '$role' WHERE username = '$username'";
    mysqli_query($koneksi, $query);
} else {
    echo "Creating ADMINKECE...\n";
    $query = "INSERT INTO tb_daftar (username, password, role) VALUES ('$username', '$password', '$role')";
    mysqli_query($koneksi, $query);
}

echo "Done.\n";
?>
