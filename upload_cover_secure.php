<?php
// Contoh upload file yang aman (untuk cover buku)

// Sertakan file keamanan
require_once 'security_init.php';

// Cek login dan role
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifikasi CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = "Token keamanan tidak valid.";
    } else {
        $bookId = (int) ($_POST['book_id'] ?? 0);

        // Validasi input
        if ($bookId <= 0) {
            $errors[] = "ID buku tidak valid.";
        }

        // Cek apakah buku ada
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT id FROM books WHERE id = ?");
        $stmt->execute([$bookId]);
        if (!$stmt->fetch()) {
            $errors[] = "Buku tidak ditemukan.";
        }

        if (empty($errors)) {
            // Validasi file upload
            if (isset($_FILES['cover']) && $_FILES['cover']['error'] !== UPLOAD_ERR_NO_FILE) {
                $validation = validateFileUpload($_FILES['cover'], ['image/jpeg', 'image/png', 'image/gif'], 2097152); // 2MB max

                if (!$validation['valid']) {
                    $errors[] = $validation['error'];
                } else {
                    // Upload file
                    $uploadResult = uploadFile($_FILES['cover'], 'uploads/covers/', 'book_cover_');

                    if ($uploadResult['success']) {
                        // Update database dengan nama file baru
                        $stmt = $conn->prepare("UPDATE books SET cover_image = ?, updated_at = NOW() WHERE id = ?");
                        $stmt->execute([$uploadResult['filename'], $bookId]);

                        // Hapus file cover lama jika ada
                        if (!empty($_POST['old_cover'])) {
                            $oldFilePath = 'uploads/covers/' . $_POST['old_cover'];
                            if (file_exists($oldFilePath)) {
                                unlink($oldFilePath);
                            }
                        }

                        logActivity('upload_cover', "Upload cover untuk buku ID: $bookId");
                        $success = "Cover buku berhasil diupload.";

                    } else {
                        $errors[] = $uploadResult['error'];
                    }
                }
            } else {
                $errors[] = "Silakan pilih file cover.";
            }
        }
    }
}

// Ambil data buku untuk form
$bookId = (int) ($_GET['book_id'] ?? $_POST['book_id'] ?? 0);
$book = null;

if ($bookId > 0) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT id, title, cover_image FROM books WHERE id = ?");
    $stmt->execute([$bookId]);
    $book = $stmt->fetch();
}

if (!$book) {
    die("Buku tidak ditemukan.");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Cover Buku - <?php echo escapeOutput($book['title']); ?></title>
    <style>
        .upload-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        .preview-image {
            max-width: 200px;
            max-height: 300px;
            border: 1px solid #ddd;
            margin-top: 10px;
        }

        .error { color: #dc3545; background: #f8d7da; padding: 10px; border-radius: 4px; }
        .success { color: #155724; background: #d4edda; padding: 10px; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="upload-container">
        <h2>Upload Cover Buku</h2>
        <h3><?php echo escapeOutput($book['title']); ?></h3>

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

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
            <input type="hidden" name="old_cover" value="<?php echo escapeOutput($book['cover_image'] ?? ''); ?>">

            <div style="margin-bottom: 15px;">
                <label for="cover">Pilih File Cover (JPG, PNG, GIF - Max 2MB):</label><br>
                <input type="file" id="cover" name="cover" accept="image/jpeg,image/png,image/gif" required onchange="previewImage(event)">
            </div>

            <div id="imagePreview" style="display: none;">
                <strong>Preview:</strong><br>
                <img id="previewImg" src="" alt="Preview" class="preview-image">
            </div>

            <button type="submit">Upload Cover</button>
            <a href="books.php">Kembali</a>
        </form>
    </div>

    <script>
        function previewImage(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
            }
        }
    </script>
</body>
</html>