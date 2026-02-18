<?php
require_once 'koneksi.php';

echo "Tables in database:\n";
$result = mysqli_query($koneksi, "SHOW TABLES");
while ($row = mysqli_fetch_row($result)) {
    echo $row[0] . "\n";
}

echo "\nChecking tb_daftar for ADMINKECE:\n";
$username = 'ADMINKECE';
$sql = "SELECT * FROM tb_daftar WHERE username = '$username'";
$result = mysqli_query($koneksi, $sql);

if ($result) {
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        echo "User found: " . print_r($user, true) . "\n";
        if ($user['password'] === 'ADMIN2025') {
            echo "Password matches!\n";
        } else {
            echo "Password does NOT match. Stored: " . $user['password'] . "\n";
        }
    } else {
        echo "User ADMINKECE not found.\n";
        
        echo "Listing all users in tb_daftar:\n";
        $all = mysqli_query($koneksi, "SELECT * FROM tb_daftar");
        while ($row = mysqli_fetch_assoc($all)) {
            echo print_r($row, true) . "\n";
        }
    }
} else {
    echo "Query failed: " . mysqli_error($koneksi) . "\n";
}
?>