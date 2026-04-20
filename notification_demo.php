<?php
// Contoh halaman yang menggunakan sistem notifikasi modern

require_once 'security_init.php';
require_once 'notifications.php';

// Contoh: Simulasi aksi yang membuat notifikasi
$userId = $_SESSION['user_id'] ?? 1; // Default untuk testing

// Handle form submissions untuk testing
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'add_book':
            createAndShowNotification($userId, 'success', 'Buku Berhasil Ditambahkan',
                'Buku "Laskar Pelangi" telah ditambahkan ke perpustakaan');
            break;

        case 'borrow_book':
            createAndShowNotification($userId, 'success', 'Peminjaman Berhasil',
                'Buku "Harry Potter" berhasil dipinjam. Jangan lupa dikembalikan tepat waktu!');
            break;

        case 'return_book':
            createAndShowNotification($userId, 'info', 'Pengembalian Buku',
                'Buku "Dilan 1990" berhasil dikembalikan. Terima kasih!');
            break;

        case 'low_stock':
            createAndShowNotification($userId, 'warning', 'Stok Menipis',
                'Stok buku "Cantik Itu Luka" tersisa 2 buku. Segera lakukan restock!');
            break;

        case 'overdue':
            createAndShowNotification($userId, 'error', 'Buku Terlambat',
                'Buku "Gone Girl" terlambat dikembalikan. Denda: Rp 5.000');
            break;

        case 'error_example':
            createAndShowNotification($userId, 'error', 'Terjadi Kesalahan',
                'Gagal memproses permintaan. Silakan coba lagi.');
            break;
    }

    // Redirect untuk mencegah resubmission
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demo Sistem Notifikasi Modern - Perpustakaan Digital</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom Notification CSS -->
    <link rel="stylesheet" href="notifications.css">

    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .demo-section {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .btn-demo {
            margin: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-demo:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .notification-types {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .type-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem;
            text-align: center;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .type-card:hover {
            border-color: #007bff;
            background: white;
        }

        .type-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .success { color: #10b981; }
        .error { color: #ef4444; }
        .warning { color: #f59e0b; }
        .info { color: #3b82f6; }
    </style>
</head>
<body>
    <!-- Navbar dengan Bell Icon -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-book"></i> Perpustakaan Digital
            </a>

            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="#">Dashboard</a>
                <a class="nav-link" href="#">Buku</a>
                <a class="nav-link" href="#">Peminjaman</a>
                <!-- Bell icon akan ditambahkan di sini oleh JavaScript -->
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="demo-section">
                    <h2 class="mb-4">
                        <i class="fas fa-bell"></i> Demo Sistem Notifikasi Modern
                    </h2>

                    <p class="text-muted mb-4">
                        Klik tombol di bawah untuk melihat berbagai jenis notifikasi toast.
                        Notifikasi akan muncul di pojok kanan atas dan otomatis hilang setelah 4 detik.
                    </p>

                    <form method="POST" class="mb-4">
                        <div class="notification-types">
                            <div class="type-card success">
                                <div class="type-icon">✅</div>
                                <h5>Buku Ditambahkan</h5>
                                <p>Notifikasi sukses hijau</p>
                                <button type="submit" name="action" value="add_book" class="btn btn-success btn-demo">
                                    <i class="fas fa-plus"></i> Tambah Buku
                                </button>
                            </div>

                            <div class="type-card info">
                                <div class="type-icon">ℹ️</div>
                                <h5>Peminjaman Berhasil</h5>
                                <p>Notifikasi info biru</p>
                                <button type="submit" name="action" value="borrow_book" class="btn btn-primary btn-demo">
                                    <i class="fas fa-hand-holding"></i> Pinjam Buku
                                </button>
                            </div>

                            <div class="type-card info">
                                <div class="type-icon">📚</div>
                                <h5>Pengembalian Buku</h5>
                                <p>Notifikasi info biru</p>
                                <button type="submit" name="action" value="return_book" class="btn btn-info btn-demo">
                                    <i class="fas fa-undo"></i> Kembalikan Buku
                                </button>
                            </div>

                            <div class="type-card warning">
                                <div class="type-icon">⚠️</div>
                                <h5>Stok Menipis</h5>
                                <p>Notifikasi warning kuning</p>
                                <button type="submit" name="action" value="low_stock" class="btn btn-warning btn-demo">
                                    <i class="fas fa-exclamation-triangle"></i> Cek Stok
                                </button>
                            </div>

                            <div class="type-card error">
                                <div class="type-icon">❌</div>
                                <h5>Buku Terlambat</h5>
                                <p>Notifikasi error merah</p>
                                <button type="submit" name="action" value="overdue" class="btn btn-danger btn-demo">
                                    <i class="fas fa-clock"></i> Buku Terlambat
                                </button>
                            </div>

                            <div class="type-card error">
                                <div class="type-icon">🚫</div>
                                <h5>Kesalahan Sistem</h5>
                                <p>Notifikasi error merah</p>
                                <button type="submit" name="action" value="error_example" class="btn btn-danger btn-demo">
                                    <i class="fas fa-times"></i> Error Example
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="alert alert-info">
                        <h5><i class="fas fa-info-circle"></i> Cara Kerja Sistem:</h5>
                        <ul class="mb-0">
                            <li><strong>Toast Notification:</strong> Muncul otomatis setelah aksi, hilang setelah 4 detik</li>
                            <li><strong>Bell Icon:</strong> Klik untuk melihat riwayat notifikasi</li>
                            <li><strong>Badge Counter:</strong> Menampilkan jumlah notifikasi yang belum dibaca</li>
                            <li><strong>Auto-polling:</strong> Mengecek notifikasi baru setiap 30 detik</li>
                            <li><strong>Database Storage:</strong> Semua notifikasi tersimpan dan dapat dilihat kapan saja</li>
                        </ul>
                    </div>
                </div>

                <div class="demo-section">
                    <h4><i class="fas fa-code"></i> Contoh Integrasi Kode</h4>
                    <p>Untuk mengintegrasikan ke halaman existing, tambahkan kode berikut:</p>

                    <div class="row">
                        <div class="col-md-6">
                            <h6>1. Include Files (di setiap halaman):</h6>
                            <pre><code>&lt;!-- CSS --&gt;
&lt;link rel="stylesheet" href="notifications.css"&gt;

&lt;!-- JavaScript --&gt;
&lt;script src="notifications.js"&gt;&lt;/script&gt;</code></pre>
                        </div>

                        <div class="col-md-6">
                            <h6>2. PHP Code (setelah aksi berhasil):</h6>
                            <pre><code>require_once 'notifications.php';

// Setelah tambah buku
createAndShowNotification($user_id, 'success',
    'Buku Berhasil Ditambahkan',
    'Buku baru telah ditambahkan ke katalog');</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom Notification JS -->
    <script src="notifications.js"></script>

    <script>
        // Contoh penggunaan manual toast
        document.addEventListener('DOMContentLoaded', function() {
            // Uncomment untuk test toast manual
            // setTimeout(() => {
            //     showToastNotification('🔔 Test Notifikasi', 'Ini adalah contoh notifikasi toast', '#10b981', 3000);
            // }, 1000);
        });
    </script>
</body>
</html>