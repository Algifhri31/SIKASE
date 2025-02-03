<?php
session_start();
include("koneksi.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SIKAP</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
  <style>
    body {
      background: linear-gradient(to bottom right, #2c3e50, #4ca1af);
      height: 100vh;
      margin: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      font-family: Arial, sans-serif;
      color: white;
    }
    .button-container {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
      text-align: center;
    }
    .btn-custom {
      display: block;
      width: 100%;
      max-width: 200px;
      margin: 10px auto;
      padding: 12px 0;
      font-size: 1.2rem;
      border: none;
      border-radius: 25px;
      color: white;
      transition: all 0.3s ease;
    }
    .btn-admin {
      background: #f39c12;
    }
    .btn-admin:hover {
      background: #e67e22;
      transform: scale(1.05);
    }
    .btn-karyawan {
      background: #2980b9;
    }
    .btn-karyawan:hover {
      background: #3498db;
      transform: scale(1.05);
    }
  </style>
</head>
<body>
  <div class="button-container">
    <h2>Sistem Kehadiran Anggota Paguyuban </h2>
    <button class="btn btn-custom btn-admin" onclick="location.href='login.php'">Login Admin</button>
    <button class="btn btn-custom btn-karyawan" onclick="location.href='karyawan/login_karyawan.php'">Login Beswan</button>
  </div>
</body>
</html>
