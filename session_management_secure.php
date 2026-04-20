<?php
// Session management yang aman

// Sertakan file keamanan
require_once 'security_init.php';

// Konfigurasi session yang aman
function configureSecureSession() {
    // Set session cookie parameters
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params([
        'lifetime' => 0, // Session cookie (expires when browser closes)
        'path' => '/',
        'domain' => '', // Current domain only
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on', // HTTPS only in production
        'httponly' => true, // Prevent JavaScript access
        'samesite' => 'Strict' // CSRF protection
    ]);

    // Set session configuration
    ini_set('session.use_strict_mode', 1); // Prevent session fixation
    ini_set('session.use_only_cookies', 1); // No URL-based sessions
    ini_set('session.cookie_httponly', 1); // HTTP only cookies
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 1 : 0);
    ini_set('session.gc_maxlifetime', 3600); // 1 hour session lifetime
    ini_set('session.gc_probability', 1);
    ini_set('session.gc_divisor', 100);

    // Custom session save path (lebih aman)
    $sessionPath = sys_get_temp_dir() . '/secure_sessions';
    if (!is_dir($sessionPath)) {
        mkdir($sessionPath, 0700, true);
    }
    ini_set('session.save_path', $sessionPath);
}

// Mulai session dengan konfigurasi aman
function startSecureSession() {
    configureSecureSession();

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Regenerate session ID secara periodik untuk mencegah session hijacking
    if (!isset($_SESSION['last_regeneration'])) {
        $_SESSION['last_regeneration'] = time();
    } elseif (time() - $_SESSION['last_regeneration'] > 300) { // 5 menit
        regenerateSecureSession();
    }

    // Validate session integrity
    validateSessionIntegrity();
}

// Regenerate session ID dengan aman
function regenerateSecureSession() {
    // Simpan data session penting
    $userId = $_SESSION['user_id'] ?? null;
    $userRole = $_SESSION['user_role'] ?? null;
    $csrfToken = $_SESSION['csrf_token'] ?? null;

    // Regenerate session ID
    session_regenerate_id(true);

    // Restore data penting
    if ($userId) $_SESSION['user_id'] = $userId;
    if ($userRole) $_SESSION['user_role'] = $userRole;
    if ($csrfToken) $_SESSION['csrf_token'] = $csrfToken;

    $_SESSION['last_regeneration'] = time();

    logActivity('session_regenerated', 'Session ID regenerated for security');
}

// Validasi integritas session
function validateSessionIntegrity() {
    // Cek IP address (opsional, bisa dinonaktifkan untuk mobile users)
    if (isset($_SESSION['client_ip'])) {
        $currentIP = $_SERVER['REMOTE_ADDR'] ?? '';
        if ($_SESSION['client_ip'] !== $currentIP) {
            // IP berubah, mungkin session hijacking
            logActivity('session_suspicious', 'IP address changed during session');
            destroySecureSession();
            header('Location: login.php?msg=session_expired');
            exit;
        }
    } else {
        $_SESSION['client_ip'] = $_SERVER['REMOTE_ADDR'] ?? '';
    }

    // Cek User Agent (opsional)
    if (isset($_SESSION['user_agent'])) {
        $currentUA = $_SERVER['HTTP_USER_AGENT'] ?? '';
        if ($_SESSION['user_agent'] !== $currentUA) {
            // User Agent berubah, mungkin session hijacking
            logActivity('session_suspicious', 'User Agent changed during session');
            destroySecureSession();
            header('Location: login.php?msg=session_expired');
            exit;
        }
    } else {
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
    }

    // Cek session expiry
    if (isset($_SESSION['login_time'])) {
        $sessionLifetime = 3600 * 24; // 24 jam
        if (time() - $_SESSION['login_time'] > $sessionLifetime) {
            destroySecureSession();
            header('Location: login.php?msg=session_expired');
            exit;
        }
    }
}

// Destroy session dengan aman
function destroySecureSession() {
    // Hapus semua data session
    $_SESSION = [];

    // Hapus cookie session
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }

    // Destroy session
    session_destroy();

    logActivity('session_destroyed', 'Session destroyed');
}

// Login dengan session aman
function loginUser($userId, $userRole = 'user') {
    startSecureSession();

    // Regenerate session ID saat login
    session_regenerate_id(true);

    // Set session data
    $_SESSION['user_id'] = $userId;
    $_SESSION['user_role'] = $userRole;
    $_SESSION['login_time'] = time();
    $_SESSION['last_activity'] = time();
    $_SESSION['client_ip'] = $_SERVER['REMOTE_ADDR'] ?? '';
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';

    // Generate CSRF token
    $_SESSION['csrf_token'] = generateCSRFToken();

    logActivity('user_login', "User ID: $userId logged in");
}

// Logout dengan aman
function logoutUser() {
    $userId = $_SESSION['user_id'] ?? 'unknown';
    logActivity('user_logout', "User ID: $userId logged out");

    destroySecureSession();
}

// Cek apakah user sudah login
function isLoggedIn() {
    startSecureSession();

    if (!isset($_SESSION['user_id']) || !isset($_SESSION['login_time'])) {
        return false;
    }

    // Cek session expiry berdasarkan aktivitas
    $inactiveLimit = 1800; // 30 menit
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $inactiveLimit)) {
        destroySecureSession();
        return false;
    }

    // Update last activity
    $_SESSION['last_activity'] = time();

    return true;
}

// Cek role user
function hasRole($requiredRole) {
    if (!isLoggedIn()) {
        return false;
    }

    $userRole = $_SESSION['user_role'] ?? 'user';

    // Role hierarchy (admin > moderator > user)
    $roleHierarchy = [
        'user' => 1,
        'moderator' => 2,
        'admin' => 3
    ];

    $userLevel = $roleHierarchy[$userRole] ?? 0;
    $requiredLevel = $roleHierarchy[$requiredRole] ?? 999;

    return $userLevel >= $requiredLevel;
}

// Require login untuk halaman tertentu
function requireLogin($redirectUrl = 'login.php') {
    if (!isLoggedIn()) {
        header("Location: $redirectUrl");
        exit;
    }
}

// Require role untuk halaman tertentu
function requireRole($role, $redirectUrl = 'login.php') {
    requireLogin($redirectUrl);

    if (!hasRole($role)) {
        header("Location: $redirectUrl?msg=insufficient_permissions");
        exit;
    }
}

// Get current user info
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }

    return [
        'id' => $_SESSION['user_id'],
        'role' => $_SESSION['user_role'],
        'login_time' => $_SESSION['login_time'],
        'last_activity' => $_SESSION['last_activity']
    ];
}

// Session cleanup untuk admin (hapus session expired)
function cleanupExpiredSessions() {
    // Ini bisa dipanggil secara periodik via cron job
    $sessionPath = ini_get('session.save_path');
    if ($sessionPath && is_dir($sessionPath)) {
        $files = glob($sessionPath . '/sess_*');
        $expiredCount = 0;

        foreach ($files as $file) {
            if (is_file($file)) {
                $fileAge = time() - filemtime($file);
                if ($fileAge > 3600 * 24 * 7) { // 7 hari
                    unlink($file);
                    $expiredCount++;
                }
            }
        }

        if ($expiredCount > 0) {
            logActivity('session_cleanup', "Cleaned up $expiredCount expired session files");
        }
    }
}

// Fungsi untuk remember me functionality (optional, dengan cookie aman)
function setRememberMeToken($userId) {
    $token = bin2hex(random_bytes(32));
    $expires = time() + (30 * 24 * 60 * 60); // 30 hari

    // Simpan token di database (hashed)
    $conn = getDBConnection();
    $hashedToken = password_hash($token, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("
        INSERT INTO user_remember_tokens (user_id, token, expires_at)
        VALUES (?, ?, FROM_UNIXTIME(?))
        ON DUPLICATE KEY UPDATE token = VALUES(token), expires_at = VALUES(expires_at)
    ");
    $stmt->execute([$userId, $hashedToken, $expires]);

    // Set cookie yang aman
    setcookie('remember_token', $token, [
        'expires' => $expires,
        'path' => '/',
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
}

// Fungsi untuk auto-login dari remember me token
function autoLoginFromRememberToken() {
    if (isset($_COOKIE['remember_token']) && !isLoggedIn()) {
        $token = $_COOKIE['remember_token'];

        $conn = getDBConnection();
        $stmt = $conn->prepare("
            SELECT user_id FROM user_remember_tokens
            WHERE token = ? AND expires_at > NOW()
        ");

        // Cek semua token yang valid
        $stmt2 = $conn->prepare("SELECT user_id FROM user_remember_tokens WHERE expires_at > NOW()");
        $stmt2->execute();
        $validTokens = $stmt2->fetchAll();

        foreach ($validTokens as $row) {
            if (password_verify($token, $row['token'])) {
                // Token valid, login user
                $userStmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
                $userStmt->execute([$row['user_id']]);
                $user = $userStmt->fetch();

                if ($user) {
                    loginUser($row['user_id'], $user['role']);
                    logActivity('auto_login', "User auto-logged in via remember token");
                    return true;
                }
            }
        }

        // Token tidak valid, hapus cookie
        setcookie('remember_token', '', time() - 3600, '/');
    }

    return false;
}
?>