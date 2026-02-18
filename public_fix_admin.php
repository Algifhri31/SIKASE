<?php
require_once 'koneksi.php';

echo "<h1>Fixing Admin Account...</h1>";

// 1. Check/Add 'role' column
$check_col = mysqli_query($koneksi, "SHOW COLUMNS FROM tb_daftar LIKE 'role'");
if (mysqli_num_rows($check_col) == 0) {
    echo "Adding 'role' column...<br>";
    if (mysqli_query($koneksi, "ALTER TABLE tb_daftar ADD COLUMN role VARCHAR(50) DEFAULT 'admin'")) {
        echo "Column 'role' added successfully.<br>";
    } else {
        echo "Error adding column: " . mysqli_error($koneksi) . "<br>";
    }
} else {
    echo "Column 'role' already exists.<br>";
}

// 2. Upsert ADMINKECE
$username = 'ADMINKECE';
$password = 'ADMIN2025'; // Plaintext
$role = 'super_admin';

$check_user = mysqli_query($koneksi, "SELECT * FROM tb_daftar WHERE username = '$username'");
if (mysqli_num_rows($check_user) > 0) {
    echo "Updating user '$username'...<br>";
    $query = "UPDATE tb_daftar SET password = '$password', role = '$role' WHERE username = '$username'";
} else {
    echo "Creating user '$username'...<br>";
    $query = "INSERT INTO tb_daftar (username, password, role) VALUES ('$username', '$password', '$role')";
}

if (mysqli_query($koneksi, $query)) {
    echo "<h2 style='color: green;'>SUCCESS! User '$username' is ready with role '$role'.</h2>";
    echo "<p>Please <a href='login.php'>Login Here</a></p>";
} else {
    echo "<h2 style='color: red;'>Error: " . mysqli_error($koneksi) . "</h2>";
}
?>