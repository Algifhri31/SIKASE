<?php 
session_start();
require_once("koneksi.php");

// Ambil data dari form
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

// Debug info
echo "<h3>🔍 Debug Login Process</h3>";
echo "Username: '$username'<br>";
echo "Password: '$password'<br><br>";

// Validasi input
if (empty($username) || empty($password)) {
    echo "❌ Username atau password kosong!<br>";
    echo "<a href='login.php'>Kembali ke login</a>";
    exit();
}

// Cek koneksi database
if (!$koneksi) {
    echo "❌ Koneksi database gagal: " . mysqli_connect_error() . "<br>";
    exit();
}
echo "✅ Koneksi database berhasil<br><br>";

// Escape string untuk keamanan
$username = mysqli_real_escape_string($koneksi, $username);
$password = mysqli_real_escape_string($koneksi, $password);

// Cek di tabel admin (tb_daftar) dengan query langsung
echo "<h4>1. Cek Admin di tb_daftar:</h4>";
$sql_admin = "SELECT * FROM tb_daftar WHERE username = '$username'";
echo "Query: $sql_admin<br>";

$result_admin = mysqli_query($koneksi, $sql_admin);

if (!$result_admin) {
    echo "❌ Error query admin: " . mysqli_error($koneksi) . "<br><br>";
} else {
    $admin_rows = mysqli_num_rows($result_admin);
    echo "Rows found: $admin_rows<br>";
    
    if ($admin_rows > 0) {
        $admin_data = mysqli_fetch_assoc($result_admin);
        echo "✅ Admin ditemukan!<br>";
        echo "DB Username: '" . $admin_data['username'] . "'<br>";
        echo "DB Password: '" . $admin_data['password'] . "'<br>";
        echo "Input Password: '$password'<br>";
        
        // Login sebagai admin - password plain text
        if ($password == $admin_data['password']) {
            echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
            echo "✅ <strong>ADMIN LOGIN BERHASIL!</strong><br>";
            echo "Setting session dan redirect ke admin2.php...";
            echo "</div>";
            
            $_SESSION['username'] = $admin_data['username'];
            $_SESSION['level'] = 'admin';
            $_SESSION['admin_id'] = $admin_data['id'];
            
            echo "<script>";
            echo "setTimeout(function() { window.location.href = 'admin2.php'; }, 2000);";
            echo "</script>";
            echo "<a href='admin2.php'>Klik di sini jika tidak redirect otomatis</a>";
            exit();
        } else {
            echo "❌ Password tidak cocok!<br>";
        }
    } else {
        echo "❌ Admin tidak ditemukan<br>";
    }
}

echo "<br><h4>2. Cek Beswan di tb_karyawan:</h4>";
$sql_karyawan = "SELECT * FROM tb_karyawan WHERE username = '$username'";
echo "Query: $sql_karyawan<br>";

$result_karyawan = mysqli_query($koneksi, $sql_karyawan);

if (!$result_karyawan) {
    echo "❌ Error query karyawan: " . mysqli_error($koneksi) . "<br><br>";
} else {
    $karyawan_rows = mysqli_num_rows($result_karyawan);
    echo "Rows found: $karyawan_rows<br>";
    
    if ($karyawan_rows > 0) {
        $karyawan_data = mysqli_fetch_assoc($result_karyawan);
        $md5_password = md5($password);
        
        echo "✅ Karyawan ditemukan!<br>";
        echo "DB Username: '" . $karyawan_data['username'] . "'<br>";
        echo "DB Password (MD5): '" . $karyawan_data['password'] . "'<br>";
        echo "Input Password: '$password'<br>";
        echo "MD5 Input: '$md5_password'<br>";
        
        // Login sebagai beswan - password MD5
        if ($md5_password == $karyawan_data['password']) {
            echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
            echo "✅ <strong>BESWAN LOGIN BERHASIL!</strong><br>";
            echo "Setting session dan redirect ke karyawan/index.php...";
            echo "</div>";
            
            $_SESSION['username'] = $karyawan_data['username'];
            $_SESSION['id_karyawan'] = $karyawan_data['id_karyawan'];
            $_SESSION['level'] = 'beswan';
            $_SESSION['idsi'] = $karyawan_data['id_karyawan'];
            $_SESSION['namasi'] = $karyawan_data['nama'];
            
            echo "<script>";
            echo "setTimeout(function() { window.location.href = 'karyawan/index.php'; }, 2000);";
            echo "</script>";
            echo "<a href='karyawan/index.php'>Klik di sini jika tidak redirect otomatis</a>";
            exit();
        } else {
            echo "❌ Password tidak cocok!<br>";
        }
    } else {
        echo "❌ Karyawan tidak ditemukan<br>";
    }
}

echo "<br><div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px;'>";
echo "❌ <strong>LOGIN GAGAL!</strong><br>";
echo "Username atau password salah!";
echo "</div>";

echo "<br><a href='login.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Kembali ke Login</a>";

mysqli_close($koneksi);
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 20px auto;
    padding: 20px;
    background: #f8f9fa;
}

h3, h4 {
    color: #343a40;
}
</style>