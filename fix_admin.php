<?php
require_once 'koneksi.php';

$username = 'ADMINKECE';
$password = 'ADMIN2025'; // Plaintext as per login logic
$role = 'super_admin';

// Check if role column exists
$check_col = mysqli_query($koneksi, "SHOW COLUMNS FROM tb_daftar LIKE 'role'");
if (mysqli_num_rows($check_col) == 0) {
    echo "Adding 'role' column...\n";
    mysqli_query($koneksi, "ALTER TABLE tb_daftar ADD COLUMN role VARCHAR(50) DEFAULT 'admin'");
}

// Check if user exists
$check_user = mysqli_query($koneksi, "SELECT * FROM tb_daftar WHERE username = '$username'");
if (mysqli_num_rows($check_user) > 0) {
    echo "Updating user '$username'...\n";
    $query = "UPDATE tb_daftar SET password = '$password', role = '$role' WHERE username = '$username'";
} else {
    echo "Creating user '$username'...\n";
    $query = "INSERT INTO tb_daftar (username, password, role) VALUES ('$username', '$password', '$role')";
}

if (mysqli_query($koneksi, $query)) {
    echo "Success! User '$username' is ready with role '$role'.\n";
} else {
    echo "Error: " . mysqli_error($koneksi) . "\n";
}
?>