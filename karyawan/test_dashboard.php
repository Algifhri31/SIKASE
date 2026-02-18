<?php
/**
 * File Test untuk Dashboard Beswan
 * Gunakan file ini untuk mengecek apakah semua komponen berfungsi
 */

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Test Dashboard Beswan KSE</h1>";
echo "<hr>";

// Test 1: Koneksi Database
echo "<h2>1. Test Koneksi Database</h2>";
include '../koneksi.php';
if (isset($koneksi) && $koneksi instanceof mysqli) {
    echo "✅ Koneksi database berhasil<br>";
    echo "Database: " . mysqli_get_host_info($koneksi) . "<br>";
} else {
    echo "❌ Koneksi database gagal<br>";
}
echo "<hr>";

// Test 2: Session
echo "<h2>2. Test Session</h2>";
if (isset($_SESSION['idsi']) && isset($_SESSION['namasi'])) {
    echo "✅ Session aktif<br>";
    echo "ID: " . htmlspecialchars($_SESSION['idsi']) . "<br>";
    echo "Nama: " . htmlspecialchars($_SESSION['namasi']) . "<br>";
} else {
    echo "⚠️ Session tidak aktif (belum login)<br>";
    echo "<a href='../login.php'>Login disini</a><br>";
}
echo "<hr>";

// Test 3: Tabel Database
echo "<h2>3. Test Tabel Database</h2>";
if (isset($koneksi)) {
    // Cek tabel tb_karyawan
    $result = mysqli_query($koneksi, "SHOW TABLES LIKE 'tb_karyawan'");
    if (mysqli_num_rows($result) > 0) {
        echo "✅ Tabel tb_karyawan ada<br>";
        $count = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_karyawan"))['total'];
        echo "Total karyawan: " . $count . "<br>";
    } else {
        echo "❌ Tabel tb_karyawan tidak ada<br>";
    }
    
    // Cek tabel tb_absen
    $result = mysqli_query($koneksi, "SHOW TABLES LIKE 'tb_absen'");
    if (mysqli_num_rows($result) > 0) {
        echo "✅ Tabel tb_absen ada<br>";
        $count = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_absen"))['total'];
        echo "Total absen: " . $count . "<br>";
    } else {
        echo "❌ Tabel tb_absen tidak ada<br>";
    }
}
echo "<hr>";

// Test 4: File-file Penting
echo "<h2>4. Test File-file Penting</h2>";
$files = [
    'index.php' => 'File routing utama',
    'dashboard_final.php' => 'Dashboard modern',
    'presensi.php' => 'Form presensi',
    'sesi_karyawan.php' => 'Validasi session',
    'header.php' => 'Template header',
    'helper_tanggal_new.php' => 'Helper tanggal'
];

foreach ($files as $file => $desc) {
    if (file_exists($file)) {
        echo "✅ $file - $desc<br>";
    } else {
        echo "❌ $file tidak ditemukan - $desc<br>";
    }
}
echo "<hr>";

// Test 5: Helper Functions
echo "<h2>5. Test Helper Functions</h2>";
if (file_exists('helper_tanggal_new.php')) {
    include 'helper_tanggal_new.php';
    echo "✅ Helper tanggal loaded<br>";
    echo "Tanggal Indonesia: " . getTanggalIndonesia() . "<br>";
    echo "Waktu Indonesia: " . getWaktuIndonesia() . "<br>";
} else {
    echo "❌ Helper tanggal tidak ditemukan<br>";
}
echo "<hr>";

// Test 6: Data Absen (jika sudah login)
if (isset($_SESSION['idsi']) && isset($koneksi)) {
    echo "<h2>6. Test Data Absen</h2>";
    $id = mysqli_real_escape_string($koneksi, $_SESSION['idsi']);
    $bulan_ini = date('Y-m');
    
    $sql = "SELECT COUNT(*) as total FROM tb_absen WHERE id_karyawan = '$id' AND DATE_FORMAT(waktu, '%Y-%m') = '$bulan_ini'";
    $result = mysqli_query($koneksi, $sql);
    if ($result) {
        $total = mysqli_fetch_assoc($result)['total'];
        echo "✅ Query absen berhasil<br>";
        echo "Total absen bulan ini: " . $total . "<br>";
    } else {
        echo "❌ Query absen gagal: " . mysqli_error($koneksi) . "<br>";
    }
    echo "<hr>";
}

// Test 7: Browser Compatibility
echo "<h2>7. Browser Info</h2>";
echo "User Agent: " . $_SERVER['HTTP_USER_AGENT'] . "<br>";
echo "Server: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
echo "PHP Version: " . phpversion() . "<br>";
echo "<hr>";

// Kesimpulan
echo "<h2>Kesimpulan</h2>";
echo "<p>Jika semua test menunjukkan ✅, maka dashboard siap digunakan!</p>";
echo "<p><a href='index.php' style='padding: 10px 20px; background: #2563eb; color: white; text-decoration: none; border-radius: 5px;'>Buka Dashboard</a></p>";
?>

<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        max-width: 800px;
        margin: 50px auto;
        padding: 20px;
        background: #f5f7fb;
    }
    h1 {
        color: #2b3144;
        border-bottom: 3px solid #2563eb;
        padding-bottom: 10px;
    }
    h2 {
        color: #4f5d77;
        margin-top: 20px;
    }
    hr {
        border: none;
        border-top: 1px solid #e5e7eb;
        margin: 20px 0;
    }
</style>
