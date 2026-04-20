<?php
// Halaman error yang aman
require_once 'error_handling_secure.php';

// Ambil pesan error dari parameter GET (jika ada)
$errorMessage = $_GET['msg'] ?? "Terjadi kesalahan sistem. Silakan coba lagi nanti.";

// Validasi dan sanitasi pesan error
$errorMessage = sanitizeErrorMessage($errorMessage);

// Log error page access
logActivity('error_page_accessed', $errorMessage);

// Tampilkan halaman error
showErrorPage($errorMessage);
?>