-- =====================================================
-- Database: karyawansi
-- Sistem Informasi Kehadiran Beswan KSE UINSU
-- Updated: 2025
-- =====================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- =====================================================
-- Create Database
-- =====================================================
CREATE DATABASE IF NOT EXISTS `karyawansi` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `karyawansi`;

-- =====================================================
-- Table: tb_daftar (Admin Users)
-- =====================================================
CREATE TABLE `tb_daftar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `level` enum('admin','super_admin') DEFAULT 'admin',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default admin
INSERT INTO `tb_daftar` (`username`, `password`, `email`, `level`) VALUES 
('admin', 'admin123', 'admin@kse.uinsu.ac.id', 'admin');

-- =====================================================
-- Table: tb_jabatan (Positions/Divisions)
-- =====================================================
CREATE TABLE `tb_jabatan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `jabatan` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `jabatan` (`jabatan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default positions
INSERT INTO `tb_jabatan` (`jabatan`, `deskripsi`) VALUES
('Mahasiswa S1', 'Mahasiswa Strata 1'),
('Mahasiswa S2', 'Mahasiswa Strata 2'),
('Mahasiswa S3', 'Mahasiswa Strata 3'),
('Koordinator', 'Koordinator Kegiatan'),
('Sekretaris', 'Sekretaris Organisasi'),
('Bendahara', 'Bendahara Organisasi');

-- =====================================================
-- Table: tb_karyawan (Employees/Beswan)
-- =====================================================
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
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_karyawan`),
  UNIQUE KEY `username` (`username`),
  KEY `idx_nama` (`nama`),
  KEY `idx_jabatan` (`jabatan`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert sample data
INSERT INTO `tb_karyawan` (`id_karyawan`, `username`, `password`, `nama`, `tmp_tgl_lahir`, `jenkel`, `agama`, `alamat`, `no_tel`, `jabatan`, `status`) VALUES
('249014764', 'siti_zahra', 'password123', 'SITI ZAHRA', 'Medan, 15 Januari 2001', 'Perempuan', 'Islam', 'Jl. Williem Iskandar No. 123, Medan', '081234567890', 'Mahasiswa S1', 'aktif'),
('219011157', 'natasya_olivia', 'password123', 'Natasya Olivia Ningrum', 'Jakarta, 20 Februari 2000', 'Perempuan', 'Islam', 'Jl. Gatot Subroto No. 456, Jakarta', '081234567891', 'Mahasiswa S1', 'aktif'),
('249014765', 'nurul_fikria', 'password123', 'Nurul Fikria', 'Banda Aceh, 10 Maret 2001', 'Perempuan', 'Islam', 'Jl. T. Nyak Arief No. 789, Banda Aceh', '081234567892', 'Mahasiswa S1', 'aktif');

-- =====================================================
-- Table: tb_absen (Attendance Records)
-- =====================================================
CREATE TABLE `tb_absen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_karyawan` varchar(20) DEFAULT NULL,
  `nama` varchar(100) NOT NULL,
  `waktu` datetime NOT NULL,
  `tipe` enum('masuk','pulang') NOT NULL,
  `kegiatan` text DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `lokasi` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_id_karyawan` (`id_karyawan`),
  KEY `idx_waktu` (`waktu`),
  KEY `idx_tipe` (`tipe`),
  KEY `idx_tanggal` (DATE(`waktu`)),
  CONSTRAINT `fk_absen_karyawan` FOREIGN KEY (`id_karyawan`) REFERENCES `tb_karyawan` (`id_karyawan`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- Table: tb_keterangan (Absence Explanations)
-- =====================================================
CREATE TABLE `tb_keterangan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_karyawan` varchar(20) DEFAULT NULL,
  `nama` varchar(100) NOT NULL,
  `tanggal` date NOT NULL,
  `keterangan` enum('sakit','izin','alpha','dinas') NOT NULL,
  `alasan` text NOT NULL,
  `bukti` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `approved_by` varchar(50) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_id_karyawan` (`id_karyawan`),
  KEY `idx_tanggal` (`tanggal`),
  KEY `idx_status` (`status`),
  CONSTRAINT `fk_keterangan_karyawan` FOREIGN KEY (`id_karyawan`) REFERENCES `tb_karyawan` (`id_karyawan`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- Table: tb_pengaturan (System Settings)
-- =====================================================
CREATE TABLE `tb_pengaturan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_setting` varchar(100) NOT NULL,
  `nilai` text NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nama_setting` (`nama_setting`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default settings
INSERT INTO `tb_pengaturan` (`nama_setting`, `nilai`, `deskripsi`) VALUES
('nama_aplikasi', 'Sistem Kehadiran Beswan KSE', 'Nama aplikasi yang ditampilkan'),
('jam_masuk', '08:00', 'Jam masuk standar'),
('jam_pulang', '17:00', 'Jam pulang standar'),
('toleransi_terlambat', '15', 'Toleransi keterlambatan dalam menit'),
('radius_absen', '100', 'Radius absensi dalam meter'),
('koordinat_kantor', '3.5979699625578974, 98.736856146506', 'Koordinat lokasi kantor/kampus');

-- =====================================================
-- Views for Easy Data Access
-- =====================================================

-- View untuk laporan kehadiran
CREATE VIEW `v_laporan_kehadiran` AS
SELECT 
    k.id_karyawan,
    k.nama,
    k.jabatan,
    DATE(a.waktu) as tanggal,
    COUNT(CASE WHEN a.tipe = 'masuk' THEN 1 END) as total_masuk,
    COUNT(CASE WHEN a.tipe = 'pulang' THEN 1 END) as total_pulang,
    MIN(CASE WHEN a.tipe = 'masuk' THEN TIME(a.waktu) END) as jam_masuk,
    MAX(CASE WHEN a.tipe = 'pulang' THEN TIME(a.waktu) END) as jam_pulang
FROM tb_karyawan k
LEFT JOIN tb_absen a ON k.id_karyawan = a.id_karyawan
WHERE k.status = 'aktif'
GROUP BY k.id_karyawan, k.nama, k.jabatan, DATE(a.waktu);

-- View untuk statistik bulanan
CREATE VIEW `v_statistik_bulanan` AS
SELECT 
    k.id_karyawan,
    k.nama,
    k.jabatan,
    YEAR(a.waktu) as tahun,
    MONTH(a.waktu) as bulan,
    COUNT(DISTINCT DATE(a.waktu)) as hari_hadir,
    COUNT(CASE WHEN a.tipe = 'masuk' THEN 1 END) as total_masuk,
    COUNT(CASE WHEN a.tipe = 'pulang' THEN 1 END) as total_pulang,
    ROUND((COUNT(DISTINCT DATE(a.waktu)) / 22) * 100, 2) as persentase_kehadiran
FROM tb_karyawan k
LEFT JOIN tb_absen a ON k.id_karyawan = a.id_karyawan
WHERE k.status = 'aktif'
GROUP BY k.id_karyawan, k.nama, k.jabatan, YEAR(a.waktu), MONTH(a.waktu);

-- =====================================================
-- Stored Procedures
-- =====================================================

DELIMITER //

-- Procedure untuk mendapatkan statistik kehadiran
CREATE PROCEDURE `sp_statistik_kehadiran`(
    IN p_id_karyawan VARCHAR(20),
    IN p_bulan INT,
    IN p_tahun INT
)
BEGIN
    SELECT 
        COUNT(DISTINCT DATE(waktu)) as total_hari_hadir,
        COUNT(CASE WHEN tipe = 'masuk' THEN 1 END) as total_masuk,
        COUNT(CASE WHEN tipe = 'pulang' THEN 1 END) as total_pulang,
        MIN(CASE WHEN tipe = 'masuk' THEN TIME(waktu) END) as jam_masuk_tercepat,
        MAX(CASE WHEN tipe = 'pulang' THEN TIME(waktu) END) as jam_pulang_terakhir
    FROM tb_absen 
    WHERE id_karyawan = p_id_karyawan 
    AND MONTH(waktu) = p_bulan 
    AND YEAR(waktu) = p_tahun;
END //

-- Procedure untuk membersihkan data lama
CREATE PROCEDURE `sp_cleanup_old_data`(IN p_months_old INT)
BEGIN
    DELETE FROM tb_absen 
    WHERE created_at < DATE_SUB(NOW(), INTERVAL p_months_old MONTH);
    
    DELETE FROM tb_keterangan 
    WHERE created_at < DATE_SUB(NOW(), INTERVAL p_months_old MONTH);
END //

DELIMITER ;

-- =====================================================
-- Triggers
-- =====================================================

DELIMITER //

-- Trigger untuk log perubahan data karyawan
CREATE TRIGGER `tr_karyawan_update` 
AFTER UPDATE ON `tb_karyawan`
FOR EACH ROW
BEGIN
    INSERT INTO tb_log_aktivitas (
        tabel, 
        aksi, 
        id_record, 
        data_lama, 
        data_baru, 
        user_id, 
        waktu
    ) VALUES (
        'tb_karyawan',
        'UPDATE',
        NEW.id_karyawan,
        CONCAT('nama:', OLD.nama, '|jabatan:', OLD.jabatan),
        CONCAT('nama:', NEW.nama, '|jabatan:', NEW.jabatan),
        @current_user_id,
        NOW()
    );
END //

DELIMITER ;

-- =====================================================
-- Indexes for Performance
-- =====================================================

-- Additional indexes for better performance
CREATE INDEX `idx_absen_tanggal_tipe` ON `tb_absen` (`waktu`, `tipe`);
CREATE INDEX `idx_karyawan_nama_jabatan` ON `tb_karyawan` (`nama`, `jabatan`);
CREATE INDEX `idx_keterangan_tanggal_status` ON `tb_keterangan` (`tanggal`, `status`);

-- =====================================================
-- Sample Data for Testing
-- =====================================================

-- Insert sample attendance data
INSERT INTO `tb_absen` (`id_karyawan`, `nama`, `waktu`, `tipe`, `kegiatan`, `keterangan`, `lokasi`) VALUES
('249014764', 'SITI ZAHRA', '2025-01-23 08:15:00', 'masuk', 'Rapat Koordinasi', 'Hadir tepat waktu', '3.5979699625578974, 98.736856146506'),
('249014764', 'SITI ZAHRA', '2025-01-23 17:30:00', 'pulang', 'Rapat Koordinasi', 'Selesai rapat', '3.5979699625578974, 98.736856146506'),
('219011157', 'Natasya Olivia Ningrum', '2025-01-23 08:20:00', 'masuk', 'Kegiatan Akademik', 'Hadir', '3.5979699625578974, 98.736856146506'),
('219011157', 'Natasya Olivia Ningrum', '2025-01-23 17:25:00', 'pulang', 'Kegiatan Akademik', 'Selesai kegiatan', '3.5979699625578974, 98.736856146506');

-- Insert sample absence explanations
INSERT INTO `tb_keterangan` (`id_karyawan`, `nama`, `tanggal`, `keterangan`, `alasan`, `status`) VALUES
('249014765', 'Nurul Fikria', '2025-01-22', 'sakit', 'Demam dan flu, tidak bisa hadir', 'approved');

-- =====================================================
-- Final Setup
-- =====================================================

-- Reset AUTO_INCREMENT values
ALTER TABLE `tb_daftar` AUTO_INCREMENT = 1;
ALTER TABLE `tb_jabatan` AUTO_INCREMENT = 1;
ALTER TABLE `tb_absen` AUTO_INCREMENT = 1;
ALTER TABLE `tb_keterangan` AUTO_INCREMENT = 1;
ALTER TABLE `tb_pengaturan` AUTO_INCREMENT = 1;

-- Set proper permissions
FLUSH PRIVILEGES;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- =====================================================
-- Database Setup Complete
-- =====================================================
-- 
-- Fitur yang tersedia:
-- 1. Tabel admin (tb_daftar) dengan user default
-- 2. Tabel karyawan/beswan (tb_karyawan) dengan sample data
-- 3. Tabel absensi (tb_absen) dengan kolom lokasi dan keterangan
-- 4. Tabel keterangan (tb_keterangan) untuk izin/sakit
-- 5. Tabel pengaturan (tb_pengaturan) untuk konfigurasi sistem
-- 6. Views untuk laporan dan statistik
-- 7. Stored procedures untuk operasi kompleks
-- 8. Triggers untuk audit trail
-- 9. Indexes untuk performa optimal
-- 10. Sample data untuk testing
--
-- Login Default:
-- Admin: username = admin, password = admin123
-- Karyawan: username = siti_zahra, password = password123
-- =====================================================