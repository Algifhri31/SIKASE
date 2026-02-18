<?php
session_start();

// Cek apakah user sudah login sebagai admin
if (!isset($_SESSION['username']) || !isset($_SESSION['level']) || $_SESSION['level'] !== 'admin') {
    header("location: ../login.php");
    exit();
}

// Redirect ke halaman modern
header("location: data_absensi_modern.php");
exit();
?>