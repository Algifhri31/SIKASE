# Testing Keterangan (Izin/Sakit) Flow

## Status Implementasi
✅ **COMPLETED** - Semua fitur sudah diimplementasikan

## Fitur yang Sudah Ditambahkan

### 1. Logout dengan SweetAlert
- ✅ Logout confirmation di semua halaman beswan (sidebar, mobile menu, dropdown)
- ✅ Menggunakan SweetAlert untuk konfirmasi logout
- ✅ File yang diupdate: `karyawan/header.php`, `karyawan/presensi.php`

### 2. Form Presensi dengan Status Kehadiran
- ✅ Dropdown pilihan: Hadir, Izin, Sakit
- ✅ Dynamic form:
  - **Hadir**: Tampilkan field GPS lokasi
  - **Izin/Sakit**: Tampilkan field upload bukti
- ✅ Validasi file: JPG, PNG, PDF (max 2MB)
- ✅ File disimpan ke folder `uploads/` dengan format: `{id_karyawan}_{timestamp}.{ext}`

### 3. Penyimpanan Data
- ✅ **Hadir**: Data masuk ke `tb_absen`
- ✅ **Izin/Sakit**: Data masuk ke `tb_keterangan`
- ✅ Session-based alerts untuk mencegah form re-submission
- ✅ SweetAlert notification setelah submit berhasil/gagal

### 4. Tampilan di Admin Dashboard
- ✅ Data keterangan muncul di halaman `data_keterangan_modern.php`
- ✅ Fitur: Search, pagination, view detail, delete
- ✅ Badge berwarna untuk tipe keterangan (Sakit/Izin/Cuti)
- ✅ Link untuk melihat file bukti yang diupload

## Cara Testing

### Persiapan
1. Buka browser dan akses: `http://localhost:8000/check_keterangan_table.php`
2. Pastikan tabel `tb_keterangan` sudah ada dan strukturnya benar
3. Pastikan folder `uploads/` sudah ada dengan file `.htaccess` dan `index.php`

### Test Flow - Beswan Submit Izin/Sakit

#### Step 1: Login sebagai Beswan
1. Buka: `http://localhost:8000/login.php`
2. Login dengan akun beswan (contoh: username beswan, password sesuai database)

#### Step 2: Akses Halaman Presensi
1. Klik menu **Presensi** di sidebar
2. Atau akses langsung: `http://localhost:8000/karyawan/index.php?m=presensi`

#### Step 3: Submit Keterangan Izin
1. Pilih **"Izin"** dari dropdown "Status Kehadiran"
2. Field upload bukti akan muncul otomatis
3. Klik "Choose File" dan pilih file JPG/PNG/PDF (max 2MB)
4. Isi field "Kegiatan/Keperluan": contoh "Keperluan Keluarga"
5. Isi field "Keterangan/Alasan": contoh "Harus mengurus dokumen penting"
6. Klik tombol **"Kirim Presensi"**
7. **Expected Result**: 
   - SweetAlert muncul dengan pesan sukses
   - Redirect ke halaman presensi
   - Data tersimpan di database

#### Step 4: Submit Keterangan Sakit
1. Ulangi Step 2-3
2. Pilih **"Sakit"** dari dropdown
3. Upload file bukti (surat sakit/foto)
4. Isi kegiatan: "Berobat"
5. Isi alasan: "Demam dan flu"
6. Submit
7. **Expected Result**: SweetAlert sukses, data tersimpan

#### Step 5: Submit Hadir (Normal Absensi)
1. Pilih **"Hadir"** dari dropdown
2. Field GPS lokasi akan muncul otomatis
3. Izinkan akses lokasi di browser
4. Tunggu lokasi terdeteksi
5. Isi kegiatan dan keterangan
6. Submit
7. **Expected Result**: Data masuk ke `tb_absen` (bukan `tb_keterangan`)

### Test Flow - Admin Melihat Data Keterangan

#### Step 1: Logout dari Beswan
1. Klik menu Logout
2. SweetAlert konfirmasi akan muncul
3. Klik "Ya, Logout"

#### Step 2: Login sebagai Super Admin
1. Login dengan akun super_admin
2. Akses dashboard admin

#### Step 3: Buka Data Keterangan
1. Klik menu **"Data Keterangan"** di sidebar
2. Atau akses: `http://localhost:8000/data_keterangan_modern.php`

#### Step 4: Verifikasi Data
1. **Expected Result**:
   - Data Izin/Sakit yang disubmit beswan muncul di tabel
   - Badge berwarna sesuai tipe (Sakit=merah, Izin=orange)
   - Kolom "Bukti" ada tombol "Lihat" untuk file yang diupload
   - Bisa search berdasarkan nama/ID
   - Bisa delete data dengan konfirmasi SweetAlert

#### Step 5: Lihat Detail & Bukti
1. Klik tombol **mata (👁️)** untuk view detail
2. Modal akan muncul dengan info lengkap
3. Klik tombol **"Lihat Bukti"** untuk membuka file
4. File akan terbuka di tab baru

### Test Flow - Logout dengan SweetAlert

#### Test di Berbagai Tempat
1. **Sidebar Desktop**: Klik "Logout" → SweetAlert muncul
2. **Mobile Menu**: Buka menu mobile → Klik "Logout" → SweetAlert muncul
3. **Dropdown Profile**: Klik profile → Klik "Logout" → SweetAlert muncul

**Expected Result**: 
- SweetAlert dengan judul "Logout?"
- Teks "Apakah Anda yakin ingin keluar?"
- Tombol "Batal" dan "Ya, Logout"
- Jika klik "Ya, Logout" → redirect ke login page

## Troubleshooting

### Problem: Data tidak masuk ke tb_keterangan
**Solution**:
1. Cek struktur tabel: `http://localhost:8000/check_keterangan_table.php`
2. Pastikan kolom yang ada: `id`, `id_karyawan`, `nama`, `keterangan`, `alasan`, `waktu`, `bukti`
3. Cek error di browser console (F12)
4. Cek PHP error log

### Problem: File tidak terupload
**Solution**:
1. Pastikan folder `uploads/` ada dan writable (chmod 755)
2. Cek ukuran file (max 2MB)
3. Cek format file (hanya JPG, PNG, PDF)
4. Cek `php.ini`: `upload_max_filesize` dan `post_max_size`

### Problem: SweetAlert tidak muncul
**Solution**:
1. Cek apakah SweetAlert library sudah diload
2. Buka browser console (F12) untuk cek error JavaScript
3. Pastikan session alert sudah di-set di PHP

### Problem: GPS tidak terdeteksi
**Solution**:
1. Pastikan browser support geolocation
2. Klik ikon gembok di address bar → Allow location
3. Pastikan GPS aktif di device
4. Coba refresh halaman

## File yang Dimodifikasi

### Backend (PHP)
- `karyawan/presensi.php` - Form presensi dengan status selection & upload
- `karyawan/header.php` - Logout SweetAlert function
- `data_keterangan_modern.php` - Admin view data keterangan
- `uploads/.htaccess` - Security untuk folder uploads
- `uploads/index.php` - Prevent directory listing

### Frontend (JavaScript)
- SweetAlert2 integration di `karyawan/presensi.php`
- SweetAlert (v2) integration di `karyawan/header.php`
- Dynamic form toggle berdasarkan status selection
- GPS location detection
- File upload validation

## Database Schema

### tb_keterangan
```sql
CREATE TABLE `tb_keterangan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_karyawan` varchar(20) DEFAULT NULL,
  `nama` varchar(100) NOT NULL,
  `keterangan` varchar(50) NOT NULL,
  `alasan` text NOT NULL,
  `waktu` varchar(100) NOT NULL,
  `bukti` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
)
```

### tb_absen (untuk Hadir)
```sql
CREATE TABLE `tb_absen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_karyawan` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `waktu` varchar(100) NOT NULL,
  `lokasi` varchar(255) NOT NULL,
  `kegiatan` text NOT NULL,
  `keterangan` text NOT NULL,
  `tipe` enum('masuk','pulang') NOT NULL,
  PRIMARY KEY (`id`)
)
```

## Security Features

### File Upload Security
- ✅ File type validation (whitelist: JPG, PNG, PDF)
- ✅ File size limit (2MB)
- ✅ Unique filename dengan timestamp
- ✅ `.htaccess` untuk prevent direct access
- ✅ `index.php` untuk prevent directory listing

### SQL Injection Prevention
- ✅ Prepared statements dengan `mysqli_prepare()`
- ✅ Parameter binding dengan `mysqli_stmt_bind_param()`

### XSS Prevention
- ✅ `htmlspecialchars()` untuk output
- ✅ Input sanitization

## Next Steps (Optional Improvements)

1. **Email Notification**: Kirim email ke admin saat ada keterangan baru
2. **Approval System**: Admin bisa approve/reject keterangan
3. **File Preview**: Preview image/PDF di modal tanpa download
4. **Export**: Export data keterangan ke Excel/PDF
5. **Statistics**: Dashboard statistik keterangan per bulan
6. **Push Notification**: Real-time notification untuk admin

## Kesimpulan

Semua fitur sudah diimplementasikan dan siap untuk testing:
- ✅ Logout dengan SweetAlert di semua halaman beswan
- ✅ Form presensi dengan pilihan Hadir/Izin/Sakit
- ✅ Upload bukti untuk Izin/Sakit
- ✅ Data masuk ke tb_keterangan
- ✅ Admin bisa lihat data di Data Keterangan page
- ✅ SweetAlert notification untuk semua aksi

Silakan test sesuai flow di atas dan laporkan jika ada issue!
