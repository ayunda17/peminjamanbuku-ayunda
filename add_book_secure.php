<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perpustakaan Digital - Aman</title>
    <style>
        /* CSS untuk form yang aman */
        .form-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #f9f9f9;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .error {
            color: #dc3545;
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        .success {
            color: #155724;
            background: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        .btn {
            background: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn:hover {
            background: #0056b3;
        }

        .btn:disabled {
            background: #6c757d;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Tambah Buku Baru</h2>

        <?php
        // Sertakan file keamanan
        require_once 'security_init.php';
        require_once 'database_operations_secure.php';

        // Cek role admin
        if (!isAdmin()) {
            die("Akses ditolak. Halaman ini hanya untuk admin.");
        }

        $errors = [];
        $success = '';

        // Proses form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verifikasi CSRF token
            if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
                $errors[] = "Token keamanan tidak valid. Silakan refresh halaman dan coba lagi.";
            } else {
                // Sanitize input
                $title = sanitizeInput($_POST['title']);
                $author = sanitizeInput($_POST['author']);
                $isbn = sanitizeInput($_POST['isbn']);
                $stock = (int) $_POST['stock'];

                // Panggil fungsi addBook
                $result = addBook($title, $author, $isbn, $stock);

                if ($result['success']) {
                    $success = $result['message'];
                    // Reset form
                    $title = $author = $isbn = '';
                    $stock = 0;
                } else {
                    $errors = $result['errors'];
                }
            }
        }
        ?>

        <?php if (!empty($errors)): ?>
            <div class="error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo escapeOutput($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="success">
                <?php echo escapeOutput($success); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" onsubmit="return validateForm()">
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

            <div class="form-group">
                <label for="title">Judul Buku *</label>
                <input type="text" id="title" name="title" required maxlength="255"
                       value="<?php echo escapeOutput($title ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="author">Penulis *</label>
                <input type="text" id="author" name="author" required maxlength="255"
                       value="<?php echo escapeOutput($author ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="isbn">ISBN *</label>
                <input type="text" id="isbn" name="isbn" required maxlength="20"
                       pattern="[0-9\-]+" title="ISBN hanya boleh berisi angka dan tanda hubung"
                       value="<?php echo escapeOutput($isbn ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="stock">Stok *</label>
                <input type="number" id="stock" name="stock" required min="0" max="1000"
                       value="<?php echo escapeOutput($stock ?? 0); ?>">
            </div>

            <button type="submit" class="btn">Simpan Buku</button>
            <a href="books.php" class="btn" style="background: #6c757d; margin-left: 10px;">Batal</a>
        </form>
    </div>

    <script>
        // Client-side validation
        function validateForm() {
            const title = document.getElementById('title').value.trim();
            const author = document.getElementById('author').value.trim();
            const isbn = document.getElementById('isbn').value.trim();
            const stock = document.getElementById('stock').value;

            if (title.length < 1 || title.length > 255) {
                alert('Judul buku harus diisi dan maksimal 255 karakter');
                return false;
            }

            if (author.length < 1 || author.length > 255) {
                alert('Penulis harus diisi dan maksimal 255 karakter');
                return false;
            }

            if (isbn.length < 10 || isbn.length > 20) {
                alert('ISBN harus diisi dan antara 10-20 karakter');
                return false;
            }

            if (stock < 0 || stock > 1000) {
                alert('Stok harus antara 0-1000');
                return false;
            }

            return true;
        }

        // Auto-format ISBN (hapus karakter non-numeric kecuali dash)
        document.getElementById('isbn').addEventListener('input', function(e) {
            let value = e.target.value.replace(/[^0-9\-]/g, '');
            e.target.value = value;
        });
    </script>
</body>
</html>