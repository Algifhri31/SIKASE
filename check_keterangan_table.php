<?php
require 'koneksi.php';

echo "<h2>Check tb_keterangan Table Structure</h2>";

// Check if table exists
$check_table = mysqli_query($koneksi, "SHOW TABLES LIKE 'tb_keterangan'");
if (mysqli_num_rows($check_table) == 0) {
    echo "<p style='color: red;'>ERROR: Table tb_keterangan does not exist!</p>";
    echo "<p>Creating table...</p>";
    
    // Create table with waktu column (not tanggal)
    $create_sql = "CREATE TABLE IF NOT EXISTS `tb_keterangan` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `id_karyawan` varchar(20) DEFAULT NULL,
        `nama` varchar(100) NOT NULL,
        `keterangan` varchar(50) NOT NULL,
        `alasan` text NOT NULL,
        `waktu` varchar(100) NOT NULL,
        `bukti` varchar(255) DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if (mysqli_query($koneksi, $create_sql)) {
        echo "<p style='color: green;'>✓ Table created successfully!</p>";
    } else {
        echo "<p style='color: red;'>ERROR creating table: " . mysqli_error($koneksi) . "</p>";
    }
} else {
    echo "<p style='color: green;'>✓ Table tb_keterangan exists</p>";
}

// Show table structure
echo "<h3>Table Structure:</h3>";
$structure = mysqli_query($koneksi, "DESCRIBE tb_keterangan");
if ($structure) {
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr style='background: #f0f0f0;'><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = mysqli_fetch_assoc($structure)) {
        echo "<tr>";
        echo "<td><strong>" . $row['Field'] . "</strong></td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . ($row['Default'] ?: 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>ERROR: " . mysqli_error($koneksi) . "</p>";
}

// Show sample data
echo "<h3>Sample Data (Last 5 records):</h3>";
$data = mysqli_query($koneksi, "SELECT * FROM tb_keterangan ORDER BY id DESC LIMIT 5");
if ($data && mysqli_num_rows($data) > 0) {
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr style='background: #f0f0f0;'><th>ID</th><th>ID Karyawan</th><th>Nama</th><th>Keterangan</th><th>Alasan</th><th>Waktu</th><th>Bukti</th></tr>";
    while ($row = mysqli_fetch_assoc($data)) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['id_karyawan'] . "</td>";
        echo "<td>" . $row['nama'] . "</td>";
        echo "<td><span style='background: #fbbf24; padding: 3px 8px; border-radius: 4px;'>" . $row['keterangan'] . "</span></td>";
        echo "<td>" . substr($row['alasan'], 0, 50) . "...</td>";
        echo "<td>" . $row['waktu'] . "</td>";
        echo "<td>" . ($row['bukti'] ?: '-') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No data found. Table is empty.</p>";
}

echo "<hr>";
echo "<h3>Test Instructions:</h3>";
echo "<ol>";
echo "<li>Login as beswan: <a href='login.php' target='_blank'>Login Page</a></li>";
echo "<li>Go to Presensi: <a href='karyawan/index.php?m=presensi' target='_blank'>Presensi Page</a></li>";
echo "<li>Select 'Izin' or 'Sakit' from dropdown</li>";
echo "<li>Upload a file (JPG/PNG/PDF, max 2MB)</li>";
echo "<li>Fill form and submit</li>";
echo "<li>Check admin dashboard: <a href='data_keterangan_modern.php' target='_blank'>Data Keterangan</a></li>";
echo "</ol>";
?>
