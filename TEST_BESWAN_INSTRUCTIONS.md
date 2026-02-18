# Instruksi Testing Data Beswan

## Server Sudah Berjalan! ✅

Server PHP sudah berjalan di: **http://localhost:8000**

## Langkah-langkah Testing:

### 1. Login sebagai Super Admin
1. Buka browser dan akses: **http://localhost:8000/login.php**
2. Login dengan akun super admin Anda
3. Setelah login, Anda akan masuk ke dashboard admin

### 2. Akses Halaman Data Beswan

**URL:** **http://localhost:8000/admin/data_beswan_modern.php**

Fitur:
- DataTables dengan sorting dan pagination
- Search/pencarian data
- Modern UI dengan SweetAlert
- Tambah, Edit, Hapus dengan konfirmasi

### 3. Test Fitur TAMBAH BESWAN

**Test Case 1: Tambah Beswan Baru (Sukses)**
1. Klik tombol "Tambah Beswan" (hijau/ungu)
2. Isi form dengan data:
   - ID Beswan: KSE.2024.00001 (atau ID baru yang belum ada)
   - Username: testuser
   - Password: test123
   - Nama: Test Beswan
   - Tempat, Tgl Lahir: Medan, 01 Januari 2000
   - Jenis Kelamin: Pilih salah satu
   - Agama: Pilih salah satu
   - Alamat: Jl. Test No. 123
   - No. Telepon: 081234567890
   - Jabatan: Mahasiswa S1
3. Klik "Simpan Data"
4. **Expected Result:** 
   - SweetAlert muncul dengan icon ✅ success
   - Pesan: "Data beswan berhasil ditambahkan!"
   - Data muncul di tabel

**Test Case 2: Tambah Beswan dengan ID Duplikat (Error)**
1. Klik tombol "Tambah Beswan" lagi
2. Isi form dengan ID yang SAMA seperti sebelumnya (KSE.2024.00001)
3. Isi field lainnya dengan data berbeda
4. Klik "Simpan Data"
5. **Expected Result:**
   - SweetAlert muncul dengan icon ❌ error
   - Pesan: "ID Beswan sudah digunakan!"
   - Data TIDAK ditambahkan ke tabel

### 4. Test Fitur EDIT BESWAN

1. Cari data beswan yang baru ditambahkan di tabel
2. Klik tombol Edit (icon pensil/kuning)
3. Modal akan muncul dengan data yang sudah terisi
4. Ubah beberapa field (misalnya nama atau no telepon)
5. Klik "Update Data"
6. **Expected Result:**
   - SweetAlert muncul dengan icon ✅ success
   - Pesan: "Data beswan berhasil diupdate!"
   - Data di tabel berubah sesuai edit

### 5. Test Fitur HAPUS BESWAN

**Test Case 1: Konfirmasi Hapus**
1. Cari data beswan di tabel
2. Klik tombol Hapus (icon trash/merah)
3. **Expected Result:**
   - SweetAlert konfirmasi muncul dengan:
     - Title: "Hapus Data Beswan?"
     - Nama beswan yang akan dihapus
     - Peringatan: "Data absensi terkait juga akan dihapus!"
     - Tombol: "Ya, Hapus!" (merah) dan "Batal" (abu-abu)

**Test Case 2: Batal Hapus**
1. Klik tombol "Batal"
2. **Expected Result:**
   - Modal konfirmasi tertutup
   - Data TIDAK dihapus
   - Tetap ada di tabel

**Test Case 3: Konfirmasi Hapus**
1. Klik tombol Hapus lagi
2. Klik "Ya, Hapus!"
3. **Expected Result:**
   - Loading indicator muncul: "Menghapus Data..."
   - SweetAlert sukses muncul: "Data beswan berhasil dihapus!"
   - Data HILANG dari tabel
   - Data absensi terkait juga terhapus dari database

### 6. Checklist Testing ✅

Centang setiap test yang berhasil:

- [ ] Server berjalan di localhost:8000
- [ ] Bisa login sebagai super admin
- [ ] Bisa akses halaman data beswan
- [ ] Tambah beswan baru → SweetAlert sukses muncul
- [ ] Tambah beswan dengan ID duplikat → SweetAlert error muncul
- [ ] Edit beswan → SweetAlert sukses muncul
- [ ] Klik hapus → Konfirmasi SweetAlert muncul
- [ ] Klik batal hapus → Data tidak terhapus
- [ ] Konfirmasi hapus → Loading muncul → Data terhapus
- [ ] Semua notifikasi menggunakan SweetAlert (bukan alert biasa)

### 7. Screenshot/Video (Optional)
Jika ada masalah, ambil screenshot atau video untuk debugging.

---

## Troubleshooting

**Jika SweetAlert tidak muncul:**
- Buka Developer Console (F12)
- Cek apakah ada error JavaScript
- Pastikan koneksi internet aktif (untuk load SweetAlert CDN)

**Jika fungsi tidak bekerja:**
- Cek Developer Console untuk error
- Cek Network tab untuk melihat request/response
- Pastikan session admin masih aktif

**Jika database error:**
- Pastikan koneksi database di koneksi.php benar
- Cek apakah tabel tb_karyawan dan tb_absensi ada

---

## Selamat Testing! 🚀
