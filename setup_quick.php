<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h2>🚀 Quick Setup - KSE System</h2>";

// Test koneksi database
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "karyawansi";

// Coba koneksi tanpa database dulu
$mysqli = @mysqli_connect($db_host, $db_user, $db_pass);

if (!$mysqli) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; color: #721c24; margin: 10px 0;'>";
    echo "<h3>❌ Koneksi MySQL Gagal</h3>";
    echo "<p>Error: " . mysqli_connect_error() . "</p>";
    echo "<p>Pastikan MySQL server berjalan dan kredensial benar.</p>";
    echo "</div>";
    exit;
}

echo "<p>✅ Koneksi MySQL berhasil</p>";

// Cek apakah database ada
$db_exists = mysqli_select_db($mysqli, $db_name);

if (!$db_exists) {
    echo "<p>⚠️ Database '$db_name' tidak ada, akan dibuat...</p>";
    
    // Buat database
    $sql_create_db = "CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci";
    if (mysqli_query($mysqli, $sql_create_db)) {
        echo "<p>✅ Database '$db_name' berhasil dibuat</p>";
        mysqli_select_db($mysqli, $db_name);
    } else {
        echo "<p>❌ Gagal membuat database: " . mysqli_error($mysqli) . "</p>";
        exit;
    }
} else {
    echo "<p>✅ Database '$db_name' sudah ada</p>";
}

// Cek tabel tb_daftar
$sql_check_table = "SHOW TABLES LIKE 'tb_daftar'";
$result_check_table = mysqli_query($mysqli, $sql_check_table);

if (mysqli_num_rows($result_check_table) == 0) {
    echo "<p>⚠️ Tabel tidak ada, akan dibuat struktur minimal...</p>";
    
    // Buat tabel minimal
    $sql_create_tables = "
    CREATE TABLE `tb_daftar` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `username` varchar(50) NOT NULL,
        `password` varchar(255) NOT NULL,
        `level` varchar(20) DEFAULT 'admin',
        PRIMARY KEY (`id`),
        UNIQUE KEY `username` (`username`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    
    CREATE TABLE `tb_karyawan` (
        `id_karyawan` varchar(20) NOT NULL,
        `username` varchar(50) NOT NULL,
        `password` varchar(255) NOT NULL,
        `nama` varchar(100) NOT NULL,
        `tmp_tgl_lahir` varchar(100) DEFAULT NULL,
        `jenkel` enum('Laki-laki','Perempuan') DEFAULT NULL,
        `agama` varchar(20) DEFAULT NULL,
        `alamat` text DEFAULT NULL,
        `no_tel` varchar(20) DEFAULT NULL,
        `jabatan` varchar(100) DEFAULT NULL,
        `foto` varchar(255) DEFAULT NULL,
        `status` enum('aktif','nonaktif') DEFAULT 'aktif',
        PRIMARY KEY (`id_karyawan`),
        UNIQUE KEY `username` (`username`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    
    CREATE TABLE `tb_absen` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `id_karyawan` varchar(20) DEFAULT NULL,
        `nama` varchar(100) NOT NULL,
        `waktu` datetime NOT NULL,
        `tipe` enum('masuk','pulang') NOT NULL,
        `kegiatan` text DEFAULT NULL,
        `keterangan` text DEFAULT NULL,
        `lokasi` text DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    
    CREATE TABLE `tb_jabatan` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `jabatan` varchar(100) NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    
    CREATE TABLE `tb_keterangan` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `id_karyawan` varchar(20) DEFAULT NULL,
        `nama` varchar(100) NOT NULL,
        `tanggal` date NOT NULL,
        `keterangan` enum('sakit','izin','alpha','dinas') NOT NULL,
        `alasan` text NOT NULL,
        `bukti` varchar(255) DEFAULT NULL,
        `status` enum('pending','approved','rejected') DEFAULT 'pending',
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
    
    // Execute multiple queries
    if (mysqli_multi_query($mysqli, $sql_create_tables)) {
        do {
            if ($result = mysqli_store_result($mysqli)) {
                mysqli_free_result($result);
            }
        } while (mysqli_next_result($mysqli));
        echo "<p>✅ Tabel berhasil dibuat</p>";
    } else {
        echo "<p>❌ Gagal membuat tabel: " . mysqli_error($mysqli) . "</p>";
    }
} else {
    echo "<p>✅ Tabel sudah ada</p>";
}

// Cek admin user
$admin_username = 'ADMINKECE';
$admin_password = 'ADMIN2025';

$sql_check_admin = "SELECT * FROM tb_daftar WHERE username = '$admin_username'";
$result_check_admin = mysqli_query($mysqli, $sql_check_admin);

if (!$result_check_admin || mysqli_num_rows($result_check_admin) == 0) {
    echo "<p>⚠️ Admin user tidak ada, akan dibuat...</p>";
    
    $sql_create_admin = "INSERT INTO tb_daftar (username, password, level) VALUES ('$admin_username', '$admin_password', 'admin')";
    if (mysqli_query($mysqli, $sql_create_admin)) {
        echo "<p>✅ Admin user berhasil dibuat</p>";
    } else {
        echo "<p>❌ Gagal membuat admin user: " . mysqli_error($mysqli) . "</p>";
    }
} else {
    $admin = mysqli_fetch_assoc($result_check_admin);
    echo "<p>✅ Admin user sudah ada</p>";
    echo "<p>Username: <strong>" . $admin['username'] . "</strong>, Password: <strong>" . $admin['password'] . "</strong></p>";
    
    // Pastikan password benar
    if ($admin['password'] !== $admin_password) {
        echo "<p>⚠️ Password admin akan diperbaiki...</p>";
        $sql_fix_password = "UPDATE tb_daftar SET password = '$admin_password' WHERE username = '$admin_username'";
        if (mysqli_query($mysqli, $sql_fix_password)) {
            echo "<p>✅ Password admin berhasil diperbaiki</p>";
        }
    }
}

// Tambah sample karyawan jika belum ada
$sql_check_karyawan = "SELECT COUNT(*) as total FROM tb_karyawan";
$result_check_karyawan = mysqli_query($mysqli, $sql_check_karyawan);
$total_karyawan = $result_check_karyawan ? mysqli_fetch_assoc($result_check_karyawan)['total'] : 0;

if ($total_karyawan == 0) {
    echo "<p>⚠️ Belum ada data karyawan, akan ditambahkan sample...</p>";
    
    $sql_sample_karyawan = "INSERT INTO tb_karyawan (id_karyawan, username, password, nama, tmp_tgl_lahir, jenkel, agama, alamat, no_tel, jabatan, status) VALUES 
    ('249014764', 'siti_zahra', 'password123', 'SITI ZAHRA', 'Medan, 15 Januari 2001', 'Perempuan', 'Islam', 'Jl. Williem Iskandar No. 123, Medan', '081234567890', 'Mahasiswa S1', 'aktif'),
    ('219011157', 'natasya_olivia', 'password123', 'Natasya Olivia Ningrum', 'Jakarta, 20 Februari 2000', 'Perempuan', 'Islam', 'Jl. Gatot Subroto No. 456, Jakarta', '081234567891', 'Mahasiswa S1', 'aktif')";
    
    if (mysqli_query($mysqli, $sql_sample_karyawan)) {
        echo "<p>✅ Sample karyawan berhasil ditambahkan</p>";
    } else {
        echo "<p>❌ Gagal menambahkan sample karyawan: " . mysqli_error($mysqli) . "</p>";
    }
} else {
    echo "<p>✅ Data karyawan sudah ada ($total_karyawan records)</p>";
}

mysqli_close($mysqli);

echo "<div style='background: #d4edda; padding: 20px; border-radius: 10px; margin: 20px 0; text-align: center;'>";
echo "<h3>🎉 Setup Selesai!</h3>";
echo "<p>Sistem siap digunakan dengan kredensial berikut:</p>";
echo "<div style='background: white; padding: 15px; border-radius: 8px; margin: 10px 0;'>";
echo "<strong>Admin Login:</strong><br>";
echo "Username: <code>ADMINKECE</code><br>";
echo "Password: <code>ADMIN2025</code>";
echo "</div>";
echo "<div style='background: white; padding: 15px; border-radius: 8px; margin: 10px 0;'>";
echo "<strong>Karyawan Login:</strong><br>";
echo "Username: <code>siti_zahra</code><br>";
echo "Password: <code>password123</code>";
echo "</div>";
echo "</div>";

echo "<div style='text-align: center; margin: 20px 0;'>";
echo "<a href='login_simple.php' style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 24px; text-decoration: none; border-radius: 8px; display: inline-block; font-weight: 500; margin: 5px;'>🚀 Login Sekarang</a>";
echo "<a href='debug_check.php' style='background: #28a745; color: white; padding: 12px 24px; text-decoration: none; border-radius: 8px; display: inline-block; font-weight: 500; margin: 5px;'>🔍 Debug Check</a>";
echo "</div>";
?>