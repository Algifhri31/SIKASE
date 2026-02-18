<?php
session_start();
require_once("koneksi.php");

// Cek auth
if (!isset($_SESSION['username']) || !isset($_SESSION['level']) || $_SESSION['level'] !== 'admin') {
    header("location: login.php");
    exit();
}

// Get filter parameters
$filter_month = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$filter_year = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
$filter_date = isset($_GET['tanggal']) ? $_GET['tanggal'] : '';

// Build Where Clauses
$where_absen = "";
$where_ket = "";
if (!empty($filter_date)) {
    $where_absen = "WHERE DATE(a.waktu) = '$filter_date'";
    $where_ket = "WHERE DATE(k.waktu) = '$filter_date'";
    $filter_label = date('d F Y', strtotime($filter_date));
    $filename_suffix = "Harian_" . $filter_date;
}
else {
    $where_absen = "WHERE MONTH(a.waktu) = '$filter_month' AND YEAR(a.waktu) = '$filter_year'";
    $where_ket = "WHERE MONTH(k.waktu) = '$filter_month' AND YEAR(k.waktu) = '$filter_year'";
    $filter_label = date('F Y', strtotime("$filter_year-$filter_month-01"));
    $filename_suffix = "Bulanan_" . $filter_year . "_" . $filter_month;
}

// Fetch Combined Data using UNION
$sql = "(SELECT a.id_karyawan as no_kse, a.nama as nama_lengkap, a.waktu, a.tipe, a.kegiatan, a.keterangan, a.lokasi 
         FROM tb_absen a 
         $where_absen)
        UNION ALL
        (SELECT k.id_karyawan as no_kse, COALESCE(kar.nama, k.nama) as nama_lengkap, k.waktu, k.keterangan as tipe, '-' as kegiatan, k.alasan as keterangan, '-' as lokasi 
         FROM tb_keterangan k 
         LEFT JOIN tb_karyawan kar ON k.id_karyawan = kar.id_karyawan
         $where_ket)
        ORDER BY waktu DESC, nama_lengkap ASC";
$query = mysqli_query($koneksi, $sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Absensi - <?php echo $filter_label; ?></title>
    <!-- Include html2pdf library directly from CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f0f0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
        }
        
        #pdf-container {
            background: white;
            padding: 40px;
            width: 297mm; /* A4 Landscape Width */
            min-height: 210mm; /* A4 Landscape Height */
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #667eea;
            padding-bottom: 15px;
        }
        
        .header h1 {
            font-size: 24px;
            color: #2d3748;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        
        .header h2 {
            font-size: 18px;
            color: #667eea;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        
        .info {
            text-align: center;
            margin-bottom: 20px;
            font-size: 12px;
            color: #718096;
        }
        
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f0f0f0;
            margin: 0;
            padding: 0;
        }
        
        #pdf-container {
            background: white;
            padding: 20px;
            width: 100%; /* Use 100% and let html2pdf handle margins */
            margin: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .info {
            margin-bottom: 15px;
            font-size: 10px;
            color: #333;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 1px solid #000;
            table-layout: fixed;
        }
        
        table thead {
            background-color: #f2f2f2;
            color: #000;
        }
        
        table th {
            padding: 10px 5px;
            text-align: center;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            border: 1px solid #000;
        }
        
        table td {
            padding: 8px 5px;
            border: 1px solid #000;
            font-size: 10px;
            color: #000;
            vertical-align: middle;
            word-wrap: break-word; /* Ensure text wraps correctly */
        }
        
        table tbody tr:nth-child(even) {
            background-color: #fafafa;
        }

        .badge {
            padding: 2px 4px;
            border-radius: 2px;
            font-size: 8px;
            font-weight: bold;
            text-align: center;
            display: inline-block;
            border: 0.5px solid #000;
            color: #000 !important;
            background: transparent !important;
            text-transform: uppercase;
        }
        
        /* Column Widths (Proportional) */
        .col-no { width: 4%; text-align: center; }
        .col-kse { width: 15%; text-align: center; font-family: monospace; }
        .col-nama { width: 30%; }
        .col-waktu { width: 14%; text-align: center; }
        .col-tipe { width: 8%; text-align: center; }
        .col-kegiatan { width: 15%; }
        .col-ket { width: 15%; }
        
        .footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 1px dashed #999;
            color: #444;
            display: flex;
            justify-content: space-between;
        }

        #loading-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(255,255,255,0.9);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        
        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>

    <!-- Loading Overlay -->
    <div id="loading-overlay">
        <div class="spinner"></div>
        <h3>Sedang Membuat PDF...</h3>
        <p>Mohon tunggu sebentar, file akan terdownload otomatis.</p>
    </div>

    <!-- PDF Content Area -->
    <div id="pdf-container">
        <div class="header">
            <div style="font-size: 20px; font-weight: bold; margin-bottom: 2px;">PAGUYUBAN KARYA SALEMBA EMPAT (KSE)</div>
            <div style="font-size: 16px; font-weight: bold; margin-bottom: 5px;">UNIVERSITAS ISLAM NEGERI SUMATERA UTARA</div>
            <div style="font-size: 10px; color: #4a5568;">Sekretariat: Jl. Letda Sujono Gg. Kapuk, Bandar Selamat, Kec. Medan Tembung, Kota Medan, Sumatera Utara.</div>
            <div style="border-bottom: 2px solid #000; margin-top: 10px; height: 1px;"></div>
            <div style="border-bottom: 1px solid #000; margin-top: 2px; margin-bottom: 20px;"></div>
            
            <h3 style="text-transform: uppercase; margin-bottom: 5px; color: #2d3748;">LAPORAN REKAPITULASI KEHADIRAN BESWAN</h3>
            <div style="background: #eef2ff; display: inline-block; padding: 5px 20px; border-radius: 20px; font-size: 12px; color: #4f46e5; border: 1px solid #c7d2fe;">
                Periode: <strong><?php echo $filter_label; ?></strong>
            </div>
        </div>
        
        <div class="info" style="margin-top: -10px;">
            <table style="width: 100%; border: none; margin-bottom: 10px;">
                <tr style="border: none; background: transparent;">
                    <td style="border: none; width: 33%; text-align: left;">Total Kehadiran: <strong><?php echo mysqli_num_rows($query); ?></strong></td>
                    <td style="border: none; width: 33%; text-align: center;">Format: <strong>PDF Document</strong></td>
                    <td style="border: none; width: 33%; text-align: right;">Status: <strong>RESMI</strong></td>
                </tr>
            </table>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th class="col-no">No</th>
                    <th class="col-kse">No. KSE</th>
                    <th class="col-nama">Nama Anggota</th>
                    <th class="col-waktu">Waktu</th>
                    <th class="col-tipe">Tipe</th>
                    <th class="col-kegiatan">Kegiatan</th>
                    <th class="col-ket">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php
if (mysqli_num_rows($query) > 0) {
    $no = 1;
    mysqli_data_seek($query, 0); // Reset pointer
    while ($row = mysqli_fetch_array($query)) {
        $tipe = strtolower($row['tipe']);
        $tipe_class = 'badge-masuk';
        if ($tipe == 'pulang')
            $tipe_class = 'badge-pulang';
        if (in_array($tipe, ['sakit', 'izin', 'cuti']))
            $tipe_class = 'badge-izin';
?>
                <tr>
                    <td class="col-no"><?php echo $no++; ?></td>
                    <td class="col-kse" style="font-family: monospace; font-size: 12px;"><?php echo htmlspecialchars($row['no_kse']); ?></td>
                    <td class="col-nama"><strong><?php echo htmlspecialchars($row['nama_lengkap']); ?></strong></td>
                    <td class="col-waktu"><?php echo date('d/m/Y H:i', strtotime($row['waktu'])); ?></td>
                    <td class="col-tipe">
                        <span class="badge <?php echo $tipe_class; ?>">
                            <?php echo strtoupper($row['tipe']); ?>
                        </span>
                    </td>
                    <td class="col-kegiatan"><?php echo htmlspecialchars($row['kegiatan'] ?: '-'); ?></td>
                    <td class="col-ket"><?php echo htmlspecialchars($row['keterangan'] ?: '-'); ?></td>
                </tr>
                <?php
    }
}
else {
?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 30px; color: #718096;">
                        Tidak ada data absensi pada periode ini.
                    </td>
                </tr>
                <?php
}?>
            </tbody>
        </table>
        
        <div class="footer">
            <div style="font-size: 10px;">
                <strong>Paguyuban KSE UINSU</strong><br>
                UIN Sumatera Utara - Medan<br>
                <small>Dicetak pada: <?php echo date('d/m/Y H:i:s'); ?> WIB</small>
            </div>
            <div style="text-align: right; font-size: 10px;">
                <p>Arsip Digital Sistem Informasi Kehadiran<br>Halaman 1 dari 1</p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Options for html2pdf
            const element = document.getElementById('pdf-container');
            const opt = {
                margin:       [15, 15, 15, 15], // top, left, bottom, right
                filename:     'Laporan_Absensi_<?php echo $filename_suffix; ?>.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, useCORS: true, letterRendering: true },
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'landscape' }
            };

            // Generate PDF and verify download
            html2pdf().set(opt).from(element).save().then(function() {
                // Remove loading overlay after a short delay
                setTimeout(function() {
                    document.getElementById('loading-overlay').style.display = 'none';
                }, 1000);
            }).catch(function(err) {
                console.error(err);
                alert('Gagal mendownload PDF. Silakan coba lagi.');
            });
        });
    </script>
</body>
</html>
