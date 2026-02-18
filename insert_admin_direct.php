<?php
// Insert admin langsung ke database
include 'koneksi.php';

echo "<h2>🔧 Insert Admin Langsung</h2>";

// Cek koneksi
if (!$koneksi) {
    die("❌ Koneksi gagal: " . mysqli_connect_error());
}
echo "✅ Koneksi database berhasil<br><br>";

// Cek apakah tabel tb_daftar ada
$check_table = mysqli_query($koneksi, "SHOW TABLES LIKE 'tb_daftar'");
if (mysqli_num_rows($check_table) == 0) {
    echo "❌ Tabel tb_daftar tidak ada. Membuat tabel...<br>";
    
    $create_table = "CREATE TABLE `tb_daftar` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `username` varchar(255) NOT NULL,
        `password` varchar(255) NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    
    if (mysqli_query($koneksi, $create_table)) {
        echo "✅ Tabel tb_daftar berhasil dibuat<br><br>";
    } else {
        die("❌ Gagal membuat tabel: " . mysqli_error($koneksi));
    }
} else {
    echo "✅ Tabel tb_daftar sudah ada<br><br>";
}

// Hapus admin ADMINKECE jika sudah ada
echo "🗑️ Menghapus admin ADMINKECE lama...<br>";
mysqli_query($koneksi, "DELETE FROM tb_daftar WHERE username = 'ADMINKECE'");
echo "✅ Admin lama dihapus<br><br>";

// Insert admin baru dengan query langsung
echo "➕ Menambahkan admin ADMINKECE baru...<br>";
$insert_query = "INSERT INTO tb_daftar (username, password) VALUES ('ADMINKECE', 'ADMIN2025')";

if (mysqli_query($koneksi, $insert_query)) {
    echo "✅ Admin ADMINKECE berhasil ditambahkan!<br><br>";
} else {
    echo "❌ Gagal menambahkan admin: " . mysqli_error($koneksi) . "<br><br>";
}

// Verifikasi dengan SELECT
echo "🔍 Verifikasi admin di database:<br>";
$select_query = "SELECT * FROM tb_daftar";
$result = mysqli_query($koneksi, $select_query);

if ($result) {
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0; background: white;'>";
    echo "<tr style='background: #007bff; color: white;'>";
    echo "<th style='padding: 10px;'>ID</th>";
    echo "<th style='padding: 10px;'>Username</th>";
    echo "<th style='padding: 10px;'>Password</th>";
    echo "</tr>";
    
    $admin_found = false;
    while ($row = mysqli_fetch_assoc($result)) {
        $bg = ($row['username'] == 'ADMINKECE') ? 'background: #d4edda;' : '';
        echo "<tr style='$bg'>";
        echo "<td style='padding: 10px; text-align: center;'>" . $row['id'] . "</td>";
        echo "<td style='padding: 10px;'>" . $row['username'] . "</td>";
        echo "<td style='padding: 10px;'>" . $row['password'] . "</td>";
        echo "</tr>";
        
        if ($row['username'] == 'ADMINKECE') {
            $admin_found = true;
        }
    }
    echo "</table>";
    
    if ($admin_found) {
        echo "<div style='background: #d4edda; color: #155724; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
        echo "<h3>✅ ADMIN BERHASIL DITAMBAHKAN!</h3>";
        echo "<p><strong>Username:</strong> ADMINKECE</p>";
        echo "<p><strong>Password:</strong> ADMIN2025</p>";
        echo "</div>";
    } else {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 20px; border-radius: 8px;'>";
        echo "<h3>❌ ADMIN TIDAK DITEMUKAN SETELAH INSERT!</h3>";
        echo "</div>";
    }
} else {
    echo "❌ Error SELECT: " . mysqli_error($koneksi) . "<br>";
}

// Test query yang sama seperti di proses_login.php
echo "<hr>";
echo "<h3>🧪 Test Query Login:</h3>";
$test_username = 'ADMINKECE';
$login_query = "SELECT * FROM tb_daftar WHERE username = '$test_username'";
echo "Query: <code>$login_query</code><br>";

$login_result = mysqli_query($koneksi, $login_query);
if ($login_result) {
    $rows = mysqli_num_rows($login_result);
    echo "Rows found: $rows<br>";
    
    if ($rows > 0) {
        $user_data = mysqli_fetch_assoc($login_result);
        echo "✅ User ditemukan!<br>";
        echo "DB Username: '" . $user_data['username'] . "'<br>";
        echo "DB Password: '" . $user_data['password'] . "'<br>";
        
        if ($user_data['password'] == 'ADMIN2025') {
            echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
            echo "✅ <strong>PASSWORD COCOK! LOGIN SEHARUSNYA BERHASIL!</strong>";
            echo "</div>";
        } else {
            echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
            echo "❌ <strong>PASSWORD TIDAK COCOK!</strong>";
            echo "</div>";
        }
    } else {
        echo "❌ User tidak ditemukan dengan query tersebut<br>";
    }
} else {
    echo "❌ Error query: " . mysqli_error($koneksi) . "<br>";
}

echo "<hr>";
echo "<div style='background: #fff3cd; padding: 20px; border-radius: 8px; border-left: 5px solid #ffc107;'>";
echo "<h3>🚀 SEKARANG COBA LOGIN LAGI!</h3>";
echo "<ol style='font-size: 16px;'>";
echo "<li><strong>Buka:</strong> <a href='login.php' target='_blank' style='color: #007bff;'>login.php</a></li>";
echo "<li><strong>Username:</strong> ADMINKECE</li>";
echo "<li><strong>Password:</strong> ADMIN2025</li>";
echo "<li><strong>Klik Masuk</strong></li>";
echo "</ol>";
echo "<p style='color: #856404;'><strong>Jika masih gagal, mungkin ada masalah di prepared statement. Saya akan buat versi tanpa prepared statement.</strong></p>";
echo "</div>";

mysqli_close($koneksi);
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 1000px;
    margin: 20px auto;
    padding: 20px;
    background: #f8f9fa;
}

code {
    background: #f1f3f4;
    padding: 2px 6px;
    border-radius: 3px;
    font-family: monospace;
}

table {
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

tr:nth-child(even) {
    background: #f8f9fa;
}
</style>