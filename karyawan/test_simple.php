<?php
session_start();

// Test login sederhana
if (isset($_POST['login_test'])) {
    $_SESSION['idsi'] = $_POST['id_karyawan'];
    $_SESSION['namasi'] = $_POST['nama'];
    $_SESSION['level'] = 'beswan';
    
    echo "<script>alert('Login berhasil! Redirect ke dashboard...'); window.location.href='index.php';</script>";
    exit();
}

// Jika sudah login, tampilkan info
if (isset($_SESSION['idsi'])) {
    echo "<h2>Session Active</h2>";
    echo "<p>ID: " . $_SESSION['idsi'] . "</p>";
    echo "<p>Nama: " . ($_SESSION['namasi'] ?? 'Not set') . "</p>";
    echo "<p>Level: " . ($_SESSION['level'] ?? 'Not set') . "</p>";
    echo "<a href='index.php' class='btn btn-primary'>Go to Dashboard</a> ";
    echo "<a href='logout.php' class='btn btn-danger'>Logout</a>";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Test Login Simple</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Test Login Simple</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">ID Karyawan</label>
                                <input type="text" class="form-control" name="id_karyawan" value="BSW001" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Nama</label>
                                <input type="text" class="form-control" name="nama" value="Test Beswan" required>
                            </div>
                            
                            <button type="submit" name="login_test" class="btn btn-primary w-100">
                                Test Login
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>