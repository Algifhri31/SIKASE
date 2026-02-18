<?php
require 'koneksi.php';

echo "<h2>Test Insert to tb_keterangan</h2>";

// Test data
$test_data = [
    'id_karyawan' => 'TEST123',
    'nama' => 'Test User',
    'keterangan' => 'Izin',
    'alasan' => 'Test alasan untuk testing insert',
    'waktu' => date('d F Y H:i:s'),
    'bukti' => ''
];

echo "<h3>Test Data:</h3>";
echo "<pre>";
print_r($test_data);
echo "</pre>";

// Try to insert
echo "<h3>Attempting Insert...</h3>";

$stmt = mysqli_prepare($koneksi, "INSERT INTO tb_keterangan (id_karyawan, nama, keterangan, alasan, waktu, bukti) VALUES (?, ?, ?, ?, ?, ?)");

if ($stmt) {
    echo "<p style='color: green;'>✓ Prepare statement successful</p>";
    
    mysqli_stmt_bind_param($stmt, "ssssss", 
        $test_data['id_karyawan'], 
        $test_data['nama'], 
        $test_data['keterangan'], 
        $test_data['alasan'], 
        $test_data['waktu'], 
        $test_data['bukti']
    );
    
    $result = mysqli_stmt_execute($stmt);
    
    if ($result) {
        $insert_id = mysqli_insert_id($koneksi);
        echo "<p style='color: green;'>✓ Insert successful! Insert ID: $insert_id</p>";
        
        // Verify data
        echo "<h3>Verifying Data:</h3>";
        $verify = mysqli_query($koneksi, "SELECT * FROM tb_keterangan WHERE id = $insert_id");
        if ($verify && mysqli_num_rows($verify) > 0) {
            $row = mysqli_fetch_assoc($verify);
            echo "<pre>";
            print_r($row);
            echo "</pre>";
            
            // Delete test data
            mysqli_query($koneksi, "DELETE FROM tb_keterangan WHERE id = $insert_id");
            echo "<p style='color: orange;'>Test data deleted</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ Insert failed: " . mysqli_stmt_error($stmt) . "</p>";
    }
    
    mysqli_stmt_close($stmt);
} else {
    echo "<p style='color: red;'>✗ Prepare statement failed: " . mysqli_error($koneksi) . "</p>";
}

// Show table structure
echo "<hr>";
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
}

echo "<hr>";
echo "<p><a href='view_error_log.php'>View Error Log</a></p>";
echo "<p><a href='check_keterangan_table.php'>Check Database</a></p>";
echo "<p><a href='karyawan/index.php?m=presensi'>Go to Presensi Page</a></p>";
?>
