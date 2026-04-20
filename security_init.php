<?php
// includes/security.php - Sertakan di semua file PHP
require_once 'security_functions.php';

// Start session dengan pengaturan aman
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1); // Jika menggunakan HTTPS
ini_set('session.use_only_cookies', 1);
session_start();

// Regenerate session ID secara berkala untuk mencegah session fixation
if (!isset($_SESSION['last_regeneration']) || time() - $_SESSION['last_regeneration'] > 300) {
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
}

// Cek login untuk halaman yang memerlukan autentikasi
if (!isLoggedIn() && basename($_SERVER['PHP_SELF']) !== 'login.php') {
    header('Location: login.php');
    exit;
}
?>