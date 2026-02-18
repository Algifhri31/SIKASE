<?php 
// Aktifkan error agar halaman kosong mudah dilacak
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');
error_log('sesi_karyawan start, session=' . json_encode($_SESSION));

// Include koneksi database - selalu diperlukan
$project_koneksi = __DIR__ . '/../koneksi.php';
if (file_exists($project_koneksi)) {
    require_once $project_koneksi;
} else {
    // fallback to parent koneksi if present (legacy)
    if (file_exists(__DIR__ . '/modul/karyawan/koneksi.php')) {
        require_once __DIR__ . '/modul/karyawan/koneksi.php';
    } else {
        // final: try parent folder
        require_once('../koneksi.php');
    }
}

if(!isset($_SESSION['idsi']) || empty($_SESSION['idsi'])) {
    error_log('sesi_karyawan: idsi tidak ada, redirect login');
    session_destroy();
    header('location:../login.php');
    exit();
}

// Cek level jika ada
if(isset($_SESSION['level']) && $_SESSION['level'] !== 'beswan') {
    error_log('sesi_karyawan: level bukan beswan -> ' . $_SESSION['level']);
    session_destroy();
    header('location:../login.php');
    exit();
}

if(!isset($_SESSION['namasi']) || empty($_SESSION['namasi'])) {
    if (!isset($koneksi) || !$koneksi) {
        error_log('sesi_karyawan: $koneksi tidak tersedia');
        die("Database connection failed");
    }
    
    $id = mysqli_real_escape_string($koneksi, $_SESSION['idsi']);
    $sql = "SELECT nama FROM tb_karyawan WHERE id_karyawan = '$id' LIMIT 1";
    $query = mysqli_query($koneksi, $sql);
    
    if($query && mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        $_SESSION['namasi'] = $data['nama'];
        error_log('sesi_karyawan: nama ditemukan untuk id ' . $id);
    } else {
        // User tidak ditemukan di database
        error_log('sesi_karyawan: id tidak ditemukan di tb_karyawan -> ' . $id);
        session_destroy();
        header('location:../login.php');
        exit();
    }
}
?>
