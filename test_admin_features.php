<?php
// File untuk testing fitur admin dashboard
session_start();
include 'koneksi.php';

echo "<h1>Test Admin Dashboard Features</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .test-item { margin: 10px 0; padding: 10px; border-left: 4px solid #007bff; background: #f8f9fa; }
    .success { border-left-color: #28a745; }
    .error { border-left-color: #dc3545; }
    .warning { border-left-color: #ffc107; }
</style>";

// Test 1: Database Connection
echo "<div class='test-item'>";
echo "<h3>1. Test Database Connection</h3>";
if ($koneksi) {
    echo "<p class='success'>✅ Database connection successful</p>";
} else {
    echo "<p class='error'>❌ Database connection failed: " . mysqli_connect_error() . "</p>";
}
echo "</div>";

// Test 2: Test Tables Exist
echo "<div class='test-item'>";
echo "<h3>2. Test Database Tables</h3>";
$tables = ['tb_karyawan', 'tb_daftar', 'tb_absen', 'tb_jabatan', 'tb_keterangan'];
foreach ($tables as $table) {
    $result = mysqli_query($koneksi, "SHOW TABLES LIKE '$table'");
    if (mysqli_num_rows($result) > 0) {
        echo "<p class='success'>✅ Table $table exists</p>";
    } else {
        echo "<p class='error'>❌ Table $table not found</p>";
    }
}
echo "</div>";

// Test 3: Test File Existence
echo "<div class='test-item'>";
echo "<h3>3. Test Required Files</h3>";
$files = [
    'admin2.php' => 'Main Admin Dashboard',
    'datakaryawan.php' => 'Data Beswan Page',
    'data_absen.php' => 'Data Absensi Page',
    'datauser.php' => 'Data Admin Page',
    'datajabatan.php' => 'Data Divisi Page',
    'data_keterangan.php' => 'Data Keterangan Page',
    'export.php' => 'Export Data Page',
    'admin_save.php' => 'Admin Save Process',
    'dt_karyawan_sv.php' => 'Karyawan Save Process',
    'jabatan_sv.php' => 'Jabatan Save Process',
    'hapus.php' => 'Delete Process',
    'proedit_karyawan.php' => 'Edit Process'
];

foreach ($files as $file => $description) {
    if (file_exists($file)) {
        echo "<p class='success'>✅ $file ($description) exists</p>";
    } else {
        echo "<p class='error'>❌ $file ($description) not found</p>";
    }
}
echo "</div>";

// Test 4: Test Directory Structure
echo "<div class='test-item'>";
echo "<h3>4. Test Directory Structure</h3>";
$directories = ['images', 'css', 'js', 'vendor', 'absen'];
foreach ($directories as $dir) {
    if (is_dir($dir)) {
        echo "<p class='success'>✅ Directory $dir exists</p>";
    } else {
        echo "<p class='warning'>⚠️ Directory $dir not found (may be optional)</p>";
    }
}
echo "</div>";

// Test 5: Test Sample Data
echo "<div class='test-item'>";
echo "<h3>5. Test Sample Data</h3>";
$data_tests = [
    'tb_karyawan' => 'SELECT COUNT(*) as count FROM tb_karyawan',
    'tb_daftar' => 'SELECT COUNT(*) as count FROM tb_daftar',
    'tb_absen' => 'SELECT COUNT(*) as count FROM tb_absen',
    'tb_jabatan' => 'SELECT COUNT(*) as count FROM tb_jabatan'
];

foreach ($data_tests as $table => $query) {
    $result = mysqli_query($koneksi, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $count = $row['count'];
        if ($count > 0) {
            echo "<p class='success'>✅ $table has $count records</p>";
        } else {
            echo "<p class='warning'>⚠️ $table is empty (no data)</p>";
        }
    } else {
        echo "<p class='error'>❌ Error querying $table: " . mysqli_error($koneksi) . "</p>";
    }
}
echo "</div>";

// Test 6: Test Navigation Links
echo "<div class='test-item'>";
echo "<h3>6. Test Navigation Links</h3>";
echo "<p><a href='admin2.php' target='_blank'>🔗 Test Admin Dashboard</a></p>";
echo "<p><a href='datakaryawan.php' target='_blank'>🔗 Test Data Beswan</a></p>";
echo "<p><a href='data_absen.php' target='_blank'>🔗 Test Data Absensi</a></p>";
echo "<p><a href='datauser.php' target='_blank'>🔗 Test Data Admin</a></p>";
echo "<p><a href='datajabatan.php' target='_blank'>🔗 Test Data Divisi</a></p>";
echo "<p><a href='data_keterangan.php' target='_blank'>🔗 Test Data Keterangan</a></p>";
echo "<p><a href='export.php' target='_blank'>🔗 Test Export Data</a></p>";
echo "</div>";

// Test 7: Test Session (if logged in)
echo "<div class='test-item'>";
echo "<h3>7. Test Session Status</h3>";
if (isset($_SESSION['username'])) {
    echo "<p class='success'>✅ User logged in as: " . htmlspecialchars($_SESSION['username']) . "</p>";
    if (isset($_SESSION['level'])) {
        echo "<p class='success'>✅ User level: " . htmlspecialchars($_SESSION['level']) . "</p>";
    }
} else {
    echo "<p class='warning'>⚠️ No active session (not logged in)</p>";
    echo "<p><a href='login.php'>🔗 Go to Login Page</a></p>";
}
echo "</div>";

echo "<div class='test-item'>";
echo "<h3>8. Quick Actions</h3>";
echo "<p><a href='login.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>Login</a></p>";
echo "<p><a href='admin2.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>Admin Dashboard</a></p>";
echo "<p><a href='cleanup_project.php' style='background: #ffc107; color: black; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>Project Info</a></p>";
echo "</div>";

echo "<hr>";
echo "<p><strong>Test completed at:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<p><strong>Note:</strong> Jika ada error, periksa file koneksi.php dan pastikan database sudah disetup dengan benar.</p>";
?>