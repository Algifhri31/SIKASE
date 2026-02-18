<?php
// Database setup script
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'koneksi.php';

// Read SQL file
$sql = file_get_contents('setup_database.sql');

// Execute SQL commands
try {
    $queries = explode(';', $sql);
    foreach ($queries as $query) {
        if (trim($query) != '') {
            $result = mysqli_query($koneksi, $query);
            if (!$result) {
                throw new Exception(mysqli_error($koneksi));
            }
        }
    }
    echo "Database setup completed successfully!";
} catch (Exception $e) {
    echo "Error setting up database: " . $e->getMessage();
}
?>