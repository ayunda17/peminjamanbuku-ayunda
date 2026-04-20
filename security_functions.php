<?php
// config/database.php - Konfigurasi Database Aman
define('DB_HOST', 'localhost');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_NAME', 'perpustakaan');

// Fungsi koneksi database dengan error handling
function getDBConnection() {
    static $conn = null;

    if ($conn === null) {
        try {
            // Menggunakan PDO untuk keamanan
            $conn = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            // Jangan tampilkan error detail ke user
            error_log("Database connection failed: " . $e->getMessage());
            die("Maaf, terjadi kesalahan sistem. Silakan coba lagi nanti.");
        }
    }

    return $conn;
}

// Fungsi untuk membersihkan input
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Fungsi untuk validasi email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Fungsi untuk generate CSRF token
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Fungsi untuk verifikasi CSRF token
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Fungsi untuk hash password
function hashPassword($password) {
    return password_hash($password, PASSWORD_ARGON2ID, [
        'memory_cost' => 65536,
        'time_cost' => 4,
        'threads' => 3
    ]);
}

// Fungsi untuk verifikasi password
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Fungsi untuk cek login
function checkLogin($username, $password) {
    $conn = getDBConnection();

    // Gunakan prepared statement
    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ? AND status = 'active' LIMIT 1");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && verifyPassword($password, $user['password'])) {
        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['login_time'] = time();

        // Regenerate session ID untuk keamanan
        session_regenerate_id(true);

        return true;
    }

    return false;
}

// Fungsi untuk cek apakah user sudah login
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['login_time']);
}

// Fungsi untuk cek role admin
function isAdmin() {
    return isLoggedIn() && $_SESSION['role'] === 'admin';
}

// Fungsi untuk logout
function logout() {
    // Hapus semua session data
    $_SESSION = [];

    // Hapus cookie session
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }

    // Destroy session
    session_destroy();
}

// Fungsi untuk validasi file upload
function validateFileUpload($file, $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'], $maxSize = 2097152) {
    // Cek apakah file diupload
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['valid' => false, 'error' => 'File tidak valid atau gagal diupload'];
    }

    // Cek ukuran file
    if ($file['size'] > $maxSize) {
        return ['valid' => false, 'error' => 'Ukuran file terlalu besar (max 2MB)'];
    }

    // Cek tipe file
    if (!in_array($file['type'], $allowedTypes)) {
        return ['valid' => false, 'error' => 'Tipe file tidak diizinkan'];
    }

    // Cek ekstensi file
    $fileInfo = pathinfo($file['name']);
    $extension = strtolower($fileInfo['extension']);
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

    if (!in_array($extension, $allowedExtensions)) {
        return ['valid' => false, 'error' => 'Ekstensi file tidak diizinkan'];
    }

    return ['valid' => true];
}

// Fungsi untuk upload file dengan aman
function uploadFile($file, $uploadDir = 'uploads/', $prefix = '') {
    // Generate nama file unik
    $fileInfo = pathinfo($file['name']);
    $extension = strtolower($fileInfo['extension']);
    $newFileName = $prefix . uniqid() . '_' . time() . '.' . $extension;

    $targetPath = $uploadDir . $newFileName;

    // Pastikan direktori upload ada
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Pindahkan file
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return ['success' => true, 'filename' => $newFileName, 'path' => $targetPath];
    } else {
        return ['success' => false, 'error' => 'Gagal mengupload file'];
    }
}

// Fungsi untuk log aktivitas
function logActivity($action, $details = '') {
    $conn = getDBConnection();

    $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, details, ip_address, user_agent, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->execute([
        $_SESSION['user_id'] ?? null,
        $action,
        $details,
        $_SERVER['REMOTE_ADDR'] ?? '',
        $_SERVER['HTTP_USER_AGENT'] ?? ''
    ]);
}

// Fungsi untuk rate limiting
function checkRateLimit($action, $limit = 5, $timeWindow = 300) { // 5 attempts per 5 minutes
    $conn = getDBConnection();
    $ip = $_SERVER['REMOTE_ADDR'];
    $timeLimit = date('Y-m-d H:i:s', time() - $timeWindow);

    // Hitung jumlah attempts dalam time window
    $stmt = $conn->prepare("SELECT COUNT(*) as attempts FROM rate_limits WHERE ip_address = ? AND action = ? AND created_at > ?");
    $stmt->execute([$ip, $action, $timeLimit]);
    $result = $stmt->fetch();

    if ($result['attempts'] >= $limit) {
        return false; // Rate limit exceeded
    }

    // Log attempt
    $stmt = $conn->prepare("INSERT INTO rate_limits (ip_address, action, created_at) VALUES (?, ?, NOW())");
    $stmt->execute([$ip, $action]);

    return true;
}

// Fungsi untuk escape output (XSS protection)
function escapeOutput($data) {
    if (is_array($data)) {
        return array_map('escapeOutput', $data);
    }
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// Fungsi untuk generate random token
function generateToken($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

// Fungsi untuk validasi input umum
function validateInput($input, $rules = []) {
    $errors = [];

    foreach ($rules as $field => $rule) {
        if (!isset($input[$field])) {
            if (strpos($rule, 'required') !== false) {
                $errors[] = "Field $field wajib diisi";
            }
            continue;
        }

        $value = trim($input[$field]);

        if (strpos($rule, 'required') !== false && empty($value)) {
            $errors[] = "Field $field wajib diisi";
        }

        if (strpos($rule, 'email') !== false && !isValidEmail($value)) {
            $errors[] = "Format email $field tidak valid";
        }

        if (strpos($rule, 'numeric') !== false && !is_numeric($value)) {
            $errors[] = "Field $field harus berupa angka";
        }

        if (preg_match('/min:(\d+)/', $rule, $matches)) {
            if (strlen($value) < $matches[1]) {
                $errors[] = "Field $field minimal {$matches[1]} karakter";
            }
        }

        if (preg_match('/max:(\d+)/', $rule, $matches)) {
            if (strlen($value) > $matches[1]) {
                $errors[] = "Field $field maksimal {$matches[1]} karakter";
            }
        }
    }

    return $errors;
}
?>