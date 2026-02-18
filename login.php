
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - Sistem Kehadiran KSE UINSU</title>
<meta name="description" content="Sistem Kehadiran Anggota Paguyuban KSE UINSU 2025-2026">
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
    padding: 20px;
    position: relative;
    overflow: hidden;
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

.login-container {
    position: relative;
    z-index: 1;
    width: 100%;
    max-width: 440px;
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

.login-card {
    background: var(--glass-bg);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-radius: 24px;
    border: 1px solid var(--glass-border);
    box-shadow: var(--shadow-xl);
    padding: 48px 40px;
    position: relative;
    overflow: hidden;
}

.login-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--success-gradient);
}

.login-card::after {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    pointer-events: none;
}

.logo-section {
    text-align: center;
    margin-bottom: 36px;
    position: relative;
    z-index: 1;
}

.logo-icon {
    width: 72px;
    height: 72px;
    margin: 0 auto 20px;
    background: var(--success-gradient);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 10px 30px rgba(79, 172, 254, 0.4);
    animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
        box-shadow: 0 10px 30px rgba(79, 172, 254, 0.4);
    }
    50% {
        transform: scale(1.05);
        box-shadow: 0 15px 40px rgba(79, 172, 254, 0.6);
    }
}

.logo-icon svg {
    width: 40px;
    height: 40px;
    color: white;
}

.login-title {
    font-size: 28px;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 8px;
    letter-spacing: -0.5px;
}

.login-subtitle {
    font-size: 15px;
    font-weight: 400;
    color: var(--text-secondary);
    line-height: 1.6;
}

.form-group {
    margin-bottom: 20px;
    position: relative;
    z-index: 1;
}

.form-label {
    display: block;
    font-size: 14px;
    font-weight: 500;
    color: var(--text-primary);
    margin-bottom: 8px;
    letter-spacing: 0.3px;
}

.input-wrapper {
    position: relative;
}

.input-icon {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-secondary);
    width: 20px;
    height: 20px;
    pointer-events: none;
}

.form-control {
    width: 100%;
    padding: 14px 16px 14px 48px;
    background: rgba(255, 255, 255, 0.08);
    border: 1.5px solid rgba(255, 255, 255, 0.15);
    border-radius: 12px;
    color: var(--text-primary);
    font-size: 15px;
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
    box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.1);
    transform: translateY(-2px);
}

.form-control:hover {
    border-color: rgba(255, 255, 255, 0.25);
}

.alert {
    padding: 14px 18px;
    border-radius: 12px;
    margin-bottom: 24px;
    font-size: 14px;
    font-weight: 500;
    text-align: center;
    animation: slideDown 0.4s ease-out;
    backdrop-filter: blur(10px);
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

.alert.success {
    background: rgba(34, 197, 94, 0.2);
    border: 1.5px solid rgba(34, 197, 94, 0.4);
    color: #ffffff;
}

.alert.error {
    background: rgba(239, 68, 68, 0.2);
    border: 1.5px solid rgba(239, 68, 68, 0.4);
    color: #ffffff;
}

.btn-login {
    width: 100%;
    padding: 16px;
    background: var(--success-gradient);
    border: none;
    border-radius: 12px;
    color: white;
    font-size: 16px;
    font-weight: 600;
    font-family: 'Poppins', sans-serif;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 8px 24px rgba(79, 172, 254, 0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin-top: 28px;
    position: relative;
    overflow: hidden;
}

.btn-login::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.5s;
}

.btn-login:hover::before {
    left: 100%;
}

.btn-login:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 32px rgba(79, 172, 254, 0.5);
}

.btn-login:active {
    transform: translateY(0);
}

.btn-login svg {
    width: 20px;
    height: 20px;
    transition: transform 0.3s ease;
}

.btn-login:hover svg {
    transform: translateX(4px);
}

.divider {
    text-align: center;
    margin: 28px 0;
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
    padding: 0 16px;
    background: transparent;
    color: var(--text-secondary);
    font-size: 13px;
    font-weight: 500;
}

.register-link {
    text-align: center;
    font-size: 14px;
    color: var(--text-secondary);
    position: relative;
    z-index: 1;
}

.register-btn {
    color: var(--text-primary);
    text-decoration: none;
    font-weight: 600;
    margin-left: 6px;
    transition: all 0.3s ease;
    position: relative;
}

.register-btn::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    width: 0;
    height: 2px;
    background: var(--text-primary);
    transition: width 0.3s ease;
}

.register-btn:hover::after {
    width: 100%;
}

.footer {
    text-align: center;
    margin-top: 32px;
    padding: 20px;
    background: rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(10px);
    border-radius: 16px;
    border: 1px solid rgba(255, 255, 255, 0.12);
}

.copyright {
    font-size: 14px;
    color: var(--text-secondary);
    margin-bottom: 6px;
    font-weight: 500;
}

.credit {
    font-size: 13px;
    color: var(--text-secondary);
    opacity: 0.8;
}

/* Responsive Design */
@media (max-width: 480px) {
    .login-card {
        padding: 36px 28px;
    }
    
    .login-title {
        font-size: 24px;
    }
    
    .login-subtitle {
        font-size: 14px;
    }
    
    .logo-icon {
        width: 64px;
        height: 64px;
    }
    
    .logo-icon svg {
        width: 36px;
        height: 36px;
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

.loading .btn-login svg {
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

  <!-- Main Login Container -->
  <div class="login-container">
    <div class="login-card">
      <!-- Logo Section -->
      <div class="logo-section">
        <div class="logo-icon">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
          </svg>
        </div>
        <h1 class="login-title">Selamat Datang</h1>
        <p class="login-subtitle">Sistem Kehadiran Anggota<br>Paguyuban KSE UINSU 2025-2026</p>
      </div>

      <!-- Alert Messages -->
      <?php
if (isset($_SESSION['registration_success'])) {
    echo '<div class="alert success">' . $_SESSION['reg_message'] . '</div>';
    unset($_SESSION['registration_success']);
    unset($_SESSION['username']);
    unset($_SESSION['reg_message']);
}
if (isset($_SESSION['login_error'])) {
    echo '<div class="alert error">' . $_SESSION['login_error'] . '</div>';
    unset($_SESSION['login_error']);
}
if (isset($_GET['message']) && $_GET['message'] === 'logout_success') {
    echo '<div class="alert success">Anda telah berhasil logout.</div>';
}
?>

      <!-- Login Form -->
      <form action="proses_login.php" method="post" id="loginForm">
        <div class="form-group">
          <label class="form-label" for="username">Username</label>
          <div class="input-wrapper">
            <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <input 
              type="text" 
              id="username" 
              name="username" 
              class="form-control" 
              placeholder="Masukkan username Anda" 
              required 
              autocomplete="username"
            >
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="password">Password</label>
          <div class="input-wrapper">
            <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            <input 
              type="password" 
              id="password" 
              name="password" 
              class="form-control" 
              placeholder="Masukkan password Anda" 
              required 
              autocomplete="current-password"
            >
          </div>
        </div>

        <button type="submit" class="btn-login">
          <span>Masuk ke Sistem</span>
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
          </svg>
        </button>
      </form>

      <!-- Divider -->
      <div class="divider">
        <span class="divider-text">atau</span>
      </div>

      <!-- Register Link -->
      <div class="register-link">
        Belum punya akun?<a href="register.php" class="register-btn">Daftar Sekarang</a>
      </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
      <div class="copyright">© 2025-2026 KSE UINSU. All Rights Reserved.</div>
      <div class="credit">Developed by Media & Creative Division</div>
    </footer>
  </div>

  <script>
    // Form submission animation
    document.getElementById('loginForm').addEventListener('submit', function() {
      this.classList.add('loading');
    });

    // Input focus animations
    const inputs = document.querySelectorAll('.form-control');
    inputs.forEach(input => {
      input.addEventListener('focus', function() {
        this.parentElement.parentElement.style.transform = 'translateX(4px)';
      });
      input.addEventListener('blur', function() {
        this.parentElement.parentElement.style.transform = 'translateX(0)';
      });
    });
  </script>
</body>
</html>