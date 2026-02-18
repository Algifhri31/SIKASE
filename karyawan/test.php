<?php
session_start();

echo "<h2>Test Dashboard Beswan</h2>";

// Test 1: Session
echo "<h3>1. Test Session:</h3>";
if (isset($_SESSION['idsi'])) {
    echo "✅ Session ID: " . $_SESSION['idsi'] . "<br>";
    echo "✅ Session Name: " . ($_SESSION['namasi'] ?? 'Tidak ada') . "<br>";
    echo "✅ Session Level: " . ($_SESSION['level'] ?? 'Tidak ada') . "<br>";
} else {
    echo "❌ Session tidak ditemukan<br>";
}

// Test 2: Koneksi Database
echo "<h3>2. Test Koneksi Database:</h3>";
if (file_exists('../koneksi.php')) {
    include '../koneksi.php';
    if (isset($koneksi) && $koneksi) {
        echo "✅ Koneksi database berhasil<br>";
        
        // Test query
        $test_query = "SELECT COUNT(*) as total FROM tb_karyawan";
        $result = mysqli_query($koneksi, $test_query);
        if ($result) {
            $data = mysqli_fetch_assoc($result);
            echo "✅ Query test berhasil. Total karyawan: " . $data['total'] . "<br>";
        } else {
            echo "❌ Query test gagal: " . mysqli_error($koneksi) . "<br>";
        }
    } else {
        echo "❌ Koneksi database gagal<br>";
    }
} else {
    echo "❌ File koneksi.php tidak ditemukan<br>";
}

// Test 3: File Dashboard
echo "<h3>3. Test File Dashboard:</h3>";
if (file_exists('dashboard_simple.php')) {
    echo "✅ File dashboard_simple.php ada<br>";
} else {
    echo "❌ File dashboard_simple.php tidak ada<br>";
}

if (file_exists('header.php')) {
    echo "✅ File header.php ada<br>";
} else {
    echo "❌ File header.php tidak ada<br>";
}

if (file_exists('footer.php')) {
    echo "✅ File footer.php ada<br>";
} else {
    echo "❌ File footer.php tidak ada<br>";
}

echo "<br><a href='index.php'>Kembali ke Dashboard</a>";
?>