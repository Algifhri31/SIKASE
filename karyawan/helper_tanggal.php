<?php
// Fungsi untuk konversi tanggal ke Bahasa Indonesia
function getIndonesianDay($englishDay) {
    $hari = array(
        'Sunday' => 'Minggu',
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu'
    );
    return isset($hari[$englishDay]) ? $hari[$englishDay] : $englishDay;
}

function getIndonesianMonth($englishMonth) {
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
    return isset($bulan[$englishMonth]) ? $bulan[$englishMonth] : $englishMonth;
}

function getTanggalIndonesia($includeTime = false) {
    $englishDay = date('l');
    $englishMonth = date('F');
    $tanggal = getIndonesianDay($englishDay) . ', ' . date('d') . ' ' . getIndonesianMonth($englishMonth) . ' ' . date('Y');
    if ($includeTime) {
        $tanggal .= ' ' . date('H:i:s');
    }
    return $tanggal;
}
?>