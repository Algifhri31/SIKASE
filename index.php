<?php
session_start();

// Jika sudah login, redirect ke dashboard yang sesuai
if (isset($_SESSION['username']) && isset($_SESSION['level'])) {
    if ($_SESSION['level'] === 'admin') {
        header("location: admin_dashboard_fixed.php");
        exit();
    } else {
        header("location: karyawan/index.php");
        exit();
    }
}

// Jika belum login, redirect ke halaman login
header("location: login_simple.php");
exit();
?>