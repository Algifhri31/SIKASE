# Dashboard Beswan KSE v2.0

Sistem Informasi Kehadiran Beasiswa KSE yang telah diperbaiki dan dimodernisasi.

## 🚀 Fitur Utama

### ✅ Dashboard Modern
- **Tampilan Responsif**: Mendukung desktop, tablet, dan mobile
- **Statistik Real-time**: Menampilkan data kehadiran bulan ini
- **Status Absensi**: Informasi status absen hari ini
- **Riwayat Terbaru**: 5 riwayat absensi terakhir

### ✅ Sistem Presensi
- **GPS Location**: Otomatis mendeteksi lokasi saat absen
- **Absen Masuk/Pulang**: Sistem absensi lengkap
- **Validasi Waktu**: Pengecekan jam kerja
- **Keterangan**: Input kegiatan dan keterangan

### ✅ Manajemen Profil
- **Upload Foto**: Fitur upload foto profil
- **Edit Data**: Update informasi pribadi
- **Validasi Form**: Validasi input yang ketat

### ✅ Riwayat Absensi
- **Filter Bulan**: Filter data berdasarkan bulan
- **Pagination**: Navigasi data yang mudah
- **Export Data**: Export ke Excel/CSV
- **Statistik**: Ringkasan kehadiran

## 📁 Struktur File

```
karyawan/
├── assets/
│   ├── css/
│   │   └── dashboard.css          # Stylesheet utama
│   ├── js/
│   │   └── dashboard.js           # JavaScript functions
│   └── img/
│       └── uploads/               # Folder upload foto
├── config/
│   └── dashboard_config.php       # Konfigurasi dashboard
├── templates/
│   ├── header.php                 # Template header
│   └── footer.php                 # Template footer
├── index.php                      # Router utama
├── dashboard_final.php            # Halaman dashboard
├── presensi.php                   # Halaman presensi
├── profil_modern.php              # Halaman profil
├── edit_modern.php                # Halaman edit profil
├── riwayat_simple.php             # Halaman riwayat
├── logout.php                     # Proses logout
├── sesi_karyawan.php              # Validasi session
└── helper_tanggal_new.php         # Helper tanggal Indonesia
```

## 🛠️ Teknologi yang Digunakan

- **Frontend**: Bootstrap 5, Font Awesome 6, Google Fonts (Inter)
- **Backend**: PHP 7.4+, MySQL
- **JavaScript**: Vanilla JS, SweetAlert
- **CSS**: Custom CSS dengan CSS Grid dan Flexbox

## 📱 Fitur Responsif

### Desktop (≥992px)
- Sidebar tetap terlihat
- Layout 2 kolom
- Menu lengkap

### Tablet (768px - 991px)
- Sidebar dapat di-toggle
- Layout adaptif
- Navigasi optimized

### Mobile (≤767px)
- Sidebar tersembunyi (hamburger menu)
- Layout 1 kolom
- Touch-friendly interface

## 🔧 Konfigurasi

### Database
Pastikan tabel berikut tersedia:
- `tb_karyawan` - Data karyawan/beswan
- `tb_absen` - Data absensi
- `tb_absensi` - Data kehadiran (opsional)

### Session
Session yang diperlukan:
- `$_SESSION['idsi']` - ID karyawan
- `$_SESSION['namasi']` - Nama karyawan
- `$_SESSION['level']` - Level akses ('beswan')

### File Upload
Pastikan folder `assets/img/uploads/` memiliki permission write (755).

## 🎨 Customization

### Warna Tema
Edit file `assets/css/dashboard.css`:
```css
:root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --primary-color: #667eea;
    --secondary-color: #764ba2;
}
```

### Menu Sidebar
Edit file `config/dashboard_config.php`:
```php
$dashboard_menu = [
    [
        'id' => 'menu_id',
        'title' => 'Menu Title',
        'icon' => 'fas fa-icon',
        'url' => '?m=module',
        'active' => ['module']
    ]
];
```

## 🚀 Instalasi

1. **Upload Files**: Upload semua file ke folder `karyawan/`
2. **Set Permissions**: 
   ```bash
   chmod 755 assets/img/uploads/
   chmod 644 *.php
   ```
3. **Database**: Pastikan koneksi database di `../koneksi.php`
4. **Test**: Akses melalui browser

## 🔒 Keamanan

- ✅ Session validation pada setiap halaman
- ✅ SQL injection protection dengan prepared statements
- ✅ XSS protection dengan htmlspecialchars()
- ✅ File upload validation
- ✅ CSRF protection (recommended)

## 📊 Performance

- ✅ Optimized CSS dan JS
- ✅ Lazy loading untuk gambar
- ✅ Minified assets
- ✅ Caching headers
- ✅ Responsive images

## 🐛 Troubleshooting

### GPS Tidak Berfungsi
1. Pastikan HTTPS aktif
2. Izinkan location access di browser
3. Aktifkan GPS di device

### Upload Foto Gagal
1. Cek permission folder uploads/
2. Cek ukuran file (max 2MB)
3. Cek format file (JPG, PNG, GIF)

### Session Expired
1. Cek konfigurasi session di PHP
2. Pastikan cookies enabled
3. Cek session timeout

## 📞 Support

Untuk bantuan teknis atau bug report, silakan hubungi developer atau buat issue di repository.

## 📝 Changelog

### v2.0 (Current)
- ✅ Complete UI/UX redesign
- ✅ Mobile responsive
- ✅ Modern dashboard with statistics
- ✅ GPS integration for attendance
- ✅ Photo upload functionality
- ✅ Improved security
- ✅ Better code organization

### v1.0 (Previous)
- Basic attendance system
- Simple UI
- Desktop only

---

**© 2025 Beswan KSE Dashboard v2.0**