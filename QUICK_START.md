# 🚀 Quick Start - Sistem Kehadiran Beswan KSE

## ⚡ Setup Cepat (5 Menit)

### 1. **Jalankan Setup Otomatis**
Buka di browser: `setup_quick.php`

File ini akan:
- ✅ Membuat database `karyawansi`
- ✅ Membuat tabel yang diperlukan
- ✅ Membuat user admin default
- ✅ Menambahkan sample data

### 2. **Login ke Sistem**
Buka: `login_simple.php` atau `index.php`

**Kredensial Default:**
- **Admin**: `admin` / `admin123`
- **Karyawan**: `siti_zahra` / `password123`

### 3. **Akses Dashboard**
- **Admin**: `admin_dashboard_modern.php`
- **Karyawan**: `karyawan/index.php`

## 🔧 Jika Ada Masalah

### Halaman Kosong?
1. Jalankan `debug_check.php` - Cek status sistem
2. Jalankan `setup_quick.php` - Setup otomatis
3. Cek `update_database_structure.php` - Status database

### Error Database?
1. Pastikan MySQL berjalan
2. Cek kredensial di `koneksi.php`
3. Database name: `karyawansi`

### Error Login?
1. Pastikan tabel `tb_daftar` ada
2. Cek user admin di database
3. Username: `admin`, Password: `admin123`

## 📱 Fitur Utama

### Admin Dashboard:
- 👥 Kelola data beswan
- 📊 Lihat statistik kehadiran
- 📅 Filter absensi harian/bulanan
- 📤 Export Excel
- 📋 Generate laporan

### Karyawan Dashboard:
- 🕐 Absensi dengan GPS
- 📈 Lihat statistik pribadi
- 📅 Riwayat kehadiran
- 📱 Design responsive

## 🎯 File Penting

- `index.php` - Halaman utama
- `login_simple.php` - Login
- `admin_dashboard_modern.php` - Dashboard admin
- `karyawan/index.php` - Dashboard karyawan
- `koneksi.php` - Konfigurasi database
- `setup_quick.php` - Setup otomatis
- `debug_check.php` - Debug sistem

## 📞 Bantuan

Jika masih ada masalah:
1. Cek error log browser (F12 → Console)
2. Pastikan PHP dan MySQL berjalan
3. Jalankan file debug yang tersedia

---

**© 2025 KSE UINSU - Sistem Kehadiran Beswan v2.0**