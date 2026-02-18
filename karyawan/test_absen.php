<?php
session_start();

echo "<h2>Test Data Absensi</h2>";

// Include koneksi
include '../koneksi.php';

// Test koneksi
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

echo "<h3>1. Test Koneksi Database:</h3>";
echo "✅ Koneksi berhasil<br><br>";

// Test session
echo "<h3>2. Test Session:</h3>";
if (isset($_SESSION['idsi'])) {
    echo "✅ Session ID: " . $_SESSION['idsi'] . "<br>";
    echo "✅ Session Name: " . ($_SESSION['namasi'] ?? 'Tidak ada') . "<br><br>";
    
    $id = $_SESSION['idsi'];
    
    // Test struktur tabel
    echo "<h3>3. Test Struktur Tabel tb_absen:</h3>";
    $desc_result = mysqli_query($koneksi, "DESCRIBE tb_absen");
    if ($desc_result) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        while ($row = mysqli_fetch_assoc($desc_result)) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . $row['Default'] . "</td>";
            echo "<td>" . $row['Extra'] . "</td>";
            echo "</tr>";
        }
        echo "</table><br>";
    }
    
    // Test data absensi untuk user ini
    echo "<h3>4. Test Data Absensi untuk ID: $id</h3>";
    $test_query = "SELECT * FROM tb_absen WHERE id_karyawan = '$id' ORDER BY id DESC LIMIT 5";
    $result = mysqli_query($koneksi, $test_query);
    
    if ($result) {
        $count = mysqli_num_rows($result);
        echo "✅ Query berhasil. Ditemukan $count data absensi untuk user ini.<br><br>";
        
        if ($count > 0) {
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>ID</th><th>ID Karyawan</th><th>Nama</th><th>Waktu</th><th>Tipe</th><th>Kegiatan</th><th>Keterangan</th></tr>";
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['id_karyawan'] . "</td>";
                echo "<td>" . $row['nama'] . "</td>";
                echo "<td>" . $row['waktu'] . "</td>";
                echo "<td>" . $row['tipe'] . "</td>";
                echo "<td>" . substr($row['kegiatan'], 0, 30) . "...</td>";
                echo "<td>" . substr($row['keterangan'], 0, 30) . "...</td>";
                echo "</tr>";
            }
            echo "</table><br>";
        } else {
            echo "❌ Tidak ada data absensi untuk user ini.<br><br>";
        }
    } else {
        echo "❌ Query gagal: " . mysqli_error($koneksi) . "<br><br>";
    }
    
    // Test semua data absensi
    echo "<h3>5. Test Semua Data Absensi (5 terakhir):</h3>";
    $all_query = "SELECT * FROM tb_absen ORDER BY id DESC LIMIT 5";
    $all_result = mysqli_query($koneksi, $all_query);
    
    if ($all_result) {
        $all_count = mysqli_num_rows($all_result);
        echo "✅ Total data absensi di database: ";
        
        $total_query = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_absen");
        $total_data = mysqli_fetch_assoc($total_query);
        echo $total_data['total'] . " data<br><br>";
        
        if ($all_count > 0) {
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>ID</th><th>ID Karyawan</th><th>Nama</th><th>Waktu</th><th>Tipe</th></tr>";
            while ($row = mysqli_fetch_assoc($all_result)) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['id_karyawan'] . "</td>";
                echo "<td>" . $row['nama'] . "</td>";
                echo "<td>" . $row['waktu'] . "</td>";
                echo "<td>" . $row['tipe'] . "</td>";
                echo "</tr>";
            }
            echo "</table><br>";
        }
    }
    
} else {
    echo "❌ Session tidak ditemukan<br>";
}

echo "<br><a href='index.php?m=karyawan&s=riwayat'>Kembali ke Riwayat Absen</a>";
?>

<style>
table {
    margin: 10px 0;
}
th, td {
    padding: 8px;
    text-align: left;
}
th {
    background-color: #f2f2f2;
}
</style>