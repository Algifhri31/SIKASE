<?php
// View PHP error log
echo "<h2>PHP Error Log Viewer</h2>";
echo "<p>Refresh halaman ini setelah submit form untuk melihat log terbaru</p>";
echo "<hr>";

// Check different possible error log locations
$log_locations = [
    __DIR__ . '/karyawan/error.log',
    __DIR__ . '/error.log',
    ini_get('error_log'),
    '/tmp/php_errors.log',
    'C:/laragon/tmp/php_errors.log'
];

echo "<h3>Checking log locations:</h3>";
foreach ($log_locations as $log_file) {
    if ($log_file && file_exists($log_file)) {
        echo "<p style='color: green;'>✓ Found: $log_file</p>";
        echo "<h4>Last 50 lines:</h4>";
        echo "<pre style='background: #f5f5f5; padding: 15px; border: 1px solid #ddd; max-height: 500px; overflow-y: scroll;'>";
        
        $lines = file($log_file);
        $last_lines = array_slice($lines, -50);
        echo htmlspecialchars(implode('', $last_lines));
        
        echo "</pre>";
        break;
    } else {
        echo "<p style='color: #999;'>✗ Not found: $log_file</p>";
    }
}

// Show PHP error_log configuration
echo "<hr>";
echo "<h3>PHP Error Log Configuration:</h3>";
echo "<pre>";
echo "error_log = " . ini_get('error_log') . "\n";
echo "log_errors = " . ini_get('log_errors') . "\n";
echo "display_errors = " . ini_get('display_errors') . "\n";
echo "</pre>";

// Create a test log entry
error_log("=== TEST LOG ENTRY FROM view_error_log.php ===");
echo "<p>Test log entry created. Check if it appears above.</p>";

echo "<hr>";
echo "<p><a href='karyawan/index.php?m=presensi'>Go to Presensi Page</a></p>";
echo "<p><a href='check_keterangan_table.php'>Check Database</a></p>";
?>
