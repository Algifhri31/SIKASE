# 🗄️ Setup Database - Sistem Kehadiran Beswan KSE

## 📋 Ringkasan

Database telah disatukan dalam satu file `karyawansi.sql` untuk memudahkan setup dan menghindari kebingungan.

## 🚀 Cara Setup Database

### 1. **Import Database Baru**

```sql
-- 1. Drop database lama (jika ada)
DROP DATABASE IF EXISTS karyawansi;

-- 2. Import file karyawansi.sql
-- Gunakan phpMyAdmin atau MySQL client untuk import
```

### 2. **Via phpMyAdmin**
1. Buka phpMyAdmin
2. Klik "Import" 
3. Pilih file `karyawansi.sql`
4. Klik "Go"

### 3. **Via Command Line**
```bash
mysql -u root -p < karyawansi.sql
```

## 📊 Struktur Database Baru

### Tabel Utama:
- **`tb_daftar`** - Data admin/pengguna sistem
- **`tb_karyawan`** - Data beswan/mahasiswa
- **`tb_absen`** - Data kehadiran dengan lokasi GPS
- **`tb_keterangan`** - Data izin/sakit
- **`tb_jabatan`** - Master data jabatan
- **`tb_pengaturan`** - Konfigurasi sistem

### Fitur Database:
- ✅ **Foreign Key Constraints** - Relasi antar tabel
- ✅ **Indexes** - Performa query optimal
- ✅ **Views** - Laporan siap pakai
- ✅ **Stored Procedures** - Operasi kompleks
- ✅ **Triggers** - Audit trail otomatis
- ✅ **Sample Data** - Data testing

## 🔑 Login Default

### Admin:
- **Username:** `admin`
- **Password:** `admin123`
- **URL:** `admin_dashboard_modern.php`

### Karyawan/Beswan:
- **Username:** `siti_zahra`
- **Password:** `password123`
- **URL:** `karyawan/index.php`

## 🔧 Konfigurasi Koneksi

File `koneksi.php` sudah dikonfigurasi untuk:
- **Database:** `karyawansi`
- **Host:** `localhost`
- **User:** `root`
- **Password:** `` (kosong untuk development)

## ✅ Verifikasi Setup

1. Jalankan `update_database_structure.php`
2. Cek status database dan tabel
3. Login ke dashboard admin
4. Test fitur absensi

## 🗑️ File yang Dihapus

File database berikut sudah dihapus untuk menghindari kebingungan:
- ❌ `setup_database.sql`
- ❌ `u524719089_karyawansi.sql`

## 📱 Fitur Terbaru

### Dashboard Admin:
- 📊 Statistik real-time
- 👥 Manajemen data beswan
- 📋 Laporan kehadiran
- 📤 Export Excel

### Dashboard Karyawan:
- 🕐 Absensi dengan GPS
- 📈 Statistik pribadi
- 📅 Riwayat kehadiran
- 📱 Responsive design

### Fitur Rekap:
- 📅 **Filter Harian** - Lihat absensi tanggal tertentu
- 📆 **Filter Bulanan** - Lihat semua hari dalam bulan
- 📊 **Statistik Lengkap** - Persentase kehadiran
- 📤 **Export Excel** - Download rekap data

## 🔄 Update dari Versi Lama

Jika Anda menggunakan database lama:

1. **Backup data penting** (jika ada)
2. **Drop database lama**
3. **Import karyawansi.sql**
4. **Tambah data karyawan** via admin panel

## 🆘 Troubleshooting

### Error: Table doesn't exist
```sql
-- Import ulang karyawansi.sql
SOURCE karyawansi.sql;
```

### Error: Access denied
```sql
-- Buat user database
CREATE USER 'kse_user'@'localhost' IDENTIFIED BY 'kse_password';
GRANT ALL PRIVILEGES ON karyawansi.* TO 'kse_user'@'localhost';
FLUSH PRIVILEGES;
```

### Error: Foreign key constraint
```sql
-- Disable foreign key checks sementara
SET FOREIGN_KEY_CHECKS = 0;
-- Import database
-- Enable kembali
SET FOREIGN_KEY_CHECKS = 1;
```

## 📞 Support

Jika ada masalah dengan setup database:

1. Cek file `update_database_structure.php`
2. Lihat error log di browser
3. Pastikan MySQL service berjalan
4. Cek permission user database

## 🎯 Next Steps

Setelah database berhasil di-setup:

1. ✅ Login sebagai admin
2. ✅ Tambah data beswan
3. ✅ Test fitur absensi
4. ✅ Coba export laporan
5. ✅ Kustomisasi sesuai kebutuhan

---

**© 2025 KSE UINSU - Sistem Informasi Kehadiran Beswan v2.0**