<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'koneksi.php';

echo "<h2>🔧 Fix Admin Credentials - KSE System</h2>";

// Cek admin yang ada
echo "<h3>1. Cek Admin yang Ada:</h3>";
$sql_check_admin = "SELECT * FROM tb_daftar";
$result_check_admin = mysqli_query($koneksi, $sql_check_admin);

if ($result_check_admin && mysqli_num_rows($result_check_admin) > 0) {
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>ID</th><th>Username</th><th>Password</th><th>Level</th></tr>";
    while ($admin = mysqli_fetch_assoc($result_check_admin)) {
        echo "<tr>";
        echo "<td>" . $admin['id'] . "</td>";
        echo "<td><strong>" . $admin['username'] . "</strong></td>";
        echo "<td>" . $admin['password'] . "</td>";
        echo "<td>" . ($admin['level'] ?? 'admin') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>❌ Tidak ada admin user ditemukan</p>";
}

echo "<h3>2. Perbaiki/Buat Admin User:</h3>";

// Hapus admin lama jika ada
$sql_delete_old = "DELETE FROM tb_daftar WHERE username = 'admin'";
mysqli_query($koneksi, $sql_delete_old);

// Buat admin baru dengan kredensial yang benar
$sql_create_admin = "INSERT INTO tb_daftar (username, password, level) VALUES ('admin', 'admin123', 'admin')";

if (mysqli_query($koneksi, $sql_create_admin)) {
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h4>✅ Admin User Berhasil Dibuat/Diperbaiki!</h4>";
    echo "<p><strong>Kredensial Login Admin:</strong></p>";
    echo "<div style='background: white; padding: 10px; border-radius: 5px; font-family: monospace;'>";
    echo "Username: <strong>admin</strong><br>";
    echo "Password: <strong>admin123</strong>";
    echo "</div>";
    echo "</div>";
} else {
    echo "<p>❌ Gagal membuat admin user: " . mysqli_error($koneksi) . "</p>";
}

// Verifikasi admin baru
echo "<h3>3. Verifikasi Admin Baru:</h3>";
$sql_verify = "SELECT * FROM tb_daftar WHERE username = 'admin'";
$result_verify = mysqli_query($koneksi, $sql_verify);

if ($result_verify && mysqli_num_rows($result_verify) > 0) {
    $admin = mysqli_fetch_assoc($result_verify);
    echo "<div style='background: #d1ecf1; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h4>✅ Verifikasi Berhasil!</h4>";
    echo "<p>Admin user ditemukan dengan data:</p>";
    echo "<ul>";
    echo "<li>ID: " . $admin['id'] . "</li>";
    echo "<li>Username: <strong>" . $admin['username'] . "</strong></li>";
    echo "<li>Password: <strong>" . $admin['password'] . "</strong></li>";
    echo "<li>Level: " . ($admin['level'] ?? 'admin') . "</li>";
    echo "</ul>";
    echo "</div>";
} else {
    echo "<p>❌ Verifikasi gagal - admin user tidak ditemukan</p>";
}

// Test login
echo "<h3>4. Test Login:</h3>";
$test_username = 'admin';
$test_password = 'admin123';

$sql_test_login = "SELECT * FROM tb_daftar WHERE username = '$test_username' AND password = '$test_password'";
$result_test_login = mysqli_query($koneksi, $sql_test_login);

if ($result_test_login && mysqli_num_rows($result_test_login) > 0) {
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h4>✅ Test Login Berhasil!</h4>";
    echo "<p>Kredensial admin dapat digunakan untuk login.</p>";
    echo "</div>";
} else {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h4>❌ Test Login Gagal!</h4>";
    echo "<p>Ada masalah dengan kredensial admin.</p>";
    echo "</div>";
}

// Cek juga karyawan sample
echo "<h3>5. Cek Sample Karyawan:</h3>";
$sql_check_karyawan = "SELECT * FROM tb_karyawan LIMIT 3";
$result_check_karyawan = mysqli_query($koneksi, $sql_check_karyawan);

if ($result_check_karyawan && mysqli_num_rows($result_check_karyawan) > 0) {
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>ID Karyawan</th><th>Username</th><th>Password</th><th>Nama</th></tr>";
    while ($karyawan = mysqli_fetch_assoc($result_check_karyawan)) {
        echo "<tr>";
        echo "<td>" . $karyawan['id_karyawan'] . "</td>";
        echo "<td><strong>" . $karyawan['username'] . "</strong></td>";
        echo "<td>" . $karyawan['password'] . "</td>";
        echo "<td>" . $karyawan['nama'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>⚠️ Belum ada data karyawan sample</p>";
    
    // Tambah sample karyawan
    $sql_sample = "INSERT INTO tb_karyawan (id_karyawan, username, password, nama, tmp_tgl_lahir, jenkel, agama, alamat, no_tel, jabatan, status) VALUES 
    ('249014764', 'siti_zahra', 'password123', 'SITI ZAHRA', 'Medan, 15 Januari 2001', 'Perempuan', 'Islam', 'Jl. Williem Iskandar No. 123, Medan', '081234567890', 'Mahasiswa S1', 'aktif')";
    
    if (mysqli_query($koneksi, $sql_sample)) {
        echo "<p>✅ Sample karyawan berhasil ditambahkan</p>";
    }
}

echo "<div style='background: #e2e3e5; padding: 20px; border-radius: 10px; margin: 20px 0; text-align: center;'>";
echo "<h3>🎯 Kredensial Login yang Benar:</h3>";
echo "<div style='display: flex; justify-content: space-around; flex-wrap: wrap;'>";

echo "<div style='background: #007bff; color: white; padding: 15px; border-radius: 8px; margin: 10px; min-width: 200px;'>";
echo "<h4>👨‍💼 Admin</h4>";
echo "<p>Username: <strong>admin</strong></p>";
echo "<p>Password: <strong>admin123</strong></p>";
echo "</div>";

echo "<div style='background: #28a745; color: white; padding: 15px; border-radius: 8px; margin: 10px; min-width: 200px;'>";
echo "<h4>👩‍🎓 Karyawan</h4>";
echo "<p>Username: <strong>siti_zahra</strong></p>";
echo "<p>Password: <strong>password123</strong></p>";
echo "</div>";

echo "</div>";
echo "</div>";

echo "<div style='text-align: center; margin: 20px 0;'>";
echo "<a href='login_simple.php' style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 24px; text-decoration: none; border-radius: 8px; display: inline-block; font-weight: 500; margin: 5px;'>🚀 Login Sekarang</a>";
echo "<a href='debug_check.php' style='background: #17a2b8; color: white; padding: 12px 24px; text-decoration: none; border-radius: 8px; display: inline-block; font-weight: 500; margin: 5px;'>🔍 Debug Check</a>";
echo "</div>";
?>