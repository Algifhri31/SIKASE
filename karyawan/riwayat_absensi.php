<?php
// Session sudah di-start di index.php
$id = $_SESSION['idsi'];
$bulan_filter = isset($_GET['bulan']) ? $_GET['bulan'] : date('Y-m');
$tanggal_filter = isset($_GET['tanggal']) ? $_GET['tanggal'] : '';

// 1. Helper function for parsing different time formats
function parseWaktuRiwayat($waktu)
{
    $timestamp = strtotime($waktu);
    if ($timestamp)
        return $timestamp;
    $bulan_map = [
        'Januari' => 'January', 'Februari' => 'February', 'Maret' => 'March',
        'April' => 'April', 'Mei' => 'May', 'Juni' => 'June',
        'Juli' => 'July', 'Agustus' => 'August', 'September' => 'September',
        'Oktober' => 'October', 'November' => 'November', 'Desember' => 'December'
    ];
    $waktu_normalized = preg_replace('/^[^0-9]+,\s*/u', '', $waktu);
    foreach ($bulan_map as $id_name => $en) {
        $waktu_normalized = str_replace($id_name, $en, $waktu_normalized);
    }
    return strtotime($waktu_normalized) ?: 0;
}

// 2. Fetch data (simplified for shared use)
function fetchHistoryRecords($koneksi, $id, $bulan_filter, $tanggal_filter)
{
    $records = [];
    $where_absen = ["id_karyawan = '$id'"];
    if (!empty($tanggal_filter))
        $where_absen[] = "DATE(waktu) = '$tanggal_filter'";
    elseif (!empty($bulan_filter))
        $where_absen[] = "DATE_FORMAT(waktu, '%Y-%m') = '$bulan_filter'";

    $res_ab = mysqli_query($koneksi, "SELECT * FROM tb_absen WHERE " . implode(" AND ", $where_absen) . " ORDER BY waktu DESC");
    if ($res_ab) {
        while ($row = mysqli_fetch_assoc($res_ab)) {
            $records[] = ['tipe' => $row['tipe'], 'waktu' => $row['waktu'], 'timestamp' => parseWaktuRiwayat($row['waktu']), 'keterangan' => $row['kegiatan'] ?: $row['keterangan'], 'lokasi' => $row['lokasi'], 'status' => 'hadir'];
        }
    }

    $res_ket = mysqli_query($koneksi, "SELECT * FROM tb_keterangan WHERE id_karyawan = '$id'");
    if ($res_ket) {
        while ($row = mysqli_fetch_assoc($res_ket)) {
            $ts = parseWaktuRiwayat($row['waktu']);
            $include = false;
            if (!empty($tanggal_filter))
                $include = ($ts && date('Y-m-d', $ts) === $tanggal_filter);
            elseif (!empty($bulan_filter))
                $include = ($ts && date('Y-m', $ts) === $bulan_filter);
            else
                $include = true;
            if ($include) {
                $records[] = ['tipe' => $row['keterangan'], 'waktu' => $row['waktu'], 'timestamp' => $ts, 'keterangan' => $row['alasan'], 'lokasi' => '-', 'status' => 'keterangan'];
            }
        }
    }
    usort($records, function ($a, $b) {
        return $b['timestamp'] <=> $a['timestamp'];
    });
    return $records;
}

$records = fetchHistoryRecords($koneksi, $id, $bulan_filter, $tanggal_filter);

// 3. Handle Excel Export (Must be before any output)
if (isset($_GET['export']) && $_GET['export'] == 'excel') {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Riwayat_Absensi_' . $_SESSION['namasi'] . '_' . date('Y-m-d') . '.xls"');
    echo "<table border='1'><tr><th>No</th><th>Waktu</th><th>Tipe</th><th>Keterangan</th><th>Lokasi</th></tr>";
    $no = 1;
    foreach ($records as $row) {
        echo "<tr><td>" . $no++ . "</td><td>" . date('d-m-Y H:i', $row['timestamp']) . "</td><td>" . strtoupper($row['tipe']) . "</td><td>" . $row['keterangan'] . "</td><td>" . $row['lokasi'] . "</td></tr>";
    }
    echo "</table>";
    exit();
}

$page_title = "Riwayat Kehadiran";
include "header.php";
?>

<?php
// Calculate stats
$total_masuk = 0;
$total_pulang = 0;
$total_izin = 0;
foreach ($records as $r) {
    $tp = strtolower($r['tipe']);
    if ($tp == 'masuk')
        $total_masuk++;
    elseif ($tp == 'pulang')
        $total_pulang++;
    elseif (in_array($tp, ['sakit', 'izin', 'cuti']))
        $total_izin++;
}
?>


<style>
    .page-header {
        background: #fff;
        padding: 25px;
        border-radius: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        margin-bottom: 30px;
    }

    .filter-card {
        background: #fff;
        padding: 20px;
        border-radius: 20px;
        border: 1px solid #f1f5f9;
        margin-bottom: 30px;
    }

    .record-card {
        background: #fff;
        border-radius: 15px;
        border: 1px solid #f1f5f9;
        padding: 15px;
        margin-bottom: 15px;
        transition: all 0.3s;
    }

    .record-card:hover {
        transform: translateX(5px);
        border-color: #e2e8f0;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }

    .type-indicator {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 10px;
    }

    .bg-masuk { background-color: #10b981; }
    .bg-pulang { background-color: #3b82f6; }
    .bg-izin { background-color: #f59e0b; }
    .bg-sakit { background-color: #ef4444; }

    .stat-badge {
        padding: 5px 15px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.8rem;
    }
</style>

<div class="container-fluid" id="exportContent">
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h4 class="fw-bold mb-1">Riwayat Kehadiran</h4>
            <p class="text-muted small mb-0">Memantau data kehadiran dan izin anda</p>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0 d-print-none">
            <button type="button" class="btn btn-outline-primary rounded-pill btn-sm px-3" onclick="exportPDF()">
                <i class="fas fa-file-pdf me-2"></i>Export PDF
            </button>
            <a href="?m=karyawan&s=riwayat&export=excel&bulan=<?php echo $bulan_filter; ?>&tanggal=<?php echo $tanggal_filter; ?>" class="btn btn-outline-success rounded-pill btn-sm px-3">
                <i class="fas fa-file-excel me-2"></i>Excel
            </a>
        </div>
    </div>

    <!-- Filter -->
    <div class="filter-card d-print-none">
        <form action="" method="GET" class="row g-3">
            <input type="hidden" name="m" value="karyawan">
            <input type="hidden" name="s" value="riwayat">
            <div class="col-md-4">
                <label class="form-label small fw-bold">Filter Bulan</label>
                <input type="month" name="bulan" class="form-control form-control-sm rounded-pill px-3" value="<?php echo $bulan_filter; ?>" onchange="this.form.submit()">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold">Atau Pilih Tanggal</label>
                <input type="date" name="tanggal" class="form-control form-control-sm rounded-pill px-3" value="<?php echo $tanggal_filter; ?>" onchange="this.form.submit()">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <a href="?m=karyawan&s=riwayat" class="btn btn-light rounded-pill btn-sm px-3 w-100">Reset</a>
            </div>
        </form>
    </div>

    <!-- Summary -->
    <div class="row g-3 mb-4">
        <div class="col-4">
            <div class="text-center p-3 border rounded-3 bg-white">
                <h3 class="fw-bold text-success mb-0"><?php echo $total_masuk; ?></h3>
                <small class="text-muted">Hadir</small>
            </div>
        </div>
        <div class="col-4">
            <div class="text-center p-3 border rounded-3 bg-white">
                <h3 class="fw-bold text-warning mb-0"><?php echo $total_izin; ?></h3>
                <small class="text-muted">Izin/Sakit</small>
            </div>
        </div>
        <div class="col-4">
            <div class="text-center p-3 border rounded-3 bg-white">
                <h3 class="fw-bold text-primary mb-0"><?php echo count($records); ?></h3>
                <small class="text-muted">Total</small>
            </div>
        </div>
    </div>

    <!-- Data List -->
    <div class="riwayat-list">
        <?php if (count($records) > 0): ?>
            <?php foreach ($records as $row):
        $tipe_lower = strtolower($row['tipe']);
        $indicator = 'bg-masuk';
        if ($tipe_lower == 'pulang')
            $indicator = 'bg-pulang';
        elseif ($tipe_lower == 'izin')
            $indicator = 'bg-izin';
        elseif ($tipe_lower == 'sakit')
            $indicator = 'bg-sakit';
?>
            <div class="record-card">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center mb-1">
                            <span class="type-indicator <?php echo $indicator; ?>"></span>
                            <span class="fw-bold text-dark me-2"><?php echo strtoupper($row['tipe']); ?></span>
                            <small class="text-muted">• <?php echo date('d M Y, H:i', $row['timestamp']); ?> WIB</small>
                        </div>
                        <div class="small text-muted mb-0">
                            <strong>Keterangan:</strong> <?php echo htmlspecialchars($row['keterangan'] ?: '-'); ?>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end mt-2 mt-md-0">
                        <?php if ($row['lokasi'] && $row['lokasi'] !== '-'): ?>
                            <a href="https://maps.google.com/?q=<?php echo $row['lokasi']; ?>" target="_blank" class="btn btn-sm btn-link text-decoration-none px-0">
                                <i class="fas fa-map-marker-alt me-1 text-danger"></i> Lihat Lokasi
                            </a>
                        <?php
        else: ?>
                            <span class="text-muted small italic">- Tanpa Koordinat -</span>
                        <?php
        endif; ?>
                    </div>
                </div>
            </div>
            <?php
    endforeach; ?>
        <?php
else: ?>
            <div class="text-center py-5">
                <i class="fas fa-folder-open fa-3x text-light mb-3"></i>
                <p class="text-muted">Tidak ada data riwayat untuk periode ini.</p>
            </div>
        <?php
endif; ?>
    </div>
</div>

<!-- Modal PDF Overlay (Invisible during normal view) -->
<div id="pdf-header-template" style="display: none;">
    <div style="text-align: center; border-bottom: 2px solid #000; padding-bottom: 20px; margin-bottom: 30px;">
        <h2 style="margin-bottom: 5px;">PAGUYUBAN KSE UINSU</h2>
        <p style="margin: 0;">REKAPITULASI RIWAYAT KEHADIRAN BESWAN</p>
        <p style="margin: 0; font-size: 12px; color: #444;">Nama: <strong><?php echo $_SESSION['namasi']; ?></strong> | Periode: <?php echo $bulan_filter; ?></p>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
function exportPDF() {
    const element = document.getElementById('exportContent');
    const name = "<?php echo $_SESSION['namasi']; ?>";
    const date = "<?php echo date('Y-m-d'); ?>";
    
    // Create a clone to customize for PDF
    const clone = element.cloneNode(true);
    
    // Add PDF Header
    const pdfHeader = document.getElementById('pdf-header-template').innerHTML;
    const headerWrapper = document.createElement('div');
    headerWrapper.innerHTML = pdfHeader;
    clone.insertBefore(headerWrapper, clone.firstChild);
    
    // Hide buttons in PDF
    const buttons = clone.querySelectorAll('.d-print-none');
    buttons.forEach(btn => btn.style.display = 'none');
    
    // Style adjustments for PDF
    clone.style.padding = '20px';
    clone.style.background = '#fff';

    const opt = {
        margin:       [10, 10, 10, 10],
        filename:     `Riwayat_Absen_${name}_${date}.pdf`,
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2, useCORS: true, letterRendering: true },
        jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };

    html2pdf().set(opt).from(clone).save();
}
</script>

<?php include "footer.php"; ?>
<?php
// Handle Excel export separately if the header is already sent (but index.php handles include, so we must be careful)
// The existing riwayat_absensi.php had an exit() after excel export.
if (isset($_GET['export']) && $_GET['export'] == 'excel') {
    ob_end_clean(); // Clear buffer
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Riwayat_Absensi_' . $_SESSION['namasi'] . '_' . date('Y-m-d') . '.xls"');

    echo "<table border='1'>";
    echo "<tr><th>No</th><th>Waktu</th><th>Tipe</th><th>Keterangan</th><th>Lokasi</th></tr>";
    $no = 1;
    foreach ($records as $row) {
        echo "<tr>";
        echo "<td>" . $no++ . "</td>";
        echo "<td>" . date('d-m-Y H:i', $row['timestamp']) . "</td>";
        echo "<td>" . strtoupper($row['tipe']) . "</td>";
        echo "<td>" . $row['keterangan'] . "</td>";
        echo "<td>" . $row['lokasi'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    exit();
}
?>
