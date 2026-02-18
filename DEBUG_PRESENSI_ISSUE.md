# Debug: Form Tersubmit Tapi Data Tidak Masuk

## Problem
- Form presensi tersubmit (refresh)
- Tapi data tidak masuk ke database
- Status tidak berubah
- Data tidak muncul di dashboard admin

## Solution: Error Logging & Debugging

Saya sudah menambahkan **error logging lengkap** di `karyawan/presensi.php` untuk melacak setiap step proses.

## Cara Debug

### Step 1: Test Database Connection
Buka: `http://localhost:8000/test_insert_keterangan.php`

**Expected Result:**
- ✓ Prepare statement successful
- ✓ Insert successful! Insert ID: xxx
- Data muncul di tabel
- Test data deleted

**Jika gagal:**
- Cek error message yang muncul
- Cek struktur tabel (apakah kolom sesuai?)

### Step 2: Submit Form Presensi
1. Login sebagai beswan
2. Buka: `http://localhost:8000/karyawan/index.php?m=presensi`
3. **Buka Browser Console** (F12 → Console tab)
4. Pilih status (Izin/Sakit/Hadir)
5. Isi form
6. Klik "Kirim Presensi"
7. **Lihat di Console** - ada log:
   - `=== VALIDATE FORM START ===`
   - `Status: Izin` (atau Sakit/Hadir)
   - `✓ Validation passed, submitting form...`

### Step 3: Check Error Log
Buka: `http://localhost:8000/view_error_log.php`

**Expected Log Entries:**
```
=== PRESENSI FORM SUBMITTED ===
POST data: Array ( [simpan] => ... [status_kehadiran] => Izin ... )
FILES data: Array ( [bukti] => ... )
Status kehadiran: Izin
Processing Izin/Sakit...
File upload detected
Attempting to move file to: /path/to/uploads/TEST123_1234567890.jpg
File uploaded successfully: TEST123_1234567890.jpg
Inserting to tb_keterangan...
Data: id=TEST123, nama=Test User, ket=Izin, alasan=..., waktu=..., bukti=...
SUCCESS: Data inserted to tb_keterangan
Setting success session and redirecting...
```

**Jika ada ERROR:**
- `ERROR: Sudah ada absensi/keterangan hari ini` → Sudah absen hari ini
- `ERROR: Invalid file extension` → Format file salah
- `ERROR: Failed to move uploaded file` → Folder uploads tidak writable
- `ERROR: Insert failed` → Ada masalah dengan query/database
- `ERROR: Prepare statement failed` → Ada masalah dengan struktur tabel

### Step 4: Check Database
Buka: `http://localhost:8000/check_keterangan_table.php`

**Expected Result:**
- ✓ Table tb_keterangan exists
- Data terbaru muncul di tabel

### Step 5: Check Admin Dashboard
1. Logout dari beswan
2. Login sebagai super_admin
3. Buka: `http://localhost:8000/data_keterangan_modern.php`
4. **Expected**: Data keterangan muncul di tabel

## Common Issues & Solutions

### Issue 1: "ERROR: Failed to move uploaded file"
**Cause**: Folder `uploads/` tidak ada atau tidak writable

**Solution**:
```bash
# Buat folder uploads
mkdir uploads

# Set permission (Linux/Mac)
chmod 755 uploads

# Windows: Right-click folder → Properties → Security → Edit → Allow Full Control
```

### Issue 2: "ERROR: Insert failed - Unknown column"
**Cause**: Struktur tabel tidak sesuai

**Solution**:
1. Buka `check_keterangan_table.php`
2. Cek struktur tabel
3. Pastikan ada kolom: `id`, `id_karyawan`, `nama`, `keterangan`, `alasan`, `waktu`, `bukti`
4. Jika tidak ada, jalankan query:
```sql
CREATE TABLE IF NOT EXISTS `tb_keterangan` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `id_karyawan` varchar(20) DEFAULT NULL,
    `nama` varchar(100) NOT NULL,
    `keterangan` varchar(50) NOT NULL,
    `alasan` text NOT NULL,
    `waktu` varchar(100) NOT NULL,
    `bukti` varchar(255) DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Issue 3: "ERROR: Sudah ada absensi/keterangan hari ini"
**Cause**: Sudah ada data untuk hari ini

**Solution**:
1. Cek database: `SELECT * FROM tb_keterangan WHERE id_karyawan = 'xxx' AND waktu LIKE '08 Februari 2025%'`
2. Jika ada data lama, hapus: `DELETE FROM tb_keterangan WHERE id = xxx`
3. Atau tunggu besok untuk test lagi

### Issue 4: Form refresh tapi tidak ada log di error_log
**Cause**: Error logging tidak aktif atau file log tidak ditemukan

**Solution**:
1. Buka `view_error_log.php` untuk cek lokasi log file
2. Jika tidak ada, aktifkan error logging di `php.ini`:
```ini
error_log = C:/laragon/tmp/php_errors.log
log_errors = On
```
3. Restart web server (Apache/Nginx)

### Issue 5: Validation passed tapi form tidak submit
**Cause**: JavaScript error atau form action salah

**Solution**:
1. Buka Browser Console (F12)
2. Cek apakah ada error JavaScript
3. Cek apakah form action kosong: `<form action="" method="post">`
4. Cek apakah ada `return false` di JavaScript yang mencegah submit

### Issue 6: Data masuk ke database tapi status tidak berubah
**Cause**: Query pengecekan status tidak benar

**Solution**:
1. Cek format tanggal di database vs `getTanggalIndonesia()`
2. Pastikan format sama: "08 Februari 2025"
3. Cek query: `SELECT * FROM tb_keterangan WHERE waktu LIKE '08 Februari 2025%'`

## Testing Checklist

- [ ] Database connection OK (`test_insert_keterangan.php`)
- [ ] Table structure correct (`check_keterangan_table.php`)
- [ ] Folder `uploads/` exists and writable
- [ ] Form validation passed (Browser Console)
- [ ] Error log shows "PRESENSI FORM SUBMITTED" (`view_error_log.php`)
- [ ] Error log shows "SUCCESS: Data inserted"
- [ ] Data appears in database (`check_keterangan_table.php`)
- [ ] Data appears in admin dashboard (`data_keterangan_modern.php`)
- [ ] Status updated in beswan dashboard
- [ ] SweetAlert notification appears

## Files Modified

### Backend (PHP)
- `karyawan/presensi.php` - Added comprehensive error logging
  - Log POST data
  - Log FILES data
  - Log each step of processing
  - Log success/error messages
  - Log database operations

### Debug Files Created
- `view_error_log.php` - View PHP error log
- `test_insert_keterangan.php` - Test database insert
- `check_keterangan_table.php` - Check table structure & data
- `debug_presensi.php` - Debug form submission

## Next Steps

1. **Run all tests** in order (Step 1-5)
2. **Check error log** after each form submission
3. **Report findings**:
   - Which step failed?
   - What error message appeared?
   - What's in the error log?

## Expected Behavior After Fix

### For Izin/Sakit:
1. Select "Izin" or "Sakit"
2. Upload file (JPG/PNG/PDF, max 2MB)
3. Fill form
4. Click "Kirim Presensi"
5. **See**: Loading indicator
6. **See**: SweetAlert success message
7. **Redirect**: Back to presensi page
8. **See**: "Keterangan Terkirim! Anda sudah mengirim keterangan Izin"
9. **Form**: Hidden (tidak muncul lagi)
10. **Admin**: Data muncul di Data Keterangan page

### For Hadir:
1. Select "Hadir"
2. Wait for GPS location
3. Fill form
4. Click "Kirim Presensi"
5. **See**: Loading indicator
6. **See**: SweetAlert success message
7. **Redirect**: Back to presensi page
8. **See**: Form for "Absen Pulang" (if masuk) or "Absensi Lengkap!" (if pulang)
9. **Admin**: Data muncul di Data Absensi page

## Contact Points for Debugging

If issue persists, provide:
1. Screenshot of error log (`view_error_log.php`)
2. Screenshot of browser console (F12)
3. Screenshot of database check (`check_keterangan_table.php`)
4. Screenshot of form when submitting
5. What status was selected (Hadir/Izin/Sakit)

This will help identify the exact point of failure! 🔍
