<?php
require_once("includes/auth_check.php");
require_once("koneksi.php");

$username = $_SESSION['username'] ?? '';
$admin_id = $_SESSION['admin_id'] ?? '';

// Get any success/error messages
$success_msg = $_SESSION['success_msg'] ?? '';
$error_msg = $_SESSION['error_msg'] ?? '';

// Clear messages after retrieving them
unset($_SESSION['success_msg']);
unset($_SESSION['error_msg']);
?>