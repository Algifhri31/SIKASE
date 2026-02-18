<?php
/**
 * Dashboard Configuration
 * Konfigurasi untuk dashboard beswan
 */

// Informasi Aplikasi
define('APP_NAME', 'Beswan KSE');
define('APP_VERSION', '2.0');
define('APP_DESCRIPTION', 'Sistem Informasi Kehadiran Beasiswa KSE');

// Menu Configuration
$dashboard_menu = [
    [
        'id' => 'dashboard',
        'title' => 'Dashboard',
        'icon' => 'fas fa-tachometer-alt',
        'url' => '?m=awal',
        'active' => ['awal']
    ],
    [
        'id' => 'presensi',
        'title' => 'Presensi',
        'icon' => 'fas fa-clock',
        'url' => '?m=presensi',
        'active' => ['presensi']
    ],
    [
        'id' => 'profil',
        'title' => 'Profil Saya',
        'icon' => 'fas fa-user',
        'url' => '?m=karyawan&s=profil',
        'active' => ['karyawan']
    ],
    [
        'id' => 'riwayat',
        'title' => 'Riwayat Absen',
        'icon' => 'fas fa-history',
        'url' => '?m=karyawan&s=riwayat',
        'active' => ['karyawan']
    ]
];

// Status Absensi
$status_absensi = [
    'Hadir' => [
        'class' => 'bg-success',
        'icon' => 'fas fa-check-circle'
    ],
    'Sakit' => [
        'class' => 'bg-warning',
        'icon' => 'fas fa-thermometer-half'
    ],
    'Izin' => [
        'class' => 'bg-info',
        'icon' => 'fas fa-file-medical'
    ],
    'Alpha' => [
        'class' => 'bg-danger',
        'icon' => 'fas fa-times-circle'
    ]
];

// Jam Kerja
define('JAM_MASUK', '08:00:00');
define('JAM_PULANG', '17:00:00');
define('BATAS_TERLAMBAT', '08:30:00');

// Timezone
date_default_timezone_set('Asia/Jakarta');

// Helper Functions
function getActiveMenu($current_module, $current_submodule = '') {
    global $dashboard_menu;
    
    foreach ($dashboard_menu as $menu) {
        if (in_array($current_module, $menu['active'])) {
            return $menu['id'];
        }
    }
    return 'dashboard';
}

function getStatusBadge($status) {
    global $status_absensi;
    
    if (isset($status_absensi[$status])) {
        return [
            'class' => $status_absensi[$status]['class'],
            'icon' => $status_absensi[$status]['icon']
        ];
    }
    
    return [
        'class' => 'bg-secondary',
        'icon' => 'fas fa-question-circle'
    ];
}

function formatTanggalIndonesia($tanggal) {
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    $hari = [
        'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat',
        'Saturday' => 'Sabtu'
    ];
    
    $timestamp = strtotime($tanggal);
    $nama_hari = $hari[date('l', $timestamp)];
    $tgl = date('j', $timestamp);
    $nama_bulan = $bulan[date('n', $timestamp)];
    $tahun = date('Y', $timestamp);
    
    return "$nama_hari, $tgl $nama_bulan $tahun";
}

function hitungJamKerja($jam_masuk, $jam_keluar) {
    if (!$jam_masuk || !$jam_keluar) {
        return '0 jam 0 menit';
    }
    
    $masuk = strtotime($jam_masuk);
    $keluar = strtotime($jam_keluar);
    
    if ($keluar < $masuk) {
        return '0 jam 0 menit';
    }
    
    $selisih = $keluar - $masuk;
    $jam = floor($selisih / 3600);
    $menit = floor(($selisih % 3600) / 60);
    
    return "$jam jam $menit menit";
}

function cekStatusKehadiran($jam_masuk) {
    if (!$jam_masuk) {
        return 'Alpha';
    }
    
    $batas_terlambat = strtotime(BATAS_TERLAMBAT);
    $waktu_masuk = strtotime($jam_masuk);
    
    if ($waktu_masuk <= $batas_terlambat) {
        return 'Tepat Waktu';
    } else {
        return 'Terlambat';
    }
}

// CSS dan JS Assets
$dashboard_assets = [
    'css' => [
        'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap',
        'assets/css/dashboard.css'
    ],
    'js' => [
        'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js',
        'https://unpkg.com/sweetalert/dist/sweetalert.min.js',
        'assets/js/dashboard.js'
    ]
];

// Breadcrumb Configuration
function generateBreadcrumb($current_module, $current_submodule = '') {
    $breadcrumb = [
        ['title' => 'Dashboard', 'url' => '?m=awal']
    ];
    
    switch ($current_module) {
        case 'presensi':
            $breadcrumb[] = ['title' => 'Presensi', 'url' => null];
            break;
        case 'karyawan':
            switch ($current_submodule) {
                case 'profil':
                    $breadcrumb[] = ['title' => 'Profil Saya', 'url' => null];
                    break;
                case 'edit':
                    $breadcrumb[] = ['title' => 'Profil Saya', 'url' => '?m=karyawan&s=profil'];
                    $breadcrumb[] = ['title' => 'Edit Profil', 'url' => null];
                    break;
                case 'riwayat':
                    $breadcrumb[] = ['title' => 'Riwayat Absen', 'url' => null];
                    break;
            }
            break;
    }
    
    return $breadcrumb;
}
?>