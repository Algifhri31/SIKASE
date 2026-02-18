<?php 
// Hanya tampilkan error di development
// Pastikan kami tidak mengakses $_SERVER['SERVER_NAME'] langsung saat dijalankan dari CLI
if (php_sapi_name() === 'cli' || (isset($_SERVER['SERVER_NAME']) && ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === '127.0.0.1'))) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    error_reporting(0);
}

// Koneksi database
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "karyawansi";

// Disable mysqli exceptions so we can handle errors gracefully here
if (function_exists('mysqli_report')) {
    mysqli_report(MYSQLI_REPORT_OFF);
}

// Use mysqli_init + mysqli_real_connect to avoid exceptions being thrown from mysqli_connect
$mysqli = mysqli_init();
$connected = @mysqli_real_connect($mysqli, $db_host, $db_user, $db_pass, $db_name);

// If initial connect failed, and we're on localhost, try development fallback (root/no-password)
$is_local = isset($_SERVER['SERVER_NAME']) && ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === '127.0.0.1');
if (!$connected && $is_local) {
    $connected = @mysqli_real_connect($mysqli, 'localhost', 'root', '', $db_name);
    if ($connected && ini_get('display_errors')) {
        error_log("[koneksi.php] menggunakan fallback koneksi root tanpa password untuk development.");
    }
}

// Final check
if (!$connected) {
    http_response_code(500);
    echo "<h2>Koneksi database gagal</h2>";
    echo "<p>Database tidak dapat dihubungi dengan kredensial saat ini.</p>";
    echo "<p>Periksa file <code>koneksi.php</code> dan kredensial database Anda, atau jalankan perintah SQL berikut di MySQL untuk membuat / memberi hak pada user aplikasi:</p>";
    echo "<pre>CREATE USER 'u524719089_Absenkse'@'localhost' IDENTIFIED BY 'Absenkse123';\nGRANT ALL PRIVILEGES ON `u524719089_karyawansi`.* TO 'u524719089_Absenkse'@'localhost';\nFLUSH PRIVILEGES;</pre>";
    echo "<p>Atau, untuk development lokal (Laragon), Anda bisa menggunakan user <code>root</code> tanpa password sementara.</p>";
    exit;
}

// Assign the established connection to the global variable used across the app
$koneksi = $mysqli;

// Set karakter encoding
mysqli_set_charset($koneksi, "utf8mb4");

// Fungsi untuk mencegah SQL injection
function escape($string) {
    global $koneksi;
    return mysqli_real_escape_string($koneksi, $string);
}
?>