<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'koneksi.php';

echo "<h2>🔧 Fix Admin Final - KSE System</h2>";
echo "<p>Menggunakan kredensial admin yang diminta...</p>";

// Hapus semua admin lama
echo "<h3>1. Hapus Admin Lama:</h3>";
$sql_delete_all = "DELETE FROM tb_daftar";
$result_delete = mysqli_query($koneksi, $sql_delete_all);

if ($result_delete) {
    echo "<p>✅ Semua admin lama berhasil dihapus</p>";
} else {
    echo "<p>⚠️ Gagal menghapus admin lama: " . mysqli_error($koneksi) . "</p>";
}

// Buat admin baru dengan kredensial yang diminta
echo "<h3>2. Buat Admin Baru:</h3>";
$new_username = 'ADMINKECE';
$new_password = 'ADMIN2025';

$sql_create_new_admin = "INSERT INTO tb_daftar (username, password, level) VALUES ('$new_username', '$new_password', 'admin')";

if (mysqli_query($koneksi, $sql_create_new_admin)) {
    echo "<div style='background: #d4edda; padding: 20px; border-radius: 10px; margin: 15px 0;'>";
    echo "<h4>✅ Admin Baru Berhasil Dibuat!</h4>";
    echo "<p><strong>Kredensial Login Admin Baru:</strong></p>";
    echo "<div style='background: white; padding: 15px; border-radius: 8px; font-family: monospace; font-size: 18px;'>";
    echo "Username: <strong style='color: #007bff;'>$new_username</strong><br>";
    echo "Password: <strong style='color: #28a745;'>$new_password</strong>";
    echo "</div>";
    echo "</div>";
} else {
    echo "<p>❌ Gagal membuat admin baru: " . mysqli_error($koneksi) . "</p>";
}

// Verifikasi admin baru
echo "<h3>3. Verifikasi Admin Baru:</h3>";
$sql_verify = "SELECT * FROM tb_daftar WHERE username = '$new_username'";
$result_verify = mysqli_query($koneksi, $sql_verify);

if ($result_verify && mysqli_num_rows($result_verify) > 0) {
    $admin = mysqli_fetch_assoc($result_verify);
    echo "<div style='background: #d1ecf1; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h4>✅ Verifikasi Berhasil!</h4>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
    echo "<tr style='background: #f8f9fa;'><th>Field</th><th>Value</th></tr>";
    echo "<tr><td>ID</td><td>" . $admin['id'] . "</td></tr>";
    echo "<tr><td>Username</td><td><strong>" . $admin['username'] . "</strong></td></tr>";
    echo "<tr><td>Password</td><td><strong>" . $admin['password'] . "</strong></td></tr>";
    echo "<tr><td>Level</td><td>" . ($admin['level'] ?? 'admin') . "</td></tr>";
    echo "</table>";
    echo "</div>";
} else {
    echo "<p>❌ Verifikasi gagal - admin user tidak ditemukan</p>";
}

// Test login dengan kredensial baru
echo "<h3>4. Test Login:</h3>";
$sql_test_login = "SELECT * FROM tb_daftar WHERE username = '$new_username' AND password = '$new_password'";
$result_test_login = mysqli_query($koneksi, $sql_test_login);

if ($result_test_login && mysqli_num_rows($result_test_login) > 0) {
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h4>✅ Test Login Berhasil!</h4>";
    echo "<p>Kredensial admin baru dapat digunakan untuk login.</p>";
    echo "</div>";
} else {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h4>❌ Test Login Gagal!</h4>";
    echo "<p>Ada masalah dengan kredensial admin baru.</p>";
    echo "</div>";
}

// Tampilkan semua admin yang ada sekarang
echo "<h3>5. Daftar Admin Saat Ini:</h3>";
$sql_all_admin = "SELECT * FROM tb_daftar";
$result_all_admin = mysqli_query($koneksi, $sql_all_admin);

if ($result_all_admin && mysqli_num_rows($result_all_admin) > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
    echo "<tr style='background: #f8f9fa;'><th>ID</th><th>Username</th><th>Password</th><th>Level</th></tr>";
    while ($admin = mysqli_fetch_assoc($result_all_admin)) {
        echo "<tr>";
        echo "<td>" . $admin['id'] . "</td>";
        echo "<td><strong>" . $admin['username'] . "</strong></td>";
        echo "<td><strong>" . $admin['password'] . "</strong></td>";
        echo "<td>" . ($admin['level'] ?? 'admin') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>❌ Tidak ada admin ditemukan</p>";
}

// Tampilkan kredensial final
echo "<div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 15px; margin: 20px 0; text-align: center;'>";
echo "<h2>🎯 KREDENSIAL LOGIN FINAL</h2>";
echo "<div style='background: rgba(255,255,255,0.2); padding: 25px; border-radius: 10px; margin: 20px auto; max-width: 400px;'>";
echo "<h3>👨‍💼 ADMIN</h3>";
echo "<p style='font-size: 24px; margin: 10px 0;'>Username: <strong>$new_username</strong></p>";
echo "<p style='font-size: 24px; margin: 10px 0;'>Password: <strong>$new_password</strong></p>";
echo "</div>";
echo "</div>";

echo "<div style='text-align: center; margin: 20px 0;'>";
echo "<a href='login_simple.php' style='background: #28a745; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; display: inline-block; font-weight: 600; font-size: 18px; margin: 5px;'>🚀 LOGIN SEKARANG</a>";
echo "</div>";

echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
echo "<h4>📝 Catatan Penting:</h4>";
echo "<ul>";
echo "<li>Username dan password bersifat <strong>case-sensitive</strong></li>";
echo "<li>Pastikan tidak ada spasi di awal atau akhir</li>";
echo "<li>Gunakan kredensial persis seperti yang ditampilkan di atas</li>";
echo "</ul>";
echo "</div>";
?>