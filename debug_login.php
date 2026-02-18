<?php
// Script debug untuk login admin
include 'koneksi.php';

echo "<h2>🔍 Debug Login Admin</h2>";

// Test data yang akan digunakan untuk login
$test_username = 'ADMINKECE';
$test_password = 'ADMIN2025';

echo "<h3>1. Test Koneksi Database:</h3>";
if ($koneksi) {
    echo "✅ Koneksi database berhasil<br><br>";
} else {
    echo "❌ Koneksi database gagal: " . mysqli_connect_error() . "<br><br>";
    exit;
}

echo "<h3>2. Cek Tabel tb_daftar:</h3>";
$check_table = mysqli_query($koneksi, "SHOW TABLES LIKE 'tb_daftar'");
if (mysqli_num_rows($check_table) > 0) {
    echo "✅ Tabel tb_daftar ada<br><br>";
} else {
    echo "❌ Tabel tb_daftar tidak ada<br><br>";
    exit;
}

echo "<h3>3. Cek Data Admin di Database:</h3>";
$admin_query = "SELECT * FROM tb_daftar";
$admin_result = mysqli_query($koneksi, $admin_query);

if ($admin_result) {
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr style='background: #f2f2f2;'><th style='padding: 8px;'>ID</th><th style='padding: 8px;'>Username</th><th style='padding: 8px;'>Password</th></tr>";
    
    $found_admin = false;
    while ($row = mysqli_fetch_assoc($admin_result)) {
        echo "<tr>";
        echo "<td style='padding: 8px;'>" . $row['id'] . "</td>";
        echo "<td style='padding: 8px;'>" . $row['username'] . "</td>";
        echo "<td style='padding: 8px;'>" . $row['password'] . "</td>";
        echo "</tr>";
        
        if ($row['username'] == $test_username) {
            $found_admin = true;
            $db_password = $row['password'];
        }
    }
    echo "</table>";
    
    if ($found_admin) {
        echo "✅ Admin ADMINKECE ditemukan di database<br>";
        echo "Password di database: '$db_password'<br>";
        echo "Password yang ditest: '$test_password'<br>";
        
        if ($db_password == $test_password) {
            echo "✅ Password cocok!<br><br>";
        } else {
            echo "❌ Password tidak cocok!<br><br>";
        }
    } else {
        echo "❌ Admin ADMINKECE tidak ditemukan<br><br>";
        
        // Tambahkan admin jika belum ada
        echo "<h4>Menambahkan admin ADMINKECE...</h4>";
        $insert_sql = "INSERT INTO tb_daftar (username, password) VALUES ('$test_username', '$test_password')";
        if (mysqli_query($koneksi, $insert_sql)) {
            echo "✅ Admin berhasil ditambahkan!<br><br>";
        } else {
            echo "❌ Gagal menambahkan admin: " . mysqli_error($koneksi) . "<br><br>";
        }
    }
} else {
    echo "❌ Error query: " . mysqli_error($koneksi) . "<br><br>";
}

echo "<h3>4. Test Query Login (Simulasi proses_login.php):</h3>";
$login_query = "SELECT * FROM tb_daftar WHERE username='$test_username'";
echo "Query: $login_query<br>";

$login_result = mysqli_query($koneksi, $login_query);
if ($login_result && mysqli_num_rows($login_result) > 0) {
    $user_data = mysqli_fetch_assoc($login_result);
    echo "✅ User ditemukan<br>";
    echo "Username dari DB: '" . $user_data['username'] . "'<br>";
    echo "Password dari DB: '" . $user_data['password'] . "'<br>";
    echo "Password input: '$test_password'<br>";
    
    if ($test_password == $user_data['password']) {
        echo "✅ Login akan berhasil!<br><br>";
    } else {
        echo "❌ Password tidak cocok, login akan gagal!<br><br>";
    }
} else {
    echo "❌ User tidak ditemukan dengan query tersebut<br><br>";
}

echo "<h3>5. Cek File proses_login.php:</h3>";
if (file_exists('proses_login.php')) {
    echo "✅ File proses_login.php ada<br>";
    
    // Baca isi file proses_login.php
    $login_content = file_get_contents('proses_login.php');
    if (strpos($login_content, 'tb_daftar') !== false) {
        echo "✅ File menggunakan tabel tb_daftar<br>";
    } else {
        echo "❌ File tidak menggunakan tabel tb_daftar<br>";
    }
    
    if (strpos($login_content, 'admin2.php') !== false) {
        echo "✅ File akan redirect ke admin2.php<br>";
    } else {
        echo "❌ File tidak redirect ke admin2.php<br>";
    }
} else {
    echo "❌ File proses_login.php tidak ada<br>";
}

echo "<hr>";
echo "<h3>🧪 Test Login Manual:</h3>";
echo "<form method='POST' action='debug_login.php' style='background: #f8f9fa; padding: 20px; border-radius: 5px;'>";
echo "<p><strong>Username:</strong> <input type='text' name='test_user' value='ADMINKECE' style='padding: 5px;'></p>";
echo "<p><strong>Password:</strong> <input type='text' name='test_pass' value='ADMIN2025' style='padding: 5px;'></p>";
echo "<p><input type='submit' name='test_login' value='Test Login' style='background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 3px;'></p>";
echo "</form>";

if (isset($_POST['test_login'])) {
    $username = $_POST['test_user'];
    $password = $_POST['test_pass'];
    
    echo "<h4>Hasil Test Login:</h4>";
    echo "Username: '$username'<br>";
    echo "Password: '$password'<br>";
    
    $sql_admin = "SELECT * FROM tb_daftar WHERE username='$username'";
    $query_admin = mysqli_query($koneksi, $sql_admin);
    
    if (mysqli_num_rows($query_admin) > 0) {
        $hasil = mysqli_fetch_assoc($query_admin);
        echo "✅ User ditemukan<br>";
        echo "Password DB: '" . $hasil['password'] . "'<br>";
        
        if ($password == $hasil['password']) {
            echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
            echo "✅ <strong>LOGIN BERHASIL!</strong><br>";
            echo "Seharusnya bisa login dengan kredensial ini.";
            echo "</div>";
        } else {
            echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
            echo "❌ <strong>PASSWORD SALAH!</strong><br>";
            echo "Password di database berbeda dengan yang diinput.";
            echo "</div>";
        }
    } else {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "❌ <strong>USERNAME TIDAK DITEMUKAN!</strong>";
        echo "</div>";
    }
}

echo "<hr>";
echo "<p><a href='login.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 3px;'>Coba Login di Halaman Asli</a></p>";

mysqli_close($koneksi);
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 1000px;
    margin: 20px auto;
    padding: 20px;
    background: #f5f5f5;
}
table {
    background: white;
    width: 100%;
}
th, td {
    border: 1px solid #ddd;
    text-align: left;
}
th {
    background: #007bff;
    color: white;
}
</style>