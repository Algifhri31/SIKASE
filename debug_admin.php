<?php
require_once 'koneksi.php';

echo "--- CHECKING USER 'ADMINKECE' ---\n";
$query = "SELECT * FROM tb_daftar WHERE username = 'ADMINKECE'";
$result = mysqli_query($koneksi, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    echo "User Found:\n";
    print_r($user);
} else {
    echo "User 'ADMINKECE' NOT FOUND.\n";
}

echo "\n--- CHECKING TABLE STRUCTURE 'tb_daftar' ---\n";
$query = "DESCRIBE tb_daftar";
$result = mysqli_query($koneksi, $query);
while ($row = mysqli_fetch_assoc($result)) {
    echo $row['Field'] . " (" . $row['Type'] . ")\n";
}
?>