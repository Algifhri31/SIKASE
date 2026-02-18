<?php
/**
 * Fungsi pembantu untuk memformat tanggal dan waktu dalam Bahasa Indonesia
 */

function getNamaHari($namaHariInggris) {
    $hari = array(
        'Sunday' => 'Minggu',
        'Monday' => 'Senin', 
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu'
    );
    return $hari[$namaHariInggris];
}

function getNamaBulan($namaBulanInggris) {
    $bulan = array(
        'January' => 'Januari',
        'February' => 'Februari',
        'March' => 'Maret',
        'April' => 'April',
        'May' => 'Mei',
        'June' => 'Juni',
        'July' => 'Juli',
        'August' => 'Agustus',
        'September' => 'September',
        'October' => 'Oktober',
        'November' => 'November',
        'December' => 'Desember'
    );
    return $bulan[$namaBulanInggris];
}

function getTanggalIndonesia($timestamp = null) {
    if ($timestamp === null) {
        $timestamp = time();
    }
    $namaHari = date('l', $timestamp);
    $namaBulan = date('F', $timestamp);
    
    return getNamaHari($namaHari) . ', ' . date('d', $timestamp) . ' ' . 
           getNamaBulan($namaBulan) . ' ' . date('Y', $timestamp);
}

function getWaktuIndonesia($timestamp = null) {
    if ($timestamp === null) {
        $timestamp = time();
    }
    return date('H:i:s', $timestamp);
}

function getTanggalWaktuIndonesia($timestamp = null) {
    if ($timestamp === null) {
        $timestamp = time();
    }
    return getTanggalIndonesia($timestamp) . ' ' . getWaktuIndonesia($timestamp);
}
?>