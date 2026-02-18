<?php
session_start();
include "koneksi.php";

if (isset($_POST['submit'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $no_induk = mysqli_real_escape_string($koneksi, trim($_POST['no_induk']));
    $divisi = mysqli_real_escape_string($koneksi, $_POST['divisi']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $tmp_tgl_lahir = mysqli_real_escape_string($koneksi, $_POST['tmp_tgl_lahir']);
    $jenkel = mysqli_real_escape_string($koneksi, $_POST['jenkel']);
    $agama = mysqli_real_escape_string($koneksi, $_POST['agama']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $no_tel = mysqli_real_escape_string($koneksi, $_POST['no_tel']);

    // Validasi password
    if ($password !== $confirm_password) {
        $error = "Password dan konfirmasi password tidak cocok!";
    }
    // Validasi format No. Induk KSE
    elseif (!preg_match('/^KSE\.\d{4}\.\d{5}$/', $no_induk)) {
        $error = "Format No. Induk KSE tidak valid! Gunakan format: KSE.XXXX.XXXXX";
    }
    else {
        $password = md5($password); // Enkripsi password
        $username = strtolower($_POST['username']); // Mengambil username dari form
        // Kolom sudah VARCHAR: simpan persis sesuai input (termasuk titik)
        $id_to_insert = $no_induk;

        if (!isset($error)) {
            // Cek apakah id (setelah kemungkinan konversi) sudah ada
            $check = mysqli_prepare($koneksi, "SELECT id_karyawan FROM tb_karyawan WHERE id_karyawan = ?");
            mysqli_stmt_bind_param($check, 's', $id_to_insert);
            mysqli_stmt_execute($check);
            $check_res = mysqli_stmt_get_result($check);
            if ($check_res && mysqli_num_rows($check_res) > 0) {
                // Tampilkan nilai yang dicek supaya jelas apakah terjadi konversi / collision
                $error = "No. Induk KSE sudah terdaftar! (value checked: " . htmlspecialchars($id_to_insert) . ")";
            }
            mysqli_stmt_close($check);
        }

        if (!isset($error)) {
            // Cek apakah username sudah ada
            $check_username = mysqli_prepare($koneksi, "SELECT username FROM tb_karyawan WHERE username = ?");
            mysqli_stmt_bind_param($check_username, 's', $username);
            mysqli_stmt_execute($check_username);
            $check_user_res = mysqli_stmt_get_result($check_username);
            if ($check_user_res && mysqli_num_rows($check_user_res) > 0) {
                $error = "Username sudah terdaftar! Gunakan nama lain.";
            }
            mysqli_stmt_close($check_username);
        }

        if (!isset($error)) {
            $foto = 'default.jpg';
            $schema_cols = [];
            $schema_res = mysqli_query($koneksi, "SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_karyawan'");
            if ($schema_res) {
                while ($row = mysqli_fetch_assoc($schema_res)) {
                    $schema_cols[] = $row['COLUMN_NAME'];
                }
                mysqli_free_result($schema_res);
            }

            $columns = ['id_karyawan', 'username', 'password', 'nama'];
            $values = [$id_to_insert, $username, $password, $nama];
            $types = 'ssss';

            $optional_fields = [
                'jabatan' => $divisi,
                'tmp_tgl_lahir' => $tmp_tgl_lahir,
                'jenkel' => $jenkel,
                'agama' => $agama,
                'alamat' => $alamat,
                'no_tel' => $no_tel,
                'foto' => $foto
            ];

            foreach ($optional_fields as $field => $value) {
                if (empty($schema_cols) || in_array($field, $schema_cols, true)) {
                    $columns[] = $field;
                    $values[] = $value;
                    $types .= 's';
                }
            }

            $placeholders = implode(', ', array_fill(0, count($columns), '?'));
            $sql = "INSERT INTO tb_karyawan (" . implode(', ', $columns) . ") VALUES (" . $placeholders . ")";
            $stmt = mysqli_prepare($koneksi, $sql);
            if (!$stmt) {
                $error = "Gagal menyiapkan query: " . mysqli_error($koneksi);
            }
            else {
                $bind_params = array_merge([$types], $values);
                $bind_refs = [];
                foreach ($bind_params as $key => $value) {
                    $bind_refs[$key] = & $bind_params[$key];
                }
                call_user_func_array([$stmt, 'bind_param'], $bind_refs);
                if (mysqli_stmt_execute($stmt)) {
                    $_SESSION['registration_success'] = true;
                    $_SESSION['username'] = $username;
                    $_SESSION['reg_message'] = "Registrasi berhasil! Silakan login dengan:\nUsername: " . $username . "\nPassword: (password yang Anda daftarkan)";
                    mysqli_stmt_close($stmt);
                    header("Location: login.php");
                    exit();
                }
                else {
                    $error = "Gagal mendaftar: " . mysqli_error($koneksi);
                    mysqli_stmt_close($stmt);
                }
            }
        }
    }
}

// Ambil daftar divisi
$result = mysqli_query($koneksi, "SELECT jabatan FROM tb_jabatan");
if (!$result) {
    $error = "Error mengambil data divisi: " . mysqli_error($koneksi);
    $divisi = array();
}
else {
    $divisi = mysqli_fetch_all($result, MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Sistem Kehadiran KSE UINSU</title>
    <meta name="description" content="Registrasi Beswan Paguyuban KSE UINSU 2025-2026">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --glass-bg: rgba(255, 255, 255, 0.1);
            --glass-border: rgba(255, 255, 255, 0.18);
            --text-primary: #ffffff;
            --text-secondary: rgba(255, 255, 255, 0.85);
            --shadow-lg: 0 20px 60px rgba(0, 0, 0, 0.3);
            --shadow-xl: 0 30px 90px rgba(0, 0, 0, 0.4);
        }

        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 15px;
            position: relative;
            overflow-x: hidden;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Animated background particles */
        .bg-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            overflow: hidden;
            pointer-events: none;
        }

        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 20s infinite;
        }

        .particle:nth-child(1) {
            width: 80px;
            height: 80px;
            left: 10%;
            animation-delay: 0s;
            animation-duration: 25s;
        }

        .particle:nth-child(2) {
            width: 60px;
            height: 60px;
            left: 70%;
            animation-delay: 2s;
            animation-duration: 20s;
        }

        .particle:nth-child(3) {
            width: 100px;
            height: 100px;
            left: 40%;
            animation-delay: 4s;
            animation-duration: 30s;
        }

        .particle:nth-child(4) {
            width: 50px;
            height: 50px;
            left: 85%;
            animation-delay: 1s;
            animation-duration: 22s;
        }

        .particle:nth-child(5) {
            width: 70px;
            height: 70px;
            left: 25%;
            animation-delay: 3s;
            animation-duration: 28s;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(100vh) scale(0);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(-100px) scale(1);
            }
        }

        .register-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 900px;
            animation: fadeInUp 0.8s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .register-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 20px;
            border: 1px solid var(--glass-border);
            box-shadow: var(--shadow-xl);
            padding: 24px 28px;
            position: relative;
            overflow: hidden;
        }

        .register-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--success-gradient);
        }

        .register-card::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            pointer-events: none;
        }

        .header-section {
            text-align: center;
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
        }

        .logo-icon {
            width: 52px;
            height: 52px;
            margin: 0 auto 12px;
            background: var(--success-gradient);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 24px rgba(79, 172, 254, 0.4);
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 8px 24px rgba(79, 172, 254, 0.4);
            }
            50% {
                transform: scale(1.05);
                box-shadow: 0 12px 32px rgba(79, 172, 254, 0.6);
            }
        }

        .logo-icon svg {
            width: 30px;
            height: 30px;
            color: white;
        }

        .register-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 4px;
            letter-spacing: -0.5px;
        }

        .register-subtitle {
            font-size: 12px;
            font-weight: 400;
            color: var(--text-secondary);
            line-height: 1.5;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 16px;
            position: relative;
            z-index: 1;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.span-2 {
            grid-column: span 2;
        }

        .form-group.span-3 {
            grid-column: span 3;
        }

        .form-label {
            display: block;
            font-size: 11px;
            font-weight: 500;
            color: var(--text-primary);
            margin-bottom: 5px;
            letter-spacing: 0.3px;
        }

        .form-control {
            width: 100%;
            padding: 10px 14px;
            background: rgba(255, 255, 255, 0.08);
            border: 1.5px solid rgba(255, 255, 255, 0.15);
            border-radius: 10px;
            color: var(--text-primary);
            font-size: 13px;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            outline: none;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(255, 255, 255, 0.4);
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
            transform: translateY(-1px);
        }

        .form-control:hover {
            border-color: rgba(255, 255, 255, 0.25);
        }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%23ffffff' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 10px center;
            background-repeat: no-repeat;
            background-size: 1.3em 1.3em;
            padding-right: 36px;
            cursor: pointer;
        }

        /* Fix option visibility */
        select.form-control option {
            background-color: #764ba2; /* Dark purple background */
            color: #ffffff; /* White text */
            padding: 12px;
        }

        /* For Firefox */
        select.form-control:-moz-focusring {
            color: transparent;
            text-shadow: 0 0 0 #000;
        }

        .form-text {
            font-size: 10px;
            color: var(--text-secondary);
            margin-top: 4px;
            line-height: 1.3;
        }

        .alert {
            padding: 10px 14px;
            border-radius: 10px;
            margin-bottom: 16px;
            font-size: 12px;
            font-weight: 500;
            animation: slideDown 0.4s ease-out;
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.2);
            border: 1.5px solid rgba(239, 68, 68, 0.4);
            color: #ffffff;
        }

        .alert svg {
            flex-shrink: 0;
            width: 18px;
            height: 18px;
        }

        .btn-register {
            width: 100%;
            padding: 13px;
            background: var(--success-gradient);
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 14px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 6px 20px rgba(79, 172, 254, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 4px;
            position: relative;
            overflow: hidden;
        }

        .btn-register::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }

        .btn-register:hover::before {
            left: 100%;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 28px rgba(79, 172, 254, 0.5);
        }

        .btn-register:active {
            transform: translateY(0);
        }

        .btn-register svg {
            width: 18px;
            height: 18px;
            transition: transform 0.3s ease;
        }

        .btn-register:hover svg {
            transform: translateX(4px);
        }

        .divider {
            text-align: center;
            margin: 16px 0;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            width: 100%;
            height: 1px;
            background: rgba(255, 255, 255, 0.15);
        }

        .divider-text {
            position: relative;
            display: inline-block;
            padding: 0 14px;
            background: transparent;
            color: var(--text-secondary);
            font-size: 11px;
            font-weight: 500;
        }

        .login-link {
            text-align: center;
            font-size: 12px;
            color: var(--text-secondary);
            position: relative;
            z-index: 1;
        }

        .login-link p {
            margin-bottom: 6px;
        }

        .login-btn {
            color: var(--text-primary);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .login-btn::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--text-primary);
            transition: width 0.3s ease;
        }

        .login-btn:hover::after {
            width: 100%;
        }

        .login-btn svg {
            width: 14px;
            height: 14px;
            transition: transform 0.3s ease;
        }

        .login-btn:hover svg {
            transform: translateX(3px);
        }

        /* Responsive Design */
        @media (max-width: 900px) {
            .form-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            body {
                padding: 15px 10px;
            }

            .register-card {
                padding: 20px 22px;
            }

            .form-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .form-group.span-2,
            .form-group.span-3 {
                grid-column: span 1;
            }

            .register-title {
                font-size: 20px;
            }

            .register-subtitle {
                font-size: 11px;
            }

            .logo-icon {
                width: 48px;
                height: 48px;
            }

            .logo-icon svg {
                width: 28px;
                height: 28px;
            }
        }

        /* Loading animation */
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .loading {
            pointer-events: none;
            opacity: 0.7;
        }

        .loading .btn-register svg {
            animation: spin 1s linear infinite;
        }
    </style>
</head>
<body>
  <!-- Background Animation -->
  <div class="bg-animation">
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
  </div>

  <!-- Main Register Container -->
  <div class="register-container">
    <div class="register-card">
      <!-- Header Section -->
      <div class="header-section">
        <div class="logo-icon">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
          </svg>
        </div>
        <h1 class="register-title">Registrasi Beswan</h1>
        <p class="register-subtitle">Lengkapi data diri Anda - Paguyuban KSE UINSU 2025-2026</p>
      </div>

      <!-- Alert Messages -->
      <?php if (isset($error)): ?>
        <div class="alert alert-danger">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width: 20px; height: 20px;">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
          </svg>
          <span><?php echo $error; ?></span>
        </div>
      <?php
endif; ?>

      <!-- Registration Form -->
      <form action="" method="POST" id="registerForm">
        <div class="form-grid">
          <!-- Nama Lengkap -->
          <div class="form-group">
            <label class="form-label" for="nama">Nama Lengkap</label>
            <input 
              type="text" 
              id="nama" 
              name="nama" 
              class="form-control" 
              placeholder="Masukkan nama lengkap"
              value="<?php echo isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : ''; ?>" 
              required
              autocomplete="name"
            >
          </div>

          <!-- Username -->
          <div class="form-group">
            <label class="form-label" for="username">Username</label>
            <input 
              type="text" 
              id="username" 
              name="username" 
              class="form-control" 
              placeholder="Username untuk login"
              value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" 
              required
              autocomplete="username"
            >
            <small class="form-text">Username ini akan digunakan untuk login</small>
          </div>

          <!-- No. Induk KSE -->
          <div class="form-group">
            <label class="form-label" for="no_induk">No. Induk KSE</label>
            <input 
              type="text" 
              id="no_induk" 
              name="no_induk" 
              class="form-control" 
              placeholder="KSE.XXXX.XXXXX"
              value="<?php echo isset($_POST['no_induk']) ? htmlspecialchars($_POST['no_induk']) : ''; ?>" 
              required
              pattern="KSE\.\d{4}\.\d{5}"
              title="Format: KSE.XXXX.XXXXX"
            >
          </div>

          <!-- Tempat, Tanggal Lahir -->
          <div class="form-group">
            <label class="form-label" for="tmp_tgl_lahir">Tempat, Tanggal Lahir</label>
            <input 
              type="text" 
              id="tmp_tgl_lahir" 
              name="tmp_tgl_lahir" 
              class="form-control" 
              placeholder="Jakarta / 01-01-2000"
              value="<?php echo isset($_POST['tmp_tgl_lahir']) ? htmlspecialchars($_POST['tmp_tgl_lahir']) : ''; ?>" 
              required
            >
          </div>

          <!-- Jenis Kelamin -->
          <div class="form-group">
            <label class="form-label" for="jenkel">Jenis Kelamin</label>
            <select id="jenkel" name="jenkel" class="form-control" required>
              <option value="">Pilih Jenis Kelamin</option>
              <option value="Laki-laki" <?php echo(isset($_POST['jenkel']) && $_POST['jenkel'] == 'Laki-laki') ? 'selected' : ''; ?>>Laki-laki</option>
              <option value="Perempuan" <?php echo(isset($_POST['jenkel']) && $_POST['jenkel'] == 'Perempuan') ? 'selected' : ''; ?>>Perempuan</option>
            </select>
          </div>

          <!-- Agama -->
          <div class="form-group">
            <label class="form-label" for="agama">Agama</label>
            <select id="agama" name="agama" class="form-control" required>
              <option value="">Pilih Agama</option>
              <?php
$list_agama = ['Islam', 'Kristen', 'Katholik', 'Hindu', 'Buddha', 'KongHuCu'];
foreach ($list_agama as $ag): ?>
                <option value="<?php echo $ag; ?>" <?php echo(isset($_POST['agama']) && $_POST['agama'] == $ag) ? 'selected' : ''; ?>>
                  <?php echo $ag; ?>
                </option>
              <?php
endforeach; ?>
            </select>
          </div>

          <!-- No. Telepon -->
          <div class="form-group">
            <label class="form-label" for="no_tel">No. Telepon</label>
            <input 
              type="tel" 
              id="no_tel" 
              name="no_tel" 
              class="form-control" 
              placeholder="081234567890"
              value="<?php echo isset($_POST['no_tel']) ? htmlspecialchars($_POST['no_tel']) : ''; ?>" 
              required
              autocomplete="tel"
            >
          </div>

          <!-- Divisi -->
          <div class="form-group">
            <label class="form-label" for="divisi">Divisi</label>
            <select id="divisi" name="divisi" class="form-control" required>
              <option value="">Pilih Divisi</option>
              <?php foreach ($divisi as $d): ?>
                <option value="<?php echo htmlspecialchars($d['jabatan']); ?>" <?php echo(isset($_POST['divisi']) && $_POST['divisi'] == $d['jabatan']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($d['jabatan']); ?></option>
              <?php
endforeach; ?>
            </select>
          </div>

          <!-- Alamat (Full Width) -->
          <div class="form-group span-2">
            <label class="form-label" for="alamat">Alamat Lengkap</label>
            <input 
              type="text" 
              id="alamat" 
              name="alamat" 
              class="form-control" 
              placeholder="Masukkan alamat lengkap"
              value="<?php echo isset($_POST['alamat']) ? htmlspecialchars($_POST['alamat']) : ''; ?>" 
              required
              autocomplete="street-address"
            >
          </div>

          <!-- Password -->
          <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <input 
              type="password" 
              id="password" 
              name="password" 
              class="form-control" 
              placeholder="Buat password"
              required 
              pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" 
              title="Password harus memiliki minimal 8 karakter, mengandung huruf besar, huruf kecil, dan angka"
              autocomplete="new-password"
            >
            <small class="form-text">Min. 8 karakter dengan huruf besar, kecil, dan angka</small>
          </div>

          <!-- Konfirmasi Password -->
          <div class="form-group">
            <label class="form-label" for="confirm_password">Konfirmasi Password</label>
            <input 
              type="password" 
              id="confirm_password" 
              name="confirm_password" 
              class="form-control" 
              placeholder="Ulangi password"
              required
              autocomplete="new-password"
            >
          </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" name="submit" class="btn-register">
          <span>Daftar Sekarang</span>
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
          </svg>
        </button>
      </form>

      <!-- Divider -->
      <div class="divider">
        <span class="divider-text">atau</span>
      </div>

      <!-- Login Link -->
      <div class="login-link">
        <p>Sudah punya akun?</p>
        <a href="login.php" class="login-btn">
          <span>Login ke Akun Anda</span>
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
          </svg>
        </a>
      </div>
    </div>
  </div>

  <script>
    // Form submission animation
    document.getElementById('registerForm').addEventListener('submit', function() {
      this.classList.add('loading');
    });

    // Password confirmation validation
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');

    function validatePassword() {
      if (password.value !== confirmPassword.value) {
        confirmPassword.setCustomValidity('Password tidak cocok!');
      } else {
        confirmPassword.setCustomValidity('');
      }
    }

    password.addEventListener('change', validatePassword);
    confirmPassword.addEventListener('keyup', validatePassword);

    // Input focus animations
    const inputs = document.querySelectorAll('.form-control');
    inputs.forEach(input => {
      input.addEventListener('focus', function() {
        this.parentElement.style.transform = 'translateX(2px)';
      });
      input.addEventListener('blur', function() {
        this.parentElement.style.transform = 'translateX(0)';
      });
    });
  </script>
</body>
</html>
