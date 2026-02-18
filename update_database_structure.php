<?php
include 'koneksi.php';

echo "<h2>Update Database Structure - KSE UINSU</h2>";
echo "<p>Memperbarui struktur database untuk sistem kehadiran beswan...</p>";

// Cek apakah database menggunakan struktur lama atau baru
$sql_check_version = "SHOW TABLES LIKE 'tb_pengaturan'";
$result_check_version = mysqli_query($koneksi, $sql_check_version);

if (mysqli_num_rows($result_check_version) == 0) {
    echo "<div style='background: #fff3cd; padding: 15px; border: 1px solid #ffeaa7; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>⚠️ Database Perlu Diperbarui</h3>";
    echo "<p>Database Anda menggunakan struktur lama. Silakan import file <strong>karyawansi.sql</strong> yang baru untuk mendapatkan fitur terbaru.</p>";
    echo "<p>File <strong>karyawansi.sql</strong> sudah berisi:</p>";
    echo "<ul>";
    echo "<li>✅ Struktur tabel yang diperbarui</li>";
    echo "<li>✅ Kolom lokasi dan keterangan di tb_absen</li>";
    echo "<li>✅ Foreign key constraints</li>";
    echo "<li>✅ Indexes untuk performa optimal</li>";
    echo "<li>✅ Views untuk laporan</li>";
    echo "<li>✅ Sample data untuk testing</li>";
    echo "</ul>";
    echo "<p><strong>Cara import:</strong></p>";
    echo "<ol>";
    echo "<li>Buka phpMyAdmin atau MySQL client</li>";
    echo "<li>Drop database lama (jika ada): <code>DROP DATABASE karyawansi;</code></li>";
    echo "<li>Import file karyawansi.sql</li>";
    echo "<li>Refresh halaman ini</li>";
    echo "</ol>";
    echo "</div>";
} else {
    echo "<div style='background: #d4edda; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>✅ Database Sudah Menggunakan Struktur Terbaru</h3>";
    echo "<p>Database Anda sudah menggunakan struktur yang terbaru dengan semua fitur modern.</p>";
    echo "</div>";
}

// Cek kolom-kolom penting di tb_absen
echo "<h3>Status Tabel tb_absen:</h3>";
$sql_describe_absen = "DESCRIBE tb_absen";
$result_describe_absen = mysqli_query($koneksi, $sql_describe_absen);

if ($result_describe_absen) {
    $columns = [];
    while ($row = mysqli_fetch_assoc($result_describe_absen)) {
        $columns[] = $row['Field'];
    }
    
    echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<strong>Kolom yang tersedia:</strong><br>";
    
    $required_columns = ['id', 'id_karyawan', 'nama', 'waktu', 'tipe', 'kegiatan', 'keterangan', 'lokasi'];
    foreach ($required_columns as $col) {
        if (in_array($col, $columns)) {
            echo "✅ $col<br>";
        } else {
            echo "❌ $col (tidak ada)<br>";
        }
    }
    echo "</div>";
} else {
    echo "<p style='color: red;'>❌ Tidak dapat mengakses tabel tb_absen</p>";
}

// Cek data admin
echo "<h3>Status Admin:</h3>";
$sql_check_admin = "SELECT username FROM tb_daftar WHERE username = 'admin'";
$result_check_admin = mysqli_query($koneksi, $sql_check_admin);

if ($result_check_admin && mysqli_num_rows($result_check_admin) > 0) {
    echo "<p>✅ Admin user 'admin' sudah tersedia</p>";
} else {
    echo "<p>❌ Admin user belum ada, akan dibuat...</p>";
    $sql_create_admin = "INSERT INTO tb_daftar (username, password) VALUES ('admin', 'admin123')";
    if (mysqli_query($koneksi, $sql_create_admin)) {
        echo "<p>✅ Admin user berhasil dibuat</p>";
    } else {
        echo "<p>❌ Gagal membuat admin user: " . mysqli_error($koneksi) . "</p>";
    }
}

// Cek sample data karyawan
echo "<h3>Status Data Karyawan:</h3>";
$sql_count_karyawan = "SELECT COUNT(*) as total FROM tb_karyawan";
$result_count_karyawan = mysqli_query($koneksi, $sql_count_karyawan);

if ($result_count_karyawan) {
    $total_karyawan = mysqli_fetch_assoc($result_count_karyawan)['total'];
    echo "<p>📊 Total karyawan/beswan: <strong>$total_karyawan</strong></p>";
    
    if ($total_karyawan == 0) {
        echo "<div style='background: #fff3cd; padding: 10px; border-radius: 5px;'>";
        echo "<p>⚠️ Belum ada data karyawan. Silakan tambah data melalui menu admin.</p>";
        echo "</div>";
    }
} else {
    echo "<p>❌ Tidak dapat mengakses data karyawan</p>";
}

// Informasi file database
echo "<h3>Informasi Database:</h3>";
echo "<div style='background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
echo "<p><strong>File Database Utama:</strong> <code>karyawansi.sql</code></p>";
echo "<p><strong>Status:</strong> File database lainnya sudah dihapus untuk menghindari kebingungan</p>";
echo "<p><strong>Fitur Terbaru:</strong></p>";
echo "<ul>";
echo "<li>🎯 Filter absensi harian dan bulanan</li>";
echo "<li>📊 Dashboard dengan statistik real-time</li>";
echo "<li>📱 Design responsive dan modern</li>";
echo "<li>📋 Export Excel untuk rekap data</li>";
echo "<li>🗺️ Tracking lokasi GPS</li>";
echo "<li>👥 Manajemen data beswan lengkap</li>";
echo "</ul>";
echo "</div>";

echo "<div style='text-align: center; margin: 20px 0;'>";
echo "<a href='admin_dashboard_modern.php' style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 24px; text-decoration: none; border-radius: 8px; display: inline-block; font-weight: 500;'>← Kembali ke Dashboard Admin</a>";
echo "</div>";

echo "<hr>";
echo "<p style='text-align: center; color: #6c757d; font-size: 14px;'>";
echo "© 2025 KSE UINSU - Sistem Informasi Kehadiran Beswan v2.0";
echo "</p>";
?>