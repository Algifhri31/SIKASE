<?php
// Script untuk langsung menambahkan admin ADMINKECE
include 'koneksi.php';

echo "<h2>🚀 Menambahkan Admin ADMINKECE</h2>";

// Cek koneksi
if (!$koneksi) {
    die("❌ Koneksi gagal: " . mysqli_connect_error());
}

// Langsung insert admin tanpa cek apapun
echo "➕ Menambahkan admin ADMINKECE...<br>";

$insert_query = "INSERT INTO tb_daftar (username, password) VALUES ('ADMINKECE', 'ADMIN2025')";
$result = mysqli_query($koneksi, $insert_query);

if ($result) {
    echo "✅ Admin berhasil ditambahkan!<br><br>";
} else {
    echo "❌ Error: " . mysqli_error($koneksi) . "<br>";
    
    // Jika error karena tabel tidak ada, buat tabel dulu
    if (strpos(mysqli_error($koneksi), "doesn't exist") !== false) {
        echo "🔧 Membuat tabel tb_daftar...<br>";
        
        $create_table = "CREATE TABLE `tb_daftar` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `username` varchar(255) NOT NULL,
            `password` varchar(255) NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        if (mysqli_query($koneksi, $create_table)) {
            echo "✅ Tabel berhasil dibuat!<br>";
            
            // Insert admin lagi
            if (mysqli_query($koneksi, $insert_query)) {
                echo "✅ Admin berhasil ditambahkan setelah membuat tabel!<br><br>";
            } else {
                echo "❌ Masih error: " . mysqli_error($koneksi) . "<br><br>";
            }
        } else {
            echo "❌ Gagal membuat tabel: " . mysqli_error($koneksi) . "<br><br>";
        }
    }
}

// Verifikasi admin ada
echo "🔍 Verifikasi admin di database:<br>";
$check_query = "SELECT * FROM tb_daftar WHERE username = 'ADMINKECE'";
$check_result = mysqli_query($koneksi, $check_query);

if ($check_result && mysqli_num_rows($check_result) > 0) {
    $admin = mysqli_fetch_assoc($check_result);
    echo "<div style='background: #d4edda; color: #155724; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h3>✅ ADMIN BERHASIL DITAMBAHKAN!</h3>";
    echo "<p><strong>ID:</strong> " . $admin['id'] . "</p>";
    echo "<p><strong>Username:</strong> " . $admin['username'] . "</p>";
    echo "<p><strong>Password:</strong> " . $admin['password'] . "</p>";
    echo "</div>";
    
    echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 8px; border-left: 5px solid #007bff;'>";
    echo "<h3>🎯 SEKARANG COBA LOGIN!</h3>";
    echo "<p><a href='login.php' style='background: #007bff; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-size: 18px; font-weight: bold;'>LOGIN SEKARANG</a></p>";
    echo "<p><strong>Username:</strong> ADMINKECE</p>";
    echo "<p><strong>Password:</strong> ADMIN2025</p>";
    echo "</div>";
    
} else {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 20px; border-radius: 8px;'>";
    echo "<h3>❌ ADMIN MASIH TIDAK DITEMUKAN!</h3>";
    echo "<p>Error: " . mysqli_error($koneksi) . "</p>";
    echo "</div>";
}

mysqli_close($koneksi);
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 50px auto;
    padding: 20px;
    background: #f8f9fa;
}

h2, h3 {
    color: #343a40;
}

a {
    display: inline-block;
    margin: 10px 0;
}
</style>