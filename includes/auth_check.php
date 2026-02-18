<?php
// auth_check.php - Include this at the top of all admin pages
session_start();

// Check if user is logged in and is an admin
function checkAdminAuth() {
    if (!isset($_SESSION['username']) || !isset($_SESSION['level']) || $_SESSION['level'] !== 'admin') {
        header("Location: login.php");
        exit();
    }
}

// Call this function at the start of each admin page
checkAdminAuth();
?>