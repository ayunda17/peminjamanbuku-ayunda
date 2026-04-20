<?php
// Script untuk setup database tables yang diperlukan untuk keamanan

require_once 'security_init.php';

function setupSecurityTables() {
    $conn = getDBConnection();

    echo "<h1>Security Database Setup</h1>";

    $tables = [
        // Tabel untuk rate limiting
        "rate_limiting" => "
            CREATE TABLE IF NOT EXISTS rate_limiting (
                id INT AUTO_INCREMENT PRIMARY KEY,
                identifier VARCHAR(255) NOT NULL,
                action VARCHAR(100) NOT NULL,
                attempts INT DEFAULT 1,
                first_attempt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                last_attempt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                blocked_until TIMESTAMP NULL,
                INDEX idx_identifier_action (identifier, action),
                INDEX idx_blocked_until (blocked_until)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ",

        // Tabel untuk remember me tokens
        "user_remember_tokens" => "
            CREATE TABLE IF NOT EXISTS user_remember_tokens (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                token VARCHAR(255) NOT NULL,
                expires_at TIMESTAMP NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_user_id (user_id),
                INDEX idx_expires_at (expires_at),
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ",

        // Tabel untuk activity logging
        "activity_logs" => "
            CREATE TABLE IF NOT EXISTS activity_logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NULL,
                action VARCHAR(100) NOT NULL,
                details TEXT,
                ip_address VARCHAR(45),
                user_agent TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_user_id (user_id),
                INDEX idx_action (action),
                INDEX idx_created_at (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ",

        // Tabel untuk failed login attempts
        "failed_login_attempts" => "
            CREATE TABLE IF NOT EXISTS failed_login_attempts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                identifier VARCHAR(255) NOT NULL,
                ip_address VARCHAR(45),
                user_agent TEXT,
                attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_identifier (identifier),
                INDEX idx_ip_address (ip_address),
                INDEX idx_attempted_at (attempted_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ",

        // Tabel untuk security events
        "security_events" => "
            CREATE TABLE IF NOT EXISTS security_events (
                id INT AUTO_INCREMENT PRIMARY KEY,
                event_type VARCHAR(100) NOT NULL,
                severity ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
                description TEXT,
                ip_address VARCHAR(45),
                user_agent TEXT,
                user_id INT NULL,
                additional_data JSON,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_event_type (event_type),
                INDEX idx_severity (severity),
                INDEX idx_created_at (created_at),
                INDEX idx_user_id (user_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        "
    ];

    $created = 0;
    $errors = [];

    foreach ($tables as $tableName => $sql) {
        try {
            $conn->exec($sql);
            echo "✅ Table '$tableName' created successfully<br>";
            $created++;
        } catch (Exception $e) {
            $errorMsg = "Failed to create table '$tableName': " . $e->getMessage();
            echo "❌ $errorMsg<br>";
            $errors[] = $errorMsg;
            logError($errorMsg, 'database');
        }
    }

    echo "<hr>";
    echo "<h3>Setup Summary</h3>";
    echo "Tables created: $created<br>";
    echo "Errors: " . count($errors) . "<br>";

    if (empty($errors)) {
        echo "<p style='color: green; font-weight: bold;'>✅ All security tables created successfully!</p>";

        // Insert sample data untuk testing
        insertSampleSecurityData($conn);
    } else {
        echo "<p style='color: red; font-weight: bold;'>❌ Some tables failed to create. Check errors above.</p>";
    }
}

function insertSampleSecurityData($conn) {
    echo "<h3>Inserting Sample Security Data</h3>";

    try {
        // Sample rate limiting data
        $stmt = $conn->prepare("
            INSERT IGNORE INTO rate_limiting (identifier, action, attempts, first_attempt)
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute(['test@example.com', 'login', 0]);

        echo "✅ Sample rate limiting data inserted<br>";

        // Sample activity log
        $stmt = $conn->prepare("
            INSERT INTO activity_logs (user_id, action, details, ip_address, user_agent)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            null,
            'security_setup',
            'Security tables and initial data setup completed',
            $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            $_SERVER['HTTP_USER_AGENT'] ?? 'Setup Script'
        ]);

        echo "✅ Sample activity log inserted<br>";

    } catch (Exception $e) {
        echo "❌ Failed to insert sample data: " . escapeOutput($e->getMessage()) . "<br>";
        logError("Failed to insert sample security data: " . $e->getMessage(), 'database');
    }
}

function createSecurityDirectories() {
    echo "<h3>Creating Security Directories</h3>";

    $directories = [
        'logs' => 0755,
        'uploads' => 0755,
        'uploads/covers' => 0755,
        'uploads/temp' => 0755,
        'backups' => 0750,
        'cache' => 0755
    ];

    $created = 0;
    $errors = [];

    foreach ($directories as $dir => $permissions) {
        if (!is_dir($dir)) {
            if (mkdir($dir, $permissions, true)) {
                echo "✅ Directory '$dir' created<br>";
                $created++;
            } else {
                $error = "Failed to create directory '$dir'";
                echo "❌ $error<br>";
                $errors[] = $error;
            }
        } else {
            echo "ℹ️ Directory '$dir' already exists<br>";
        }
    }

    // Create .htaccess files for security
    $htaccessContent = "
# Security headers
<IfModule mod_headers.c>
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection \"1; mode=block\"
    Header always set Referrer-Policy \"strict-origin-when-cross-origin\"
</IfModule>

# Prevent access to sensitive files
<FilesMatch \"\.(htaccess|htpasswd|ini|log|sh|sql|conf)$\">
    Order Allow,Deny
    Deny from all
</FilesMatch>

# Prevent PHP execution in uploads
<FilesMatch \.php$>
    Order Deny,Allow
    Deny from all
</FilesMatch>

# Allow only specific file types
<FilesMatch \"\.(jpg|jpeg|png|gif|pdf)$\">
    Order Deny,Allow
    Allow from all
</FilesMatch>
";

    $htaccessFiles = ['uploads/.htaccess', 'uploads/covers/.htaccess'];

    foreach ($htaccessFiles as $file) {
        if (file_put_contents($file, $htaccessContent)) {
            echo "✅ Security .htaccess created in " . dirname($file) . "<br>";
        } else {
            $error = "Failed to create .htaccess in " . dirname($file);
            echo "❌ $error<br>";
            $errors[] = $error;
        }
    }

    echo "<h4>Directory Setup Summary</h4>";
    echo "Directories created: $created<br>";
    echo "Errors: " . count($errors) . "<br>";
}

function generateSecurityConfig() {
    echo "<h3>Generating Security Configuration</h3>";

    $config = [
        'security' => [
            'session_lifetime' => 3600 * 24, // 24 hours
            'max_login_attempts' => 5,
            'lockout_duration' => 900, // 15 minutes
            'password_min_length' => 8,
            'upload_max_size' => 2097152, // 2MB
            'allowed_file_types' => ['image/jpeg', 'image/png', 'image/gif'],
            'rate_limits' => [
                'login' => ['attempts' => 5, 'window' => 900],
                'form' => ['attempts' => 10, 'window' => 60],
                'api' => ['attempts' => 100, 'window' => 60]
            ]
        ],
        'logging' => [
            'enabled' => true,
            'level' => 'warning', // debug, info, warning, error
            'max_files' => 30,
            'max_file_size' => 10485760 // 10MB
        ],
        'backup' => [
            'enabled' => true,
            'frequency' => 'daily', // hourly, daily, weekly
            'retention_days' => 30
        ]
    ];

    $configFile = 'config/security.php';
    if (file_put_contents($configFile, "<?php\nreturn " . var_export($config, true) . ";\n?>")) {
        echo "✅ Security configuration file created<br>";
    } else {
        echo "❌ Failed to create security configuration file<br>";
    }
}

// Run setup if accessed directly
if (basename($_SERVER['PHP_SELF']) === 'setup_security.php') {
    echo "<!DOCTYPE html>
<html lang='id'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Security Setup - Perpustakaan Digital</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
        hr { margin: 20px 0; }
    </style>
</head>
<body>
";

    setupSecurityTables();
    echo "<hr>";
    createSecurityDirectories();
    echo "<hr>";
    generateSecurityConfig();

    echo "
    <hr>
    <h2>Setup Complete</h2>
    <p>If all steps show ✅, your security system is ready!</p>
    <p><a href='security_test.php'>Run Security Tests</a> to verify everything works.</p>
    <p><strong>Important:</strong> Delete this setup file after successful installation.</p>
</body>
</html>
";
} else {
    echo "This script should be run directly.";
}
?>