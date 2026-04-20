<?php
echo "<h1>Basic Security Test</h1>";

// Test basic functions without database
require_once 'security_functions.php';

echo "<h2>Test XSS Protection</h2>";
$malicious = "<script>alert('xss')</script>";
$sanitized = sanitizeInput($malicious);
$escaped = escapeOutput($malicious);

echo "Original: $malicious<br>";
echo "Sanitized: $sanitized<br>";
echo "Escaped: $escaped<br>";

if (strpos($escaped, '<script>') === false) {
    echo "✅ XSS protection working<br>";
} else {
    echo "❌ XSS protection failed<br>";
}

echo "<h2>Test Password Security</h2>";
$password = "TestPass123!";
$hashed = hashPassword($password);
$verified = verifyPassword($password, $hashed);

echo "Password hashed: " . substr($hashed, 0, 20) . "...<br>";
echo "Verification: " . ($verified ? "✅ Working" : "❌ Failed") . "<br>";

echo "<h2>Test CSRF Protection</h2>";
$token = generateCSRFToken();
$valid = verifyCSRFToken($token);

echo "Token generated: " . substr($token, 0, 20) . "...<br>";
echo "Token valid: " . ($valid ? "✅ Working" : "❌ Failed") . "<br>";

echo "<h2>Test Complete</h2>";
?>