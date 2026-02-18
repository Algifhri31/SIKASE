<?php 
session_start();
require_once("koneksi.php");

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_log("Starting login process...");

// Ambil data dari form
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

error_log("Raw input - Username: " . $username . ", Password length: " . strlen($password));

// Validasi input
if (empty($username) || empty($password)) {
    error_log("Empty username or password");
    $_SESSION['login_error'] = "Username dan password harus diisi!";
    header('location:login.php');
    exit();
}

// Cek koneksi database
if (!$koneksi) {
    error_log("Database connection failed!");
    $_SESSION['login_error'] = "Koneksi database gagal!";
    header('location:login.php');
    exit();
}

// Log database connection info
error_log("Database connection successful. Charset: " . mysqli_character_set_name($koneksi));

// Escape string untuk keamanan
$username = mysqli_real_escape_string($koneksi, $username);
$password = mysqli_real_escape_string($koneksi, $password);

error_log("Escaped values - Username: " . $username . ", Password length: " . strlen($password));

// Cek di tabel admin (tb_daftar) terlebih dahulu
$sql_admin = "SELECT * FROM tb_daftar WHERE username = '$username'";
error_log("Admin query: " . $sql_admin);

$result_admin = mysqli_query($koneksi, $sql_admin);

if (!$result_admin) {
    error_log("Query error: " . mysqli_error($koneksi));
    $_SESSION['login_error'] = "Database error!";
    header('location:login.php');
    exit();
}

error_log("Found " . mysqli_num_rows($result_admin) . " matching admin accounts");

if ($result_admin && mysqli_num_rows($result_admin) > 0) {
    $admin_data = mysqli_fetch_assoc($result_admin);
    
    error_log("Admin account details:");
    error_log("ID: " . $admin_data['id']);
    error_log("Username: " . $admin_data['username']);
    error_log("Stored password: " . $admin_data['password']);
    error_log("Input password: " . $password);
    error_log("Password match check: " . ($password === $admin_data['password'] ? 'YES' : 'NO'));
    
    // Login sebagai admin - password plain text
    if ($password === $admin_data['password']) {
        error_log("Password matched! Setting up session...");
        
        $_SESSION['username'] = $admin_data['username'];
        $_SESSION['level'] = 'admin';
        // Ambil role dari database, default ke 'admin' jika kosong
        $_SESSION['role'] = !empty($admin_data['role']) ? $admin_data['role'] : 'admin';
        $_SESSION['admin_id'] = $admin_data['id'];
        $_SESSION['logged_in'] = true;
        
        error_log("Session variables set. Username: " . $_SESSION['username'] . ", Role: " . $_SESSION['role']);
        
        // Set success message
        $_SESSION['success_msg'] = "Login berhasil! Selamat datang " . $admin_data['username'];
        
        error_log("Redirecting admin " . $admin_data['username']);
        
        // Redirect ke dashboard admin fixed
        header('location:admin_dashboard_fixed.php');
        exit();
    } else {
        error_log("Password mismatch!");
        error_log("Expected: '" . $admin_data['password'] . "'");
        error_log("Received: '" . $password . "'");
        $_SESSION['login_error'] = "Password salah!";
        header('location:login.php');
        exit();
    }
}

// Jika bukan admin, cek di tabel karyawan/beswan (tb_karyawan)
$sql_karyawan = "SELECT * FROM tb_karyawan WHERE username = '$username'";
$result_karyawan = mysqli_query($koneksi, $sql_karyawan);

if ($result_karyawan && mysqli_num_rows($result_karyawan) > 0) {
    $karyawan_data = mysqli_fetch_assoc($result_karyawan);
    $md5_password = md5($password);
    
    // Login sebagai beswan - password MD5
    if ($md5_password == $karyawan_data['password']) {
        $_SESSION['username'] = $karyawan_data['username'];
        $_SESSION['id_karyawan'] = $karyawan_data['id_karyawan'];
        $_SESSION['level'] = 'beswan';
        $_SESSION['idsi'] = $karyawan_data['id_karyawan'];
        $_SESSION['namasi'] = $karyawan_data['nama'];
        
        // Redirect ke dashboard beswan
        header('location:karyawan/index.php');
        exit();
    }
}

// Jika sampai sini, login gagal
$_SESSION['login_error'] = "Username atau password salah!";
header('location:login.php');
exit();
?>