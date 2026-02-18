<?php
require_once 'koneksi.php';

$username = 'ADMINKECE';
$password = 'ADMIN2025';

// Check if user already exists (just to be safe, though we know it doesn't)
$check = mysqli_query($koneksi, "SELECT * FROM tb_daftar WHERE username = '$username'");
if (mysqli_num_rows($check) > 0) {
    echo "User $username already exists. Updating password...\n";
    $update = mysqli_query($koneksi, "UPDATE tb_daftar SET password = '$password' WHERE username = '$username'");
    if ($update) {
        echo "Password updated successfully.\n";
    } else {
        echo "Error updating password: " . mysqli_error($koneksi) . "\n";
    }
} else {
    echo "Creating user $username...\n";
    $insert = mysqli_query($koneksi, "INSERT INTO tb_daftar (username, password) VALUES ('$username', '$password')");
    if ($insert) {
        echo "User created successfully.\n";
    } else {
        echo "Error creating user: " . mysqli_error($koneksi) . "\n";
    }
}

// Verify
$result = mysqli_query($koneksi, "SELECT * FROM tb_daftar WHERE username = '$username'");
if ($row = mysqli_fetch_assoc($result)) {
    echo "Verification: User found -> ID: " . $row['id'] . ", Username: " . $row['username'] . ", Password: " . $row['password'] . "\n";
} else {
    echo "Verification failed: User not found.\n";
}
?>