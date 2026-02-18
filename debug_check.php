<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h2>🔍 Debug Check - KSE System</h2>";

// Test PHP
echo "<h3>1. PHP Status:</h3>";
echo "✅ PHP Version: " . phpversion() . "<br>";
echo "✅ PHP working correctly<br><br>";

// Test database connection
echo "<h3>2. Database Connection:</h3>";
try {
    include 'koneksi.php';
    
    if (isset($koneksi) && $koneksi) {
        echo "✅ Database connection successful<br>";
        
        // Test query
        $test_query = "SHOW TABLES";
        $result = mysqli_query($koneksi, $test_query);
        
        if ($result) {
            echo "✅ Database query working<br>";
            echo "<strong>Tables found:</strong><br>";
            while ($row = mysqli_fetch_array($result)) {
                echo "- " . $row[0] . "<br>";
            }
        } else {
            echo "❌ Database query failed: " . mysqli_error($koneksi) . "<br>";
        }
    } else {
        echo "❌ Database connection failed<br>";
    }
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

echo "<br><h3>3. File System Check:</h3>";

// Check important files
$important_files = [
    'koneksi.php',
    'karyawansi.sql',
    'admin_dashboard_modern.php',
    'karyawan/index.php',
    'karyawan/awal.php'
];

foreach ($important_files as $file) {
    if (file_exists($file)) {
        echo "✅ $file exists<br>";
    } else {
        echo "❌ $file missing<br>";
    }
}

echo "<br><h3>4. Session Check:</h3>";
session_start();
echo "✅ Session started successfully<br>";
echo "Session ID: " . session_id() . "<br>";

echo "<br><h3>5. Server Info:</h3>";
echo "Server: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Script Name: " . $_SERVER['SCRIPT_NAME'] . "<br>";

echo "<br><h3>6. Quick Links:</h3>";
echo "<a href='check_credentials.php' style='background: #dc3545; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px; margin: 5px;'>🔍 Check Credentials</a><br><br>";
echo "<a href='admin_dashboard_modern.php' style='background: #007bff; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px; margin: 5px;'>Admin Dashboard</a><br><br>";
echo "<a href='karyawan/index.php' style='background: #28a745; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px; margin: 5px;'>Karyawan Dashboard</a><br><br>";
echo "<a href='update_database_structure.php' style='background: #ffc107; color: black; padding: 8px 16px; text-decoration: none; border-radius: 4px; margin: 5px;'>Database Check</a><br><br>";

echo "<hr>";
echo "<p><strong>Jika semua status di atas ✅, maka sistem siap digunakan!</strong></p>";
?>