# 🔍 Testing Instructions - Debug Mode Aktif

## Status
Debug mode sudah AKTIF di halaman presensi. Sekarang akan tampil info lengkap saat submit form.

## Cara Test

### 1. Login sebagai Beswan
- Buka: `http://localhost:8000/login.php`
- Login dengan akun beswan

### 2. Buka Halaman Presensi
- Klik menu "Presensi" di sidebar
- Atau: `http://localhost:8000/karyawan/index.php?m=presensi`

### 3. Test Submit Form Izin/Sakit

#### Pilih "Izin":
1. Pilih "Izin" dari dropdown
2. Upload file (JPG/PNG/PDF, max 2MB)
3. Isi "Kegiatan": contoh "Keperluan Keluarga"
4. Isi "Keterangan": contoh "Harus mengurus dokumen penting"
5. Klik "Kirim Presensi"

#### Pilih "Sakit":
1. Pilih "Sakit" dari dropdown
2. Upload file surat sakit
3. Isi "Kegiatan": contoh "Berobat"
4. Isi "Keterangan": contoh "Demam dan flu"
5. Klik "Kirim Presensi"

### 4. Lihat Debug Info

Setelah klik "Kirim Presensi", akan muncul **kotak kuning** dengan info:

```
🔍 DEBUG INFO
Form Submitted: YES
Status Kehadiran: Izin
ID Karyawan: xxx
Nama: xxx
Tanggal Check: 08 Februari 2025
Sudah Absen: NO
Sudah Keterangan: NO
📝 Processing: Izin/Sakit
File Upload: Detected
Upload Dir: /path/to/uploads/
File Extension: jpg
Target File: /path/to/uploads/xxx_1234567890.jpg
✓ File Uploaded: xxx_1234567890.jpg
📊 Inserting to tb_keterangan...
✓ Prepare statement OK
✓ SUCCESS! Insert ID: 123
```

Lalu akan muncul **kotak hijau**:
```
✅ BERHASIL!
Data keterangan Izin berhasil disimpan!
[Kembali ke Presensi]
```

### 5. Cek Database

Setelah berhasil, cek apakah data masuk:
1. Buka: `http://localhost:8000/data_keterangan_modern.php`
2. Login sebagai super_admin jika belum
3. Lihat apakah data muncul di tabel

## Kemungkinan Error & Solusi

### Error 1: "✗ Invalid Extension"
**Penyebab**: Format file bukan JPG/PNG/PDF
**Solusi**: Upload file dengan format yang benar

### Error 2: "✗ Upload Failed"
**Penyebab**: Folder uploads tidak writable
**Solusi**: 
- Windows: Klik kanan folder `uploads` → Properties → Security → Edit → Allow Full Control
- Linux/Mac: `chmod 755 uploads`

### Error 3: "✗ Prepare Failed: Table 'xxx.tb_keterangan' doesn't exist"
**Penyebab**: Tabel tb_keterangan belum ada
**Solusi**: Jalankan query SQL:
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

### Error 4: "✗ Execute Failed: Unknown column"
**Penyebab**: Struktur tabel tidak sesuai
**Solusi**: Cek struktur tabel, pastikan ada kolom: id, id_karyawan, nama, keterangan, alasan, waktu, bukti

### Error 5: "❌ ERROR: Sudah ada absensi/keterangan hari ini"
**Penyebab**: Sudah submit untuk hari ini
**Solusi**: 
- Hapus data lama dari database
- Atau tunggu besok untuk test lagi

### Error 6: "No File: Error code 4"
**Penyebab**: File tidak dipilih
**Solusi**: Pastikan file sudah dipilih sebelum submit

## Yang Harus Dilaporkan

Jika masih gagal, screenshot dan kirim:

1. **Screenshot kotak kuning (DEBUG INFO)**
   - Semua info yang muncul di kotak kuning

2. **Screenshot kotak merah (jika ada error)**
   - Pesan error yang muncul

3. **Screenshot browser console** (F12 → Console)
   - Apakah ada error JavaScript?

4. **Info tambahan:**
   - Status yang dipilih (Hadir/Izin/Sakit)
   - Ukuran file yang diupload
   - Format file yang diupload

## Setelah Berhasil

Jika muncul "✅ BERHASIL!", berarti:
- ✓ Data sudah masuk ke database
- ✓ File sudah terupload (jika Izin/Sakit)
- ✓ Bisa cek di dashboard admin

Klik "Kembali ke Presensi" untuk:
- Lihat status berubah
- Form tidak muncul lagi (karena sudah absen hari ini)

## Notes

- Debug mode ini **sementara** untuk troubleshooting
- Setelah masalah selesai, akan saya kembalikan ke mode normal (dengan redirect & SweetAlert)
- Untuk sekarang, **tidak ada redirect otomatis** agar bisa lihat debug info

---

**Silakan test dan screenshot hasilnya!** 📸
