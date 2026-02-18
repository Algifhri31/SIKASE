# 🔐 Panduan Login - Sistem Kehadiran Beswan KSE

## ❌ **Masalah: Username Admin Salah**

### 🎯 **Kredensial Login yang BENAR:**

#### **Admin:**
- **Username:** `admin`
- **Password:** `admin123`

#### **Karyawan Sample:**
- **Username:** `siti_zahra`
- **Password:** `password123`

## 🔧 **Cara Memperbaiki:**

### **Opsi 1: Fix Otomatis (Recommended)**
1. Buka: `fix_admin_credentials.php`
2. File akan otomatis memperbaiki kredensial admin
3. Login dengan `admin` / `admin123`

### **Opsi 2: Check Database**
1. Buka: `check_credentials.php`
2. Lihat kredensial yang ada di database
3. Jika salah, gunakan "Fix Admin Credentials"

### **Opsi 3: Setup Ulang**
1. Buka: `setup_quick.php`
2. Tunggu setup selesai
3. Login dengan kredensial default

## 🔍 **Debug Steps:**

### **1. Cek Database Connection:**
```
Buka: debug_check.php
```

### **2. Cek Kredensial Database:**
```
Buka: check_credentials.php
```

### **3. Fix Admin User:**
```
Buka: fix_admin_credentials.php
```

### **4. Login:**
```
Buka: login_simple.php
Username: admin
Password: admin123
```

## 📱 **Setelah Login Berhasil:**

### **Admin Dashboard:**
- URL: `admin_dashboard_modern.php`
- Fitur: Kelola beswan, absensi, laporan

### **Karyawan Dashboard:**
- URL: `karyawan/index.php`
- Fitur: Absensi GPS, riwayat pribadi

## ⚠️ **Troubleshooting:**

### **Error: "Username tidak ditemukan"**
- Jalankan `fix_admin_credentials.php`
- Pastikan tabel `tb_daftar` ada

### **Error: "Password salah"**
- Pastikan password: `admin123` (bukan `admin`)
- Cek di `check_credentials.php`

### **Halaman kosong/error**
- Jalankan `debug_check.php`
- Pastikan database `karyawansi` ada
- Cek koneksi MySQL

### **Database tidak ada**
- Jalankan `setup_quick.php`
- Atau import `karyawansi.sql` manual

## 🚀 **Quick Links:**

- 🔍 **Check Credentials:** `check_credentials.php`
- 🔧 **Fix Admin:** `fix_admin_credentials.php`
- ⚡ **Quick Setup:** `setup_quick.php`
- 🚀 **Login:** `login_simple.php`
- 🐛 **Debug:** `debug_check.php`

## 📞 **Masih Bermasalah?**

1. Pastikan MySQL berjalan
2. Cek file `koneksi.php` (database: `karyawansi`)
3. Jalankan semua file debug di atas
4. Import `karyawansi.sql` jika perlu

---

**© 2025 KSE UINSU - Sistem Kehadiran Beswan v2.0**