<?php
// Contoh implementasi operasi database yang aman

// 1. LOGIN dengan rate limiting dan prepared statement
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!checkRateLimit('login', 5, 300)) { // 5 attempts per 5 minutes
        $error = "Terlalu banyak percobaan login. Silakan tunggu 5 menit.";
    } else {
        $username = sanitizeInput($_POST['username']);
        $password = $_POST['password'];

        $errors = validateInput($_POST, [
            'username' => 'required|min:3|max:50',
            'password' => 'required|min:6'
        ]);

        if (empty($errors)) {
            if (checkLogin($username, $password)) {
                logActivity('login', "Login berhasil untuk user: $username");
                header('Location: dashboard.php');
                exit;
            } else {
                logActivity('login_failed', "Login gagal untuk user: $username");
                $error = "Username atau password salah";
            }
        } else {
            $error = implode('<br>', $errors);
        }
    }
}
?>

<!-- Form login dengan CSRF protection -->
<form method="POST" action="">
    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

    <div class="form-group">
        <label>Username:</label>
        <input type="text" name="username" required maxlength="50">
    </div>

    <div class="form-group">
        <label>Password:</label>
        <input type="password" name="password" required>
    </div>

    <button type="submit">Login</button>

    <?php if (isset($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>
</form>