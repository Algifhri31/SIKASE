<?php
session_start();
require_once("koneksi.php");

// Cek auth
if (!isset($_SESSION['username']) || !isset($_SESSION['level']) || $_SESSION['level'] !== 'admin') {
    header("location: login.php");
    exit();
}

// Get filter parameters
$filter_month = isset($_GET['bulan']) ? $_GET['bulan'] : '';
$filter_year = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';

// Build Query
$where_clause = "WHERE YEAR(tb_keterangan.tanggal) = '$filter_year'";
$filter_label = $filter_year;

if (!empty($filter_month)) {
    $where_clause .= " AND MONTH(tb_keterangan.tanggal) = '$filter_month'";
    $filter_label = date('F', mktime(0, 0, 0, $filter_month, 10)) . " " . $filter_year;
}

if (!empty($search)) {
    $where_clause .= " AND (tb_keterangan.nama LIKE '%$search%' OR tb_keterangan.id_karyawan LIKE '%$search%')";
}

// Fetch Data
$sql = "SELECT tb_keterangan.*, tb_karyawan.nama as nama_asli 
        FROM tb_keterangan 
        LEFT JOIN tb_karyawan ON tb_keterangan.id_karyawan = tb_karyawan.id_karyawan 
        $where_clause 
        ORDER BY tb_keterangan.tanggal DESC";
$query = mysqli_query($koneksi, $sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keterangan - <?php echo $filter_label; ?></title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        * { box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f0f0f0; margin: 0; padding: 20px; display: flex; justify-content: center; }
        #pdf-container { background: white; padding: 30px; width: 100%; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .header { text-align: center; margin-bottom: 25px; }
        .kop-title { font-size: 20px; font-weight: bold; }
        .kop-sub { font-size: 16px; font-weight: bold; margin-top: 5px; }
        .kop-contact { font-size: 10px; color: #4a5568; margin-top: 5px; line-height: 1.4; }
        .line-thick { border-bottom: 2px solid #000; margin-top: 10px; }
        .line-thin { border-bottom: 1px solid #000; margin-top: 2px; margin-bottom: 20px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; table-layout: fixed; border: 1px solid #000; }
        th { background-color: #f2f2f2; padding: 10px 5px; border: 1px solid #000; font-size: 10px; text-transform: uppercase; text-align: center; }
        td { padding: 8px 5px; border: 1px solid #000; font-size: 10px; vertical-align: middle; word-wrap: break-word; }
        
        .badge { padding: 2px 4px; border-radius: 2px; color: #000; font-weight: bold; font-size: 8px; border: 0.5px solid #000; text-transform: uppercase; }
        
        .footer { margin-top: 30px; display: flex; justify-content: space-between; font-size: 10px; color: #333; border-top: 1px dashed #999; padding-top: 10px; }
        
        #loading-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.9); display: flex; flex-direction: column; justify-content: center; align-items: center; z-index: 9999; }
        .spinner { width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #3b82f6; border-radius: 50%; animation: spin 1s linear infinite; margin-bottom: 20px; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        
        /* Widths */
        .col-no { width: 4%; }
        .col-id { width: 15%; }
        .col-nama { width: 30%; }
        .col-tanggal { width: 15%; }
        .col-ket { width: 10%; }
        .col-alasan { width: 26%; }
    </style>
</head>
<body>
    <div id="loading-overlay">
        <div class="spinner"></div>
        <h3>Sedang Membuat PDF...</h3>
    </div>

    <div id="pdf-container">
        <div class="header">
            <div class="kop-title">PAGUYUBAN KARYA SALEMBA EMPAT (KSE)</div>
            <div class="kop-sub">UNIVERSITAS ISLAM NEGERI SUMATERA UTARA</div>
            <div class="kop-contact">Sekretariat: Jl. Letda Sujono Gg. Kapuk, Bandar Selamat, Kec. Medan Tembung, Kota Medan, Sumatera Utara.</div>
            <div class="line-thick"></div>
            <div class="line-thin"></div>
            
            <h3 style="margin-top: 10px;">LAPORAN REKAPITULASI KETERANGAN BESWAN</h3>
            <p>Periode: <strong><?php echo $filter_label; ?></strong></p>
        </div>

        <table>
            <thead>
                <tr>
                    <th class="col-no">No</th>
                    <th class="col-id">ID Beswan</th>
                    <th class="col-nama">Nama Anggota</th>
                    <th class="col-tanggal">Tanggal</th>
                    <th class="col-ket">Keterangan</th>
                    <th class="col-alasan">Alasan</th>
                </tr>
            </thead>
            <tbody>
                <?php
$no = 1;
if (mysqli_num_rows($query) > 0) {
    while ($row = mysqli_fetch_array($query)) {
?>
                <tr>
                    <td style="text-align: center;"><?php echo $no++; ?></td>
                    <td style="text-align: center; font-family: monospace;"><?php echo htmlspecialchars($row['id_karyawan']); ?></td>
                    <td><strong><?php echo htmlspecialchars($row['nama']); ?></strong></td>
                    <td style="text-align: center;"><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                    <td style="text-align: center;">
                        <span class="badge"><?php echo strtoupper($row['keterangan']); ?></span>
                    </td>
                    <td><?php echo htmlspecialchars($row['alasan']); ?></td>
                </tr>
                <?php
    }
}
else { ?>
                <tr><td colspan="6" style="text-align: center; padding: 20px;">Tidak ada data.</td></tr>
                <?php
}?>
            </tbody>
        </table>

        <div class="footer">
            <div>
                <strong>Paguyuban KSE UINSU</strong><br>
                <small>Dicetak pada: <?php echo date('d/m/Y H:i:s'); ?> WIB</small>
            </div>
            <div style="text-align: right;">Halaman 1 dari 1</div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const element = document.getElementById('pdf-container');
            const opt = {
                margin:       [15, 15, 15, 15],
                filename:     'Laporan_Keterangan_<?php echo $filter_label; ?>.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, useCORS: true, letterRendering: true },
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'landscape' }
            };
            html2pdf().set(opt).from(element).save().then(() => {
                setTimeout(() => { document.getElementById('loading-overlay').style.display = 'none'; }, 1000);
            });
        });
    </script>
</body>
</html>
