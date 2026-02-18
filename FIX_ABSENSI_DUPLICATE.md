# Fix: Prevent Duplicate Absensi/Keterangan

## Problem
Beswan bisa kirim presensi berkali-kali dalam satu hari karena sistem hanya mengecek `tb_absen`, tidak mengecek `tb_keterangan`. Jadi kalau sudah kirim Izin/Sakit (masuk ke `tb_keterangan`), masih bisa absen Hadir lagi.

## Solution
Menambahkan pengecekan ganda:
1. Cek `tb_absen` untuk absensi Hadir (masuk/pulang)
2. Cek `tb_keterangan` untuk keterangan Izin/Sakit

Jika salah satu sudah ada untuk hari ini, maka tidak bisa absen lagi.

## Changes Made

### 1. File: `karyawan/presensi.php`

#### A. Pengecekan Awal (Load Page)
```php
// Cek absensi di tb_absen
$sqls = "SELECT * FROM tb_absen WHERE id_karyawan = '$id' AND waktu LIKE '".getTanggalIndonesia()."%'";

// Cek keterangan di tb_keterangan
$sql_ket = "SELECT * FROM tb_keterangan WHERE id_karyawan = '$id' AND waktu LIKE '".getTanggalIndonesia()."%'";

$sudah_absen = !empty($rz); 
$sudah_keterangan = !empty($rz_ket);
```

#### B. Pengecekan Saat Submit Form
```php
if (isset($_POST['simpan'])) {
    // Double check sebelum insert
    $check_absen = mysqli_query($koneksi, "SELECT * FROM tb_absen WHERE id_karyawan = '$id' AND waktu LIKE '".getTanggalIndonesia()."%'");
    $check_ket = mysqli_query($koneksi, "SELECT * FROM tb_keterangan WHERE id_karyawan = '$id' AND waktu LIKE '".getTanggalIndonesia()."%'");
    
    if (mysqli_num_rows($check_absen) > 0 || mysqli_num_rows($check_ket) > 0) {
        // Tampilkan error alert
        $_SESSION['alert_success'] = false;
        $_SESSION['alert_message'] = "Sudah Absen Hari Ini!";
        $_SESSION['alert_text'] = "Anda sudah melakukan absensi atau mengirim keterangan untuk hari ini.";
        header("Location: index.php?m=presensi");
        exit();
    }
    
    // Lanjut proses insert...
}
```

#### C. Update Status Display
```php
// Status badge menampilkan:
// - "Izin" atau "Sakit" jika sudah kirim keterangan
// - "Lengkap" jika sudah absen masuk & pulang
// - "Masuk" jika baru absen masuk
// - "Belum" jika belum ada absensi/keterangan

if ($sudah_keterangan) {
    echo $tipe_keterangan; // Izin/Sakit
} elseif ($tipe_absen == 'pulang') {
    echo 'Lengkap';
} elseif ($tipe_absen == 'masuk') {
    echo 'Masuk';
} else {
    echo 'Belum';
}
```

#### D. Kondisi Tampilkan Form
```php
// Form hanya muncul jika:
// - Belum absen pulang DAN
// - Belum kirim keterangan (Izin/Sakit)

<?php if ($tipe_absen != 'pulang' && !$sudah_keterangan) : ?>
    <!-- Form Presensi -->
<?php else: ?>
    <!-- Pesan sudah absen/kirim keterangan -->
<?php endif; ?>
```

### 2. File: `karyawan/awal.php`

Perubahan yang sama diterapkan di halaman dashboard beswan:

#### A. Pengecekan Awal
```php
// Cek absensi
$sqls = "SELECT * FROM tb_absen WHERE id_karyawan = '$id' AND waktu LIKE '".getTanggalIndonesia()."%'";
$querys = mysqli_query($koneksi, $sqls);
$rz = mysqli_fetch_array($querys);

// Cek keterangan
$sql_ket = "SELECT * FROM tb_keterangan WHERE id_karyawan = '$id' AND waktu LIKE '".getTanggalIndonesia()."%'";
$query_ket = mysqli_query($koneksi, $sql_ket);
$rz_ket = mysqli_fetch_array($query_ket);

$sudah_absen = !empty($rz);
$sudah_keterangan = !empty($rz_ket);
```

#### B. Pengecekan Saat Submit
```php
if (isset($_POST['simpan'])) {
    $check_absen = mysqli_query($koneksi, "SELECT * FROM tb_absen WHERE id_karyawan = '$id' AND waktu LIKE '".getTanggalIndonesia()."%'");
    $check_ket = mysqli_query($koneksi, "SELECT * FROM tb_keterangan WHERE id_karyawan = '$id' AND waktu LIKE '".getTanggalIndonesia()."%'");
    
    if (mysqli_num_rows($check_absen) > 0 || mysqli_num_rows($check_ket) > 0) {
        // SweetAlert warning
        swal({
            icon: 'warning',
            title: 'Sudah Absen!',
            text: 'Anda sudah melakukan absensi atau mengirim keterangan untuk hari ini.'
        });
    } else {
        // Lanjut proses insert...
    }
}
```

#### C. Update Status Display
```php
<h6>Status Hari Ini</h6>
<p>
<?php 
if ($sudah_keterangan) {
    echo $tipe_keterangan; // Izin/Sakit
} elseif ($tipe_absen == 'pulang') {
    echo 'Sudah Absen';
} else {
    echo 'Belum Absen';
}
?>
</p>
```

#### D. Kondisi Tampilkan Form
```php
<?php if ($tipe_absen != 'pulang' && !$sudah_keterangan) : ?>
    <!-- Form absen -->
    <!-- Tombol "Klik jika tidak hadir" -->
<?php endif; ?>
```

## Testing Scenarios

### Scenario 1: Beswan Kirim Izin, Lalu Coba Absen Hadir
1. Login sebagai beswan
2. Pilih "Izin" → Upload bukti → Submit
3. **Expected**: SweetAlert sukses, data masuk ke `tb_keterangan`
4. Refresh halaman atau kembali ke Presensi
5. **Expected**: Form tidak muncul, ada pesan "Keterangan Terkirim! Anda sudah mengirim keterangan Izin untuk hari ini"
6. Coba akses halaman dashboard (awal.php)
7. **Expected**: Status menampilkan "Izin", form tidak muncul

### Scenario 2: Beswan Absen Masuk, Lalu Coba Kirim Izin
1. Login sebagai beswan
2. Pilih "Hadir" → Izinkan GPS → Submit
3. **Expected**: SweetAlert sukses, data masuk ke `tb_absen`
4. Refresh halaman
5. **Expected**: Form muncul untuk absen pulang (bukan form baru)
6. Coba pilih "Izin" atau "Sakit"
7. **Expected**: Saat submit, muncul alert "Sudah Absen Hari Ini!"

### Scenario 3: Beswan Absen Masuk & Pulang
1. Login sebagai beswan
2. Absen Masuk (Hadir) → Submit
3. Refresh halaman
4. Absen Pulang (Hadir) → Submit
5. **Expected**: SweetAlert sukses
6. Refresh halaman
7. **Expected**: Form tidak muncul, ada pesan "Absensi Lengkap!"
8. Status menampilkan "Lengkap" atau "Sudah Absen"

### Scenario 4: Beswan Kirim Sakit
1. Login sebagai beswan
2. Pilih "Sakit" → Upload bukti → Submit
3. **Expected**: SweetAlert sukses, data masuk ke `tb_keterangan`
4. Refresh halaman
5. **Expected**: Form tidak muncul, pesan "Keterangan Terkirim! Anda sudah mengirim keterangan Sakit untuk hari ini"
6. Status menampilkan "Sakit"

### Scenario 5: Beswan Coba Submit Dua Kali (Double Submit)
1. Login sebagai beswan
2. Pilih "Hadir" → Submit
3. Langsung klik tombol submit lagi sebelum redirect
4. **Expected**: Karena ada pengecekan di backend, akan muncul alert "Sudah Absen Hari Ini!"

## Database Queries

### Check Absensi Hari Ini
```sql
-- Cek di tb_absen
SELECT * FROM tb_absen 
WHERE id_karyawan = '123' 
AND waktu LIKE '01 Januari 2025%';

-- Cek di tb_keterangan
SELECT * FROM tb_keterangan 
WHERE id_karyawan = '123' 
AND waktu LIKE '01 Januari 2025%';
```

### Verify Data
```sql
-- Lihat semua absensi hari ini
SELECT 'absen' as source, id_karyawan, nama, waktu, tipe as status 
FROM tb_absen 
WHERE waktu LIKE '01 Januari 2025%'
UNION ALL
SELECT 'keterangan' as source, id_karyawan, nama, waktu, keterangan as status 
FROM tb_keterangan 
WHERE waktu LIKE '01 Januari 2025%'
ORDER BY waktu DESC;
```

## Benefits

1. **Prevent Duplicate**: Beswan tidak bisa absen/kirim keterangan lebih dari 1x per hari
2. **Data Integrity**: Data lebih akurat, tidak ada duplikasi
3. **Better UX**: User langsung tahu kalau sudah absen, tidak perlu coba-coba
4. **Clear Status**: Status ditampilkan dengan jelas (Hadir/Izin/Sakit/Lengkap)
5. **Admin View**: Admin bisa lihat semua data di 2 tempat:
   - `admin/data_absensi_modern.php` untuk Hadir (tb_absen)
   - `data_keterangan_modern.php` untuk Izin/Sakit (tb_keterangan)

## Notes

- Format tanggal menggunakan `getTanggalIndonesia()` yang return format: "01 Januari 2025"
- Pengecekan menggunakan `LIKE` dengan wildcard untuk match tanggal saja (ignore waktu)
- Session alerts digunakan untuk mencegah alert muncul berulang saat refresh
- SweetAlert digunakan untuk notifikasi yang lebih user-friendly

## Troubleshooting

### Problem: Masih bisa absen berkali-kali
**Check**:
1. Pastikan query pengecekan menggunakan format tanggal yang sama
2. Cek apakah `getTanggalIndonesia()` return format yang benar
3. Cek di database apakah kolom `waktu` berisi format yang sama

### Problem: Status tidak update
**Check**:
1. Refresh halaman setelah submit
2. Cek apakah query SELECT mengambil data terbaru
3. Cek apakah variable `$sudah_keterangan` di-set dengan benar

### Problem: Form masih muncul padahal sudah absen
**Check**:
1. Cek kondisi `if ($tipe_absen != 'pulang' && !$sudah_keterangan)`
2. Pastikan variable `$sudah_keterangan` bernilai `true` jika ada data
3. Cek query `$sql_ket` apakah return data

## Conclusion

Fix ini memastikan bahwa beswan hanya bisa melakukan 1 aksi per hari:
- **Hadir**: Absen masuk → Absen pulang (2 kali, tapi tipe berbeda)
- **Izin/Sakit**: Kirim keterangan 1x (tidak bisa absen Hadir lagi)

Sistem sekarang lebih robust dan mencegah data duplikat! ✅
