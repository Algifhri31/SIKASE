<!DOCTYPE html>
<html>
<head>
    <title>Fix Admin Login - KSE</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 5px solid #28a745;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 5px solid #dc3545;
        }
        .info {
            background: #e7f3ff;
            color: #0c5460;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 5px solid #007bff;
        }
        .btn {
            background: #007bff;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            display: inline-block;
            margin: 10px 5px;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        .btn:hover {
            background: #0056b3;
        }
        .btn-success {
            background: #28a745;
        }
        .btn-success:hover {
            background: #1e7e34;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: white;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background: #f8f9fa;
        }
        .highlight {
            background: #fff3cd !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Fix Admin Login - KSE</h1>
        
        <?php
        include 'koneksi.php';
        
        // Cek koneksi
        if (!$koneksi) {
            echo "<div class='error'>";
            echo "<h3>❌ Koneksi Database Gagal!</h3>";
            echo "<p>" . mysqli_connect_error() . "</p>";
            echo "</div>";
            exit;
        }
        
        echo "<div class='success'>";
        echo "<h3>✅ Koneksi Database Berhasil!</h3>";
        echo "</div>";
        
        // Jika tombol "Fix Admin" diklik
        if (isset($_POST['fix_admin'])) {
            echo "<h2>🔧 Memperbaiki Admin...</h2>";
            
            // Buat tabel tb_daftar jika belum ada
            $create_table = "CREATE TABLE IF NOT EXISTS `tb_daftar` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `username` varchar(255) NOT NULL,
                `password` varchar(255) NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
            
            if (mysqli_query($koneksi, $create_table)) {
                echo "<p>✅ Tabel tb_daftar siap</p>";
            } else {
                echo "<div class='error'>❌ Error membuat tabel: " . mysqli_error($koneksi) . "</div>";
            }
            
            // Hapus admin lama
            mysqli_query($koneksi, "DELETE FROM tb_daftar WHERE username = 'ADMINKECE'");
            echo "<p>🗑️ Admin lama dihapus</p>";
            
            // Insert admin baru
            $insert_admin = "INSERT INTO tb_daftar (username, password) VALUES ('ADMINKECE', 'ADMIN2025')";
            if (mysqli_query($koneksi, $insert_admin)) {
                echo "<div class='success'>";
                echo "<h3>✅ Admin ADMINKECE Berhasil Ditambahkan!</h3>";
                echo "<p><strong>Username:</strong> ADMINKECE</p>";
                echo "<p><strong>Password:</strong> ADMIN2025</p>";
                echo "</div>";
            } else {
                echo "<div class='error'>❌ Error menambahkan admin: " . mysqli_error($koneksi) . "</div>";
            }
        }
        
        // Jika tombol "Test Login" diklik
        if (isset($_POST['test_login'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];
            
            echo "<h2>🧪 Test Login</h2>";
            echo "<p><strong>Username:</strong> $username</p>";
            echo "<p><strong>Password:</strong> $password</p>";
            
            // Test query admin
            $login_query = "SELECT * FROM tb_daftar WHERE username = '$username'";
            $result = mysqli_query($koneksi, $login_query);
            
            if ($result && mysqli_num_rows($result) > 0) {
                $admin = mysqli_fetch_assoc($result);
                if ($password == $admin['password']) {
                    echo "<div class='success'>";
                    echo "<h3>✅ LOGIN BERHASIL!</h3>";
                    echo "<p>Redirect ke admin dashboard...</p>";
                    echo "</div>";
                    
                    // Set session dan redirect
                    session_start();
                    $_SESSION['username'] = $admin['username'];
                    $_SESSION['level'] = 'admin';
                    $_SESSION['admin_id'] = $admin['id'];
                    
                    echo "<script>";
                    echo "setTimeout(function() { window.location.href = 'admin2.php'; }, 2000);";
                    echo "</script>";
                    echo "<p><a href='admin2.php' class='btn btn-success'>Masuk ke Dashboard Admin</a></p>";
                } else {
                    echo "<div class='error'>";
                    echo "<h3>❌ Password Salah!</h3>";
                    echo "<p>Password di database: '" . $admin['password'] . "'</p>";
                    echo "<p>Password yang diinput: '$password'</p>";
                    echo "</div>";
                }
            } else {
                echo "<div class='error'>";
                echo "<h3>❌ Username Tidak Ditemukan!</h3>";
                echo "<p>Query: $login_query</p>";
                echo "</div>";
            }
        }
        
        // Tampilkan semua admin yang ada
        echo "<h2>📋 Admin di Database</h2>";
        $admin_list = mysqli_query($koneksi, "SELECT * FROM tb_daftar ORDER BY id");
        
        if ($admin_list && mysqli_num_rows($admin_list) > 0) {
            echo "<table>";
            echo "<tr><th>ID</th><th>Username</th><th>Password</th></tr>";
            
            while ($admin = mysqli_fetch_assoc($admin_list)) {
                $class = ($admin['username'] == 'ADMINKECE') ? 'highlight' : '';
                echo "<tr class='$class'>";
                echo "<td>" . $admin['id'] . "</td>";
                echo "<td>" . $admin['username'] . "</td>";
                echo "<td>" . $admin['password'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<div class='info'>";
            echo "<h3>ℹ️ Tidak Ada Admin di Database</h3>";
            echo "<p>Klik tombol 'Fix Admin' untuk menambahkan admin ADMINKECE</p>";
            echo "</div>";
        }
        
        mysqli_close($koneksi);
        ?>
        
        <hr>
        
        <h2>🛠️ Actions</h2>
        
        <!-- Form Fix Admin -->
        <form method="POST" style="display: inline-block;">
            <button type="submit" name="fix_admin" class="btn">
                🔧 Fix Admin (Tambah ADMINKECE)
            </button>
        </form>
        
        <!-- Form Test Login -->
        <form method="POST" style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h3>🧪 Test Login</h3>
            <p>
                <label>Username:</label><br>
                <input type="text" name="username" value="ADMINKECE" style="padding: 10px; width: 200px; border: 1px solid #ddd; border-radius: 4px;">
            </p>
            <p>
                <label>Password:</label><br>
                <input type="text" name="password" value="ADMIN2025" style="padding: 10px; width: 200px; border: 1px solid #ddd; border-radius: 4px;">
            </p>
            <button type="submit" name="test_login" class="btn btn-success">
                🚀 Test Login
            </button>
        </form>
        
        <div class="info">
            <h3>📋 Langkah-langkah:</h3>
            <ol>
                <li><strong>Klik "Fix Admin"</strong> untuk menambahkan admin ADMINKECE ke database</li>
                <li><strong>Klik "Test Login"</strong> untuk test login dengan kredensial ADMINKECE</li>
                <li><strong>Jika berhasil</strong>, akan otomatis masuk ke dashboard admin</li>
                <li><strong>Atau login manual</strong> di <a href="login.php">login.php</a></li>
            </ol>
        </div>
    </div>
</body>
</html>