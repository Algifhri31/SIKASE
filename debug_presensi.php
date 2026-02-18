<?php
session_start();
require 'koneksi.php';

echo "<h2>Debug Presensi Form</h2>";

// Check if form was submitted
if (isset($_POST['simpan'])) {
    echo "<h3>✅ Form Submitted!</h3>";
    echo "<pre>";
    echo "POST Data:\n";
    print_r($_POST);
    echo "\n\nFILES Data:\n";
    print_r($_FILES);
    echo "\n\nSESSION Data:\n";
    print_r($_SESSION);
    echo "</pre>";
    
    // Check database connection
    if ($koneksi) {
        echo "<p style='color: green;'>✓ Database connected</p>";
    } else {
        echo "<p style='color: red;'>✗ Database connection failed: " . mysqli_connect_error() . "</p>";
    }
    
    // Check tables
    $check_absen = mysqli_query($koneksi, "SHOW TABLES LIKE 'tb_absen'");
    $check_ket = mysqli_query($koneksi, "SHOW TABLES LIKE 'tb_keterangan'");
    
    echo "<p>tb_absen exists: " . (mysqli_num_rows($check_absen) > 0 ? "✓ Yes" : "✗ No") . "</p>";
    echo "<p>tb_keterangan exists: " . (mysqli_num_rows($check_ket) > 0 ? "✓ Yes" : "✗ No") . "</p>";
    
} else {
    echo "<h3>❌ Form Not Submitted</h3>";
    echo "<p>No POST data received</p>";
}

// Show test form
?>
<hr>
<h3>Test Form</h3>
<form method="POST" enctype="multipart/form-data">
    <div style="margin-bottom: 10px;">
        <label>Status:</label>
        <select name="status_kehadiran" required>
            <option value="">-- Pilih --</option>
            <option value="Hadir">Hadir</option>
            <option value="Izin">Izin</option>
            <option value="Sakit">Sakit</option>
        </select>
    </div>
    
    <div style="margin-bottom: 10px;">
        <label>ID Karyawan:</label>
        <input type="text" name="id_karyawan" value="TEST123" required>
    </div>
    
    <div style="margin-bottom: 10px;">
        <label>Nama:</label>
        <input type="text" name="nama" value="Test User" required>
    </div>
    
    <div style="margin-bottom: 10px;">
        <label>Waktu:</label>
        <input type="text" name="waktu" value="<?php echo date('d F Y H:i:s'); ?>" required>
    </div>
    
    <div style="margin-bottom: 10px;">
        <label>Tipe:</label>
        <input type="text" name="tipe" value="masuk" required>
    </div>
    
    <div style="margin-bottom: 10px;">
        <label>Lokasi:</label>
        <input type="text" name="lokasi" value="-6.200000, 106.816666">
    </div>
    
    <div style="margin-bottom: 10px;">
        <label>Kegiatan:</label>
        <input type="text" name="kegiatan" value="Test kegiatan" required>
    </div>
    
    <div style="margin-bottom: 10px;">
        <label>Alasan:</label>
        <textarea name="alasan" required>Test alasan</textarea>
    </div>
    
    <div style="margin-bottom: 10px;">
        <label>Bukti (optional):</label>
        <input type="file" name="bukti">
    </div>
    
    <button type="submit" name="simpan" style="padding: 10px 20px; background: #4CAF50; color: white; border: none; cursor: pointer;">
        Submit Test
    </button>
</form>

<hr>
<p><a href="karyawan/index.php?m=presensi">Go to Real Presensi Page</a></p>
