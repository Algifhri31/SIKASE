<?php
// Script untuk menambahkan admin baru
// Username: ADMINKECE
// Password: ADMIN2025

include 'koneksi.php';

// Data admin baru
$username = 'ADMINKECE';
$password = 'ADMIN2025';

// Cek apakah username sudah ada
$check_sql = "SELECT * FROM tb_daftar WHERE username = ?";
$check_stmt = mysqli_prepare($koneksi, $check_sql);
mysqli_stmt_bind_param($check_stmt, "s", $username);
mysqli_stmt_execute($check_stmt);
$check_result = mysqli_stmt_get_result($check_stmt);

if (mysqli_num_rows($check_result) > 0) {
    echo "<h2>❌ Error!</h2>";
    echo "<p>Username '$username' sudah ada di database.</p>";
} else {
    // Insert admin baru
    $insert_sql = "INSERT INTO tb_daftar (username, password) VALUES (?, ?)";
    $insert_stmt = mysqli_prepare($koneksi, $insert_sql);
    mysqli_stmt_bind_param($insert_stmt, "ss", $username, $password);
    
    if (mysqli_stmt_execute($insert_stmt)) {
        echo "<h2>✅ Berhasil!</h2>";
        echo "<p>Admin baru berhasil ditambahkan:</p>";
        echo "<ul>";
        echo "<li><strong>Username:</strong> $username</li>";
        echo "<li><strong>Password:</strong> $password</li>";
        echo "</ul>";
        echo "<p><a href='login.php'>Login sebagai Admin</a></p>";
    } else {
        echo "<h2>❌ Error!</h2>";
        echo "<p>Gagal menambahkan admin: " . mysqli_error($koneksi) . "</p>";
    }
    mysqli_stmt_close($insert_stmt);
}

mysqli_stmt_close($check_stmt);

// Tampilkan semua admin yang ada
echo "<hr>";
echo "<h3>Daftar Admin yang Ada:</h3>";
$admin_list = mysqli_query($koneksi, "SELECT * FROM tb_daftar ORDER BY id");
if (mysqli_num_rows($admin_list) > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #f2f2f2;'>";
    echo "<th style='padding: 10px;'>ID</th>";
    echo "<th style='padding: 10px;'>Username</th>";
    echo "<th style='padding: 10px;'>Password</th>";
    echo "</tr>";
    
    while ($admin = mysqli_fetch_assoc($admin_list)) {
        echo "<tr>";
        echo "<td style='padding: 10px; text-align: center;'>" . $admin['id'] . "</td>";
        echo "<td style='padding: 10px;'>" . htmlspecialchars($admin['username']) . "</td>";
        echo "<td style='padding: 10px;'>" . htmlspecialchars($admin['password']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>Tidak ada admin di database.</p>";
}

mysqli_close($koneksi);
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 50px auto;
    padding: 20px;
    background: #f5f5f5;
}

h2, h3 {
    color: #333;
}

table {
    margin-top: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    background: white;
}

th {
    background: #667eea !important;
    color: white !important;
}

tr:nth-child(even) {
    background: #f9f9f9;
}

a {
    display: inline-block;
    background: #667eea;
    color: white;
    padding: 10px 20px;
    text-decoration: none;
    border-radius: 5px;
    margin-top: 15px;
}

a:hover {
    background: #5a6fd8;
}

ul {
    background: #e8f5e8;
    padding: 15px;
    border-radius: 5px;
    border-left: 4px solid #4caf50;
}
</style>