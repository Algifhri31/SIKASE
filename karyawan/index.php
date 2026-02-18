<?php
session_start();
// Sementara: tampilkan error runtime untuk debugging lokal
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');
error_log('karyawan/index.php loaded, session=' . json_encode($_SESSION));
include_once "sesi_karyawan.php";
$modul = (isset($_GET['m'])) ? $_GET['m'] : "awal";
$submodul = (isset($_GET['s'])) ? $_GET['s'] : ""; 
$jawal = "Login Karyawan || SI Karyawan";

switch($modul) {
    case 'awal': 
    default: 
        $aktif = "Dashboard"; 
        $judul = "Dashboard $jawal"; 
        include "dashboard_final.php"; 
        break;

    case 'presensi':
        $aktif = "Presensi";
        $judul = "Presensi $jawal";
        include "presensi.php";
        break;
        
    case 'karyawan':
        $aktif = "Profil";
        $judul = "Profil $jawal";
        
        switch($submodul) {
            case 'profil':
                include "profil_new.php";
                break;
            case 'edit':
                include "edit.php";
                break;
            case 'riwayat':
                include "riwayat_absensi.php";
                break;
            case 'title':
                include "modul/karyawan/title.php";
                break;
            default:
                include "awal.php";
                break;
        }
        break;
}
?>
