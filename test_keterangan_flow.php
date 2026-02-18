<?php
require 'koneksi.php';

echo "<h2>Test Keterangan Data Flow</h2>";

// 1. Check tb_keterangan structure
echo "<h3>1. tb_keterangan Table Structure:</h3>";
$res = mysqli_query($koneksi, "DESCRIBE tb_keterangan");
if ($res) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = mysqli_fetch_assoc($res)) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>Error: " . mysqli_error($koneksi) . "</p>";
}

// 2. Check recent keterangan data
echo "<h3>2. Recent Keterangan Data (Last 5):</h3>";
$res2 = mysqli_query($koneksi, "SELECT * FROM tb_keterangan ORDER BY waktu DESC LIMIT 5");
if ($res2) {
    if (mysqli_num_rows($res2) > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>ID Karyawan</th><th>Nama</th><th>Keterangan</th><th>Alasan</th><th>Waktu</th><th>Bukti</th></tr>";
        while ($row2 = mysqli_fetch_assoc($res2)) {
            echo "<tr>";
            echo "<td>" . $row2['id'] . "</td>";
            echo "<td>" . $row2['id_karyawan'] . "</td>";
            echo "<td>" . $row2['nama'] . "</td>";
            echo "<td>" . $row2['keterangan'] . "</td>";
            echo "<td>" . substr($row2['alasan'], 0, 50) . "...</td>";
            echo "<td>" . $row2['waktu'] . "</td>";
            echo "<td>" . ($row2['bukti'] ?: '-') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No data found in tb_keterangan</p>";
    }
} else {
    echo "<p style='color: red;'>Error: " . mysqli_error($koneksi) . "</p>";
}

// 3. Check uploads folder
echo "<h3>3. Uploads Folder Status:</h3>";
$uploads_dir = __DIR__ . '/uploads';
if (is_dir($uploads_dir)) {
    echo "<p style='color: green;'>✓ Uploads folder exists</p>";
    echo "<p>Path: " . $uploads_dir . "</p>";
    
    $files = scandir($uploads_dir);
    $files = array_diff($files, array('.', '..', '.htaccess', 'index.php'));
    
    if (count($files) > 0) {
        echo "<p>Files in uploads folder:</p>";
        echo "<ul>";
        foreach ($files as $file) {
            $filepath = $uploads_dir . '/' . $file;
            $filesize = filesize($filepath);
            echo "<li>" . $file . " (" . round($filesize / 1024, 2) . " KB)</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No files uploaded yet</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Uploads folder does not exist</p>";
}

// 4. Test data insertion (simulation)
echo "<h3>4. Test Data Insertion:</h3>";
echo "<p>To test the flow:</p>";
echo "<ol>";
echo "<li>Login as a beswan user</li>";
echo "<li>Go to Presensi page</li>";
echo "<li>Select 'Izin' or 'Sakit'</li>";
echo "<li>Upload a file (JPG/PNG/PDF, max 2MB)</li>";
echo "<li>Fill in the form and submit</li>";
echo "<li>Check if data appears in Data Keterangan page (admin dashboard)</li>";
echo "</ol>";

echo "<p><a href='data_keterangan_modern.php'>View Data Keterangan Page</a></p>";
echo "<p><a href='karyawan/index.php?m=presensi'>Go to Presensi Page</a></p>";
?>
