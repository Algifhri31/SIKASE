<?php
// Script untuk mengecek dan menambahkan admin ADMINKECE
include 'koneksi.php';

echo "<h2>🔍 Cek Status Admin</h2>";

// Cek semua admin yang ada
echo "<h3>Admin yang ada di database:</h3>";
$admin_list = mysqli_query($koneksi, "SELECT * FROM tb_daftar ORDER BY id");

if (mysqli_num_rows($admin_list) > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 20px 0;'>";
    echo "<tr style='background: #f2f2f2;'>";
    echo "<th style='padding: 10px;'>ID</th>";
    echo "<th style='padding: 10px;'>Username</th>";
    echo "<th style='padding: 10px;'>Password</th>";
    echo "</tr>";
    
    $found_adminkece = false;
    while ($admin = mysqli_fetch_assoc($admin_list)) {
        echo "<tr>";
        echo "<td style='padding: 10px; text-align: center;'>" . $admin['id'] . "</td>";
        echo "<td style='padding: 10px;'>" . htmlspecialchars($admin['username']) . "</td>";
        echo "<td style='padding: 10px;'>" . htmlspecialchars($admin['password']) . "</td>";
        echo "</tr>";
        
        if ($admin['username'] == 'ADMINKECE') {
            $found_adminkece = true;
        }
    }
    echo "</table>";
    
    if ($found_adminkece) {
        echo "<p style='color: green; font-weight: bold;'>✅ Admin ADMINKECE sudah ada di database!</p>";
    } else {
        echo "<p style='color: red; font-weight: bold;'>❌ Admin ADMINKECE belum ada di database!</p>";
        echo "<p>Menambahkan admin ADMINKECE sekarang...</p>";
        
        // Tambahkan admin ADMINKECE
        $username = 'ADMINKECE';
        $password = 'ADMIN2025';
        
        $insert_sql = "INSERT INTO tb_daftar (username, password) VALUES (?, ?)";
        $insert_stmt = mysqli_prepare($koneksi, $insert_sql);
        mysqli_stmt_bind_param($insert_stmt, "ss", $username, $password);
        
        if (mysqli_stmt_execute($insert_stmt)) {
            echo "<p style='color: green; font-weight: bold;'>✅ Admin ADMINKECE berhasil ditambahkan!</p>";
            echo "<div style='background: #e8f5e8; padding: 15px; border-radius: 5px; border-left: 4px solid #4caf50; margin: 20px 0;'>";
            echo "<h4>Kredensial Login Admin:</h4>";
            echo "<ul>";
            echo "<li><strong>Username:</strong> ADMINKECE</li>";
            echo "<li><strong>Password:</strong> ADMIN2025</li>";
            echo "</ul>";
            echo "</div>";
        } else {
            echo "<p style='color: red; font-weight: bold;'>❌ Gagal menambahkan admin: " . mysqli_error($koneksi) . "</p>";
        }
        mysqli_stmt_close($insert_stmt);
    }
    
} else {
    echo "<p>Tidak ada admin di database. Menambahkan admin ADMINKECE...</p>";
    
    // Tambahkan admin ADMINKECE
    $username = 'ADMINKECE';
    $password = 'ADMIN2025';
    
    $insert_sql = "INSERT INTO tb_daftar (username, password) VALUES (?, ?)";
    $insert_stmt = mysqli_prepare($koneksi, $insert_sql);
    mysqli_stmt_bind_param($insert_stmt, "ss", $username, $password);
    
    if (mysqli_stmt_execute($insert_stmt)) {
        echo "<p style='color: green; font-weight: bold;'>✅ Admin ADMINKECE berhasil ditambahkan!</p>";
        echo "<div style='background: #e8f5e8; padding: 15px; border-radius: 5px; border-left: 4px solid #4caf50; margin: 20px 0;'>";
        echo "<h4>Kredensial Login Admin:</h4>";
        echo "<ul>";
        echo "<li><strong>Username:</strong> ADMINKECE</li>";
        echo "<li><strong>Password:</strong> ADMIN2025</li>";
        echo "</ul>";
        echo "</div>";
    } else {
        echo "<p style='color: red; font-weight: bold;'>❌ Gagal menambahkan admin: " . mysqli_error($koneksi) . "</p>";
    }
    mysqli_stmt_close($insert_stmt);
}

echo "<hr>";
echo "<h3>📋 Langkah Selanjutnya:</h3>";
echo "<ol>";
echo "<li>Buka halaman <a href='login.php' target='_blank'>login.php</a></li>";
echo "<li>Masukkan username: <strong>ADMINKECE</strong></li>";
echo "<li>Masukkan password: <strong>ADMIN2025</strong></li>";
echo "<li>Klik tombol Masuk</li>";
echo "</ol>";

mysqli_close($koneksi);
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 900px;
    margin: 30px auto;
    padding: 20px;
    background: #f5f5f5;
}

h2, h3 {
    color: #333;
}

table {
    background: white;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

th {
    background: #667eea !important;
    color: white !important;
}

tr:nth-child(even) {
    background: #f9f9f9;
}

a {
    color: #667eea;
    font-weight: bold;
}

ol {
    background: #fff3cd;
    padding: 20px;
    border-radius: 5px;
    border-left: 4px solid #ffc107;
}

ol li {
    margin: 10px 0;
    font-weight: 500;
}
</style>