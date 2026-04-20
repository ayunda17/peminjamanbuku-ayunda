<?php
// Error handling yang aman untuk aplikasi

// Sertakan file keamanan
require_once 'security_init.php';

// Fungsi untuk menangani error dengan aman
function handleError($errno, $errstr, $errfile, $errline) {
    // Log error ke file (jangan tampilkan ke user)
    $errorMessage = sprintf(
        "[%s] Error %d: %s in %s on line %d\n",
        date('Y-m-d H:i:s'),
        $errno,
        $errstr,
        $errfile,
        $errline
    );

    error_log($errorMessage, 3, 'logs/error.log');

    // Jangan tampilkan error detail ke user
    if (ini_get('display_errors')) {
        echo "Terjadi kesalahan sistem. Silakan coba lagi nanti.";
        exit;
    }
}

// Fungsi untuk menangani exception
function handleException($exception) {
    // Log exception
    $errorMessage = sprintf(
        "[%s] Exception: %s in %s on line %d\nStack trace:\n%s\n",
        date('Y-m-d H:i:s'),
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine(),
        $exception->getTraceAsString()
    );

    error_log($errorMessage, 3, 'logs/exception.log');

    // Redirect ke halaman error yang aman
    header('Location: error.php');
    exit;
}

// Fungsi untuk menangani fatal error
function handleShutdown() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        // Log fatal error
        $errorMessage = sprintf(
            "[%s] Fatal Error: %s in %s on line %d\n",
            date('Y-m-d H:i:s'),
            $error['message'],
            $error['file'],
            $error['line']
        );

        error_log($errorMessage, 3, 'logs/fatal.log');

        // Redirect ke halaman error
        header('Location: error.php');
        exit;
    }
}

// Set error handlers
set_error_handler('handleError');
set_exception_handler('handleException');
register_shutdown_function('handleShutdown');

// Pastikan folder logs ada
if (!is_dir('logs')) {
    mkdir('logs', 0755, true);
}

// Fungsi untuk halaman error yang aman
function showErrorPage($message = "Terjadi kesalahan sistem. Silakan coba lagi nanti.") {
    http_response_code(500);
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Kesalahan Sistem</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                text-align: center;
                padding: 50px;
                background-color: #f5f5f5;
            }
            .error-container {
                max-width: 600px;
                margin: 0 auto;
                background: white;
                padding: 30px;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }
            .error-icon {
                font-size: 48px;
                color: #dc3545;
                margin-bottom: 20px;
            }
            .error-message {
                color: #333;
                font-size: 18px;
                margin-bottom: 20px;
            }
            .error-actions {
                margin-top: 30px;
            }
            .error-actions a {
                display: inline-block;
                padding: 10px 20px;
                background: #007bff;
                color: white;
                text-decoration: none;
                border-radius: 4px;
                margin: 0 10px;
            }
            .error-actions a:hover {
                background: #0056b3;
            }
        </style>
    </head>
    <body>
        <div class="error-container">
            <div class="error-icon">⚠️</div>
            <h1>Kesalahan Sistem</h1>
            <p class="error-message"><?php echo escapeOutput($message); ?></p>
            <div class="error-actions">
                <a href="javascript:history.back()">Kembali</a>
                <a href="index.php">Beranda</a>
                <a href="contact.php">Hubungi Admin</a>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Fungsi untuk  dan sanitasi error messages
function sanitizeErrorMessage($message) {
    // Hapus informasi sensitif dari error message
    $patterns = [
        '/(SELECT|INSERT|UPDATE|DELETE|DROP|CREATE|ALTER)/i', // SQL keywords
        '/(password|token|key|secret)/i', // Sensitive data
        '/(file:\/\/|\/var\/|\/etc\/|\/home\/)/i', // File paths
        '/(\$[a-zA-Z_][a-zA-Z0-9_]*)/', // Variable names
    ];

    foreach ($patterns as $pattern) {
        $message = preg_replace($pattern, '[REDACTED]', $message);
    }

    return escapeOutput($message);
}

// Fungsi untuk rate limiting error logging (mencegah log spam)
class ErrorRateLimiter {
    private static $errorCounts = [];
    private static $lastReset = 0;

    public static function canLogError($errorType) {
        $currentTime = time();

        // Reset counter setiap menit
        if ($currentTime - self::$lastReset > 60) {
            self::$errorCounts = [];
            self::$lastReset = $currentTime;
        }

        // Limit 10 error per menit per type
        if (!isset(self::$errorCounts[$errorType])) {
            self::$errorCounts[$errorType] = 0;
        }

        if (self::$errorCounts[$errorType] < 10) {
            self::$errorCounts[$errorType]++;
            return true;
        }

        return false;
    }
}

// Fungsi untuk log error dengan rate limiting
function logError($message, $type = 'general') {
    if (ErrorRateLimiter::canLogError($type)) {
        error_log("[$type] " . sanitizeErrorMessage($message), 3, 'logs/error.log');
    }
}

// Fungsi untuk log aktivitas yang aman
function logActivity($action, $details = '', $userId = null) {
    $userId = $userId ?? ($_SESSION['user_id'] ?? 'unknown');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $userAgent = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 200);

    $logMessage = sprintf(
        "[%s] User: %s, IP: %s, Action: %s, Details: %s, UA: %s\n",
        date('Y-m-d H:i:s'),
        $userId,
        $ip,
        $action,
        sanitizeErrorMessage($details),
        $userAgent
    );

    error_log($logMessage, 3, 'logs/activity.log');
}

// Fungsi untuk validasi database connection
function validateDBConnection($conn) {
    try {
        $conn->query('SELECT 1');
        return true;
    } catch (Exception $e) {
        logError("Database connection failed: " . $e->getMessage(), 'database');
        return false;
    }
}

// Fungsi untuk rollback database transaction dengan aman
function safeRollback($conn) {
    try {
        if ($conn->inTransaction()) {
            $conn->rollBack();
            logActivity('transaction_rollback', 'Transaction rolled back due to error');
        }
    } catch (Exception $e) {
        logError("Failed to rollback transaction: " . $e->getMessage(), 'database');
    }
}
?>