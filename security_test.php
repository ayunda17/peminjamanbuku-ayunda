<?php
// File testing untuk memverifikasi implementasi keamanan

require_once 'security_init.php';
require_once 'rate_limiting_secure.php';

// Test 1: SQL Injection Protection
function testSQLInjectionProtection() {
    echo "<h3>Test SQL Injection Protection</h3>";

    $conn = getDBConnection();

    // Test dengan input berbahaya
    $maliciousInput = "'; DROP TABLE users; --";

    try {
        // Query aman dengan prepared statement
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$maliciousInput]);
        $result = $stmt->fetch();

        if ($result === false) {
            echo "✅ SQL Injection test passed - No user found (expected)<br>";
        } else {
            echo "❌ SQL Injection test failed - Unexpected result<br>";
        }

        // Verifikasi tabel masih ada
        $stmt = $conn->query("SHOW TABLES LIKE 'users'");
        if ($stmt->fetch()) {
            echo "✅ Table 'users' still exists after malicious input<br>";
        } else {
            echo "❌ Table 'users' was dropped - SQL Injection vulnerability!<br>";
        }

    } catch (Exception $e) {
        echo "❌ SQL Injection test error: " . escapeOutput($e->getMessage()) . "<br>";
    }
}

// Test 2: XSS Protection
function testXSSProtection() {
    echo "<h3>Test XSS Protection</h3>";

    $maliciousScript = "<script>alert('XSS Attack!')</script>";
    $maliciousLink = "<a href=\"javascript:alert('XSS')\">Click me</a>";

    $sanitizedScript = sanitizeInput($maliciousScript);
    $escapedScript = escapeOutput($maliciousScript);

    echo "Original: " . escapeOutput($maliciousScript) . "<br>";
    echo "Sanitized: " . escapeOutput($sanitizedScript) . "<br>";
    echo "Escaped: " . $escapedScript . "<br>";

    // Test apakah script masih executable
    if (strpos($escapedScript, '<script>') === false) {
        echo "✅ XSS protection working - script tags escaped<br>";
    } else {
        echo "❌ XSS protection failed - script tags not escaped<br>";
    }
}

// Test 3: CSRF Protection
function testCSRFProtection() {
    echo "<h3>Test CSRF Protection</h3>";

    // Generate token
    $token1 = generateCSRFToken();
    $token2 = generateCSRFToken();

    echo "Token 1: " . escapeOutput($token1) . "<br>";
    echo "Token 2: " . escapeOutput($token2) . "<br>";

    // Test token verification
    if (verifyCSRFToken($token1)) {
        echo "✅ CSRF token verification working<br>";
    } else {
        echo "❌ CSRF token verification failed<br>";
    }

    // Test dengan token salah
    if (!verifyCSRFToken('invalid_token')) {
        echo "✅ Invalid CSRF token rejected<br>";
    } else {
        echo "❌ Invalid CSRF token accepted - vulnerability!<br>";
    }
}

// Test 4: Password Security
function testPasswordSecurity() {
    echo "<h3>Test Password Security</h3>";

    $weakPassword = "123456";
    $strongPassword = "MySecureP@ssw0rd2024!";

    // Test password strength
    $weakValidation = validatePasswordStrength($weakPassword);
    $strongValidation = validatePasswordStrength($strongPassword);

    echo "Weak password validation: " . ($weakValidation['valid'] ? 'Valid' : 'Invalid') . "<br>";
    echo "Strong password validation: " . ($strongValidation['valid'] ? 'Valid' : 'Invalid') . "<br>";

    if (!$weakValidation['valid'] && $strongValidation['valid']) {
        echo "✅ Password strength validation working<br>";
    } else {
        echo "❌ Password strength validation failed<br>";
    }

    // Test hashing
    $hashed = hashPassword($strongPassword);
    $verifyResult = verifyPassword($strongPassword, $hashed);

    if ($verifyResult) {
        echo "✅ Password hashing and verification working<br>";
    } else {
        echo "❌ Password hashing/verification failed<br>";
    }

    // Test wrong password
    $wrongVerify = verifyPassword("wrongpassword", $hashed);
    if (!$wrongVerify) {
        echo "✅ Wrong password correctly rejected<br>";
    } else {
        echo "❌ Wrong password incorrectly accepted<br>";
    }
}

// Test 5: Input Validation
function testInputValidation() {
    echo "<h3>Test Input Validation</h3>";

    // Test email validation
    $validEmails = ["user@example.com", "test.email+tag@gmail.com"];
    $invalidEmails = ["invalid-email", "@example.com", "user@"];

    foreach ($validEmails as $email) {
        if (validateEmail($email)) {
            echo "✅ Valid email accepted: " . escapeOutput($email) . "<br>";
        } else {
            echo "❌ Valid email rejected: " . escapeOutput($email) . "<br>";
        }
    }

    foreach ($invalidEmails as $email) {
        if (!validateEmail($email)) {
            echo "✅ Invalid email rejected: " . escapeOutput($email) . "<br>";
        } else {
            echo "❌ Invalid email accepted: " . escapeOutput($email) . "<br>";
        }
    }

    // Test input sanitization
    $maliciousInput = "<b>Bold text</b> <script>alert('xss')</script>";
    $sanitized = sanitizeInput($maliciousInput);

    echo "Original input: " . escapeOutput($maliciousInput) . "<br>";
    echo "Sanitized input: " . escapeOutput($sanitized) . "<br>";

    if (strpos($sanitized, '<script>') === false && strpos($sanitized, '<b>') === false) {
        echo "✅ Input sanitization working<br>";
    } else {
        echo "❌ Input sanitization failed<br>";
    }
}

// Test 6: Rate Limiting
function testRateLimiting() {
    echo "<h3>Test Rate Limiting</h3>";

    $identifier = 'test_user_' . time();
    $limiter = new RateLimiter(3, 60); // 3 attempts per minute

    // Test normal usage
    for ($i = 1; $i <= 3; $i++) {
        $allowed = $limiter->isAllowed($identifier, 'test_action');
        echo "Attempt $i: " . ($allowed ? 'Allowed' : 'Blocked') . "<br>";

        if ($allowed) {
            $limiter->recordAttempt($identifier, 'test_action');
        }
    }

    // Test blocking
    $blocked = $limiter->isAllowed($identifier, 'test_action');
    echo "After limit exceeded: " . ($blocked ? 'Still allowed (error)' : 'Correctly blocked') . "<br>";

    if (!$blocked) {
        echo "✅ Rate limiting working<br>";
    } else {
        echo "❌ Rate limiting failed<br>";
    }
}

// Test 7: Session Security
function testSessionSecurity() {
    echo "<h3>Test Session Security</h3>";

    // Test session start
    startSecureSession();

    if (isset($_SESSION)) {
        echo "✅ Secure session started<br>";
    } else {
        echo "❌ Session failed to start<br>";
    }

    // Test CSRF token generation
    if (isset($_SESSION['csrf_token']) && !empty($_SESSION['csrf_token'])) {
        echo "✅ CSRF token generated in session<br>";
    } else {
        echo "❌ CSRF token not generated<br>";
    }

    // Test login simulation
    loginUser(1, 'admin');

    if (isLoggedIn() && hasRole('admin')) {
        echo "✅ User login and role check working<br>";
    } else {
        echo "❌ User login or role check failed<br>";
    }

    // Test logout
    logoutUser();

    if (!isLoggedIn()) {
        echo "✅ User logout working<br>";
    } else {
        echo "❌ User logout failed<br>";
    }
}

// Test 8: File Upload Security
function testFileUploadSecurity() {
    echo "<h3>Test File Upload Security</h3>";

    // Create test upload directory
    $testDir = 'test_uploads';
    if (!is_dir($testDir)) {
        mkdir($testDir, 0755, true);
    }

    // Test valid file
    $validFile = [
        'name' => 'test_image.jpg',
        'type' => 'image/jpeg',
        'tmp_name' => tempnam(sys_get_temp_dir(), 'test'),
        'error' => UPLOAD_ERR_OK,
        'size' => 1024
    ];

    // Create dummy file
    file_put_contents($validFile['tmp_name'], 'dummy image content');

    $validation = validateFileUpload($validFile, ['image/jpeg', 'image/png'], 2048);

    if ($validation['valid']) {
        echo "✅ Valid file upload accepted<br>";
    } else {
        echo "❌ Valid file upload rejected: " . escapeOutput($validation['error']) . "<br>";
    }

    // Test invalid file type
    $invalidFile = $validFile;
    $invalidFile['name'] = 'malicious.php';
    $invalidFile['type'] = 'text/php';

    $validation2 = validateFileUpload($invalidFile, ['image/jpeg'], 2048);

    if (!$validation2['valid']) {
        echo "✅ Invalid file type rejected<br>";
    } else {
        echo "❌ Invalid file type accepted - vulnerability!<br>";
    }

    // Cleanup
    unlink($validFile['tmp_name']);
    rmdir($testDir);
}

// Test 9: Error Handling
function testErrorHandling() {
    echo "<h3>Test Error Handling</h3>";

    // Test error logging
    logActivity('test_action', 'Testing error logging functionality');

    if (file_exists('logs/activity.log')) {
        echo "✅ Activity logging working<br>";
    } else {
        echo "❌ Activity logging failed<br>";
    }

    // Test error sanitization
    $sensitiveError = "Database error: SELECT * FROM users WHERE password = 'secret'";
    $sanitized = sanitizeErrorMessage($sensitiveError);

    echo "Original error: " . escapeOutput($sensitiveError) . "<br>";
    echo "Sanitized error: " . escapeOutput($sanitized) . "<br>";

    if (strpos($sanitized, 'secret') === false && strpos($sanitized, 'SELECT') === false) {
        echo "✅ Error message sanitization working<br>";
    } else {
        echo "❌ Error message sanitization failed<br>";
    }
}

// Run all tests
function runSecurityTests() {
    echo "<h1>Security Implementation Test Suite</h1>";
    echo "<p>Running comprehensive security tests...</p>";

    testSQLInjectionProtection();
    echo "<hr>";

    testXSSProtection();
    echo "<hr>";

    testCSRFProtection();
    echo "<hr>";

    testPasswordSecurity();
    echo "<hr>";

    testInputValidation();
    echo "<hr>";

    testRateLimiting();
    echo "<hr>";

    testSessionSecurity();
    echo "<hr>";

    testFileUploadSecurity();
    echo "<hr>";

    testErrorHandling();
    echo "<hr>";

    echo "<h2>Test Suite Complete</h2>";
    echo "<p>Review the results above. All tests should show ✅ for proper security implementation.</p>";
}

// Only run tests if accessed directly
if (basename($_SERVER['PHP_SELF']) === 'security_test.php') {
    runSecurityTests();
}
?>