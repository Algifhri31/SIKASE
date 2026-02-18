<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h2>🔍 Check Credentials - KSE System</h2>";

try {
    include 'koneksi.php';
    
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>✅ Database Connection OK</h3>";
    echo "</div>";
    
    // Cek tabel tb_daftar
    echo "<h3>Admin Users in Database:</h3>";
    $sql_admin = "SELECT * FROM tb_daftar";
    $result_admin = mysqli_query($koneksi, $sql_admin);
    
    if ($result_admin && mysqli_num_rows($result_admin) > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
        echo "<tr style='background: #f8f9fa;'><th>ID</th><th>Username</th><th>Password</th><th>Level</th><th>Status</th></tr>";
        
        while ($admin = mysqli_fetch_assoc($result_admin)) {
            $status = ($admin['username'] === 'ADMINKECE' && $admin['password'] === 'ADMIN2025') ? '✅ BENAR' : '❌ SALAH';
            $bg_color = ($admin['username'] === 'ADMINKECE' && $admin['password'] === 'ADMIN2025') ? '#d4edda' : '#f8d7da';
            
            echo "<tr style='background: $bg_color;'>";
            echo "<td>" . $admin['id'] . "</td>";
            echo "<td><strong>" . $admin['username'] . "</strong></td>";
            echo "<td><code>" . $admin['password'] . "</code></td>";
            echo "<td>" . ($admin['level'] ?? 'admin') . "</td>";
            echo "<td><strong>$status</strong></td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px;'>";
        echo "<p>❌ Tidak ada admin user ditemukan!</p>";
        echo "</div>";
    }
    
    // Cek tabel tb_karyawan
    echo "<h3>Sample Karyawan in Database:</h3>";
    $sql_karyawan = "SELECT id_karyawan, username, password, nama FROM tb_karyawan LIMIT 5";
    $result_karyawan = mysqli_query($koneksi, $sql_karyawan);
    
    if ($result_karyawan && mysqli_num_rows($result_karyawan) > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
        echo "<tr style='background: #f8f9fa;'><th>ID Karyawan</th><th>Username</th><th>Password</th><th>Nama</th></tr>";
        
        while ($karyawan = mysqli_fetch_assoc($result_karyawan)) {
            echo "<tr>";
            echo "<td>" . $karyawan['id_karyawan'] . "</td>";
            echo "<td><strong>" . $karyawan['username'] . "</strong></td>";
            echo "<td><code>" . $karyawan['password'] . "</code></td>";
            echo "<td>" . $karyawan['nama'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px;'>";
        echo "<p>⚠️ Belum ada data karyawan sample</p>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px;'>";
    echo "<h3>❌ Database Error</h3>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "</div>";
}

// Tampilkan kredensial yang benar
echo "<div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 25px; border-radius: 15px; margin: 20px 0; text-align: center;'>";
echo "<h2>🎯 Kredensial Login yang BENAR</h2>";
echo "<div style='display: flex; justify-content: space-around; flex-wrap: wrap; margin-top: 20px;'>";

echo "<div style='background: rgba(255,255,255,0.2); padding: 20px; border-radius: 10px; margin: 10px; min-width: 250px;'>";
echo "<h3>👨‍💼 ADMIN</h3>";
echo "<p style='font-size: 18px; margin: 5px 0;'>Username: <strong>ADMINKECE</strong></p>";
echo "<p style='font-size: 18px; margin: 5px 0;'>Password: <strong>ADMIN2025</strong></p>";
echo "</div>";

echo "<div style='background: rgba(255,255,255,0.2); padding: 20px; border-radius: 10px; margin: 10px; min-width: 250px;'>";
echo "<h3>👩‍🎓 KARYAWAN</h3>";
echo "<p style='font-size: 18px; margin: 5px 0;'>Username: <strong>siti_zahra</strong></p>";
echo "<p style='font-size: 18px; margin: 5px 0;'>Password: <strong>password123</strong></p>";
echo "</div>";

echo "</div>";
echo "</div>";

// Quick actions
echo "<div style='text-align: center; margin: 20px 0;'>";
echo "<a href='fix_admin_credentials.php' style='background: #dc3545; color: white; padding: 12px 24px; text-decoration: none; border-radius: 8px; display: inline-block; font-weight: 500; margin: 5px;'>🔧 Fix Admin Credentials</a>";
echo "<a href='setup_quick.php' style='background: #28a745; color: white; padding: 12px 24px; text-decoration: none; border-radius: 8px; display: inline-block; font-weight: 500; margin: 5px;'>⚡ Quick Setup</a>";
echo "<a href='login_simple.php' style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 24px; text-decoration: none; border-radius: 8px; display: inline-block; font-weight: 500; margin: 5px;'>🚀 Login</a>";
echo "</div>";

echo "<div style='background: #e9ecef; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
echo "<h4>📝 Langkah Troubleshooting:</h4>";
echo "<ol>";
echo "<li>Jika admin credentials salah → Klik <strong>Fix Admin Credentials</strong></li>";
echo "<li>Jika database kosong → Klik <strong>Quick Setup</strong></li>";
echo "<li>Jika sudah benar → Klik <strong>Login</strong></li>";
echo "</ol>";
echo "</div>";
?>