# Folder Uploads - Tempat Penyimpanan Bukti Izin/Sakit

## 📁 Lokasi Folder

**Path:** `uploads/`

**Full Path:** `C:\laragon\www\KSEHADIR\uploads\`

**URL Access:** `http://localhost:8000/uploads/`

## 🎯 Fungsi Folder

Folder ini digunakan untuk menyimpan file bukti yang diupload oleh beswan saat mengajukan izin atau sakit, yaitu:
- Surat izin
- Surat sakit/keterangan dokter
- Bukti lainnya

## 📄 Format File yang Diizinkan

- **Gambar:** JPG, JPEG, PNG, GIF
- **Dokumen:** PDF
- **Ukuran Maksimal:** 2MB per file

## 🔒 Keamanan

### 1. File `.htaccess`
Sudah dibuat untuk:
- ✅ Mencegah eksekusi file PHP
- ✅ Hanya mengizinkan file gambar dan PDF
- ✅ Menonaktifkan directory listing

### 2. File `index.php`
Sudah dibuat untuk:
- ✅ Mencegah akses langsung ke folder
- ✅ Menampilkan "Access Denied" jika ada yang coba akses

## 📝 Penamaan File

Format penamaan file yang diupload:
```
{id_karyawan}_{timestamp}.{extension}

Contoh:
KSE.2024.00001_1707350400.jpg
KSE.2024.00002_1707350500.pdf
```

## 🔄 Flow Upload

### Dari Halaman Presensi Beswan:
```
1. Beswan pilih "Izin" atau "Sakit"
2. Form upload bukti muncul
3. Beswan pilih file (JPG/PNG/PDF)
4. Klik "Kirim Presensi"
5. File diupload ke: uploads/{id_karyawan}_{timestamp}.{ext}
6. Nama file disimpan ke database (tb_keterangan)
```

### Dari Halaman Data Keterangan Admin:
```
1. Admin buka "Data Keterangan"
2. Klik tombol "Lihat" di kolom Bukti
3. File dibuka di tab baru
4. URL: http://localhost:8000/uploads/{nama_file}
```

## 📊 Struktur Database

### Tabel: tb_keterangan
```sql
CREATE TABLE tb_keterangan (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_karyawan VARCHAR(50),
    nama VARCHAR(100),
    keterangan VARCHAR(50),  -- 'Izin' atau 'Sakit'
    alasan TEXT,
    waktu DATETIME,
    bukti VARCHAR(255)       -- Nama file yang diupload
);
```

## 🛠️ Troubleshooting

### File tidak bisa diupload?
1. Cek permission folder `uploads/` (harus writable)
2. Cek ukuran file (max 2MB)
3. Cek format file (hanya JPG, PNG, PDF)
4. Cek PHP settings:
   - `upload_max_filesize` di php.ini
   - `post_max_size` di php.ini

### File tidak bisa diakses?
1. Cek apakah file ada di folder `uploads/`
2. Cek nama file di database
3. Cek permission file (harus readable)

### Error 403 Forbidden?
Ini normal jika:
- Akses langsung ke folder `uploads/` (directory listing disabled)
- Coba upload file PHP (tidak diizinkan)

## 📂 Contoh Isi Folder

```
uploads/
├── .htaccess                          (keamanan)
├── index.php                          (prevent direct access)
├── KSE.2024.00001_1707350400.jpg    (bukti izin)
├── KSE.2024.00002_1707350500.pdf    (surat sakit)
├── KSE.2024.00003_1707350600.png    (keterangan dokter)
└── ...
```

## 🔗 Link Terkait

**Upload dari:**
- Halaman Presensi Beswan: `karyawan/presensi.php`

**View dari:**
- Data Keterangan Admin: `data_keterangan_modern.php`

**Proses Upload:**
```php
// Di karyawan/presensi.php
$target_dir = "../uploads/";
$bukti = $id_karyawan . '_' . time() . '.' . $file_extension;
move_uploaded_file($_FILES['bukti']['tmp_name'], $target_dir . $bukti);
```

**View File:**
```php
// Di data_keterangan_modern.php
<a href="uploads/<?php echo $row['bukti']; ?>" target="_blank">
    <i class="fas fa-file"></i> Lihat
</a>
```

## ✅ Checklist Setup

- [x] Folder `uploads/` sudah ada
- [x] File `.htaccess` sudah dibuat (keamanan)
- [x] File `index.php` sudah dibuat (prevent access)
- [x] Permission folder writable
- [x] Integrasi dengan form presensi
- [x] Integrasi dengan data keterangan admin

## 🎯 Testing

### Test Upload:
1. Login sebagai beswan
2. Klik menu "Presensi"
3. Pilih "Izin" atau "Sakit"
4. Upload file (JPG/PNG/PDF)
5. Klik "Kirim Presensi"
6. Cek folder `uploads/` → file harus ada

### Test View:
1. Login sebagai admin
2. Klik menu "Data Keterangan"
3. Cari data yang ada buktinya
4. Klik tombol "Lihat"
5. File harus terbuka di tab baru

## 📍 Lokasi Lengkap

**Windows (Laragon):**
```
C:\laragon\www\KSEHADIR\uploads\
```

**URL Browser:**
```
http://localhost:8000/uploads/{nama_file}
```

**Relative Path (dari root):**
```
./uploads/
```

**Relative Path (dari karyawan/):**
```
../uploads/
```

Folder uploads sudah siap digunakan! 🚀
