<?php
// Script untuk membersihkan file-file debug dan duplikat
echo "<h2>🧹 Cleanup Project Structure</h2>";

// File-file debug yang bisa dihapus
$debug_files = [
    'add_admin_now.php',
    'admin_dashboard_modern.php', // sudah dicopy ke admin2.php
    'admin2_backup.php',
    'cek_admin.php',
    'debug_login.php',
    'fix_admin_login.php',
    'insert_admin_direct.php',
    'login_admin_fix.php',
    'proses_login_simple.php',
    'setup_admin.php',
    'tambah_admin.php'
];

echo "<h3>File Debug yang Akan Dihapus:</h3>";
echo "<ul>";
foreach ($debug_files as $file) {
    if (file_exists($file)) {
        echo "<li>✅ $file (ada)</li>";
    } else {
        echo "<li>❌ $file (tidak ada)</li>";
    }
}
echo "</ul>";

echo "<p><strong>File-file ini adalah file debug/testing yang tidak diperlukan lagi.</strong></p>";
echo "<p><a href='cleanup_project.php?action=delete' style='background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Hapus File Debug</a></p>";

if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    echo "<h3>🗑️ Menghapus File Debug...</h3>";
    
    foreach ($debug_files as $file) {
        if (file_exists($file)) {
            if (unlink($file)) {
                echo "<p>✅ $file dihapus</p>";
            } else {
                echo "<p>❌ Gagal menghapus $file</p>";
            }
        }
    }
    
    echo "<div style='background: #d4edda; color: #155724; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h4>✅ Cleanup Selesai!</h4>";
    echo "<p>File-file debug sudah dihapus. Project structure sekarang lebih bersih.</p>";
    echo "</div>";
}

echo "<hr>";
echo "<h3>📁 Struktur Project yang Direkomendasikan:</h3>";
echo "<pre>";
echo "KSEHADIR/
├── admin/                 # Halaman admin
├── karyawan/             # Halaman beswan
├── css/                  # Stylesheet
├── js/                   # JavaScript
├── images/               # Upload foto
├── vendor/               # Library eksternal
├── db/                   # Database files
├── login.php             # Halaman login
├── admin2.php            # Dashboard admin
├── proses_login.php      # Proses login
├── koneksi.php           # Koneksi database
├── logout.php            # Logout
└── export.php            # Export data
";
echo "</pre>";

echo "<p><a href='admin2.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Kembali ke Dashboard Admin</a></p>";
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 1000px;
    margin: 20px auto;
    padding: 20px;
    background: #f8f9fa;
}

pre {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    border-left: 4px solid #007bff;
    font-family: 'Courier New', monospace;
}

ul {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
</style>