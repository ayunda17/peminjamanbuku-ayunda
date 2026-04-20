-- Membuat tabel notifications untuk sistem notifikasi modern

CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL, -- NULL untuk notifikasi umum, atau ID user tertentu
    type ENUM('success', 'error', 'warning', 'info') NOT NULL DEFAULT 'info',
    title VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_is_read (is_read),
    INDEX idx_created_at (created_at),
    INDEX idx_type (type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Contoh data untuk testing
INSERT INTO notifications (user_id, type, title, message) VALUES
(NULL, 'info', 'Selamat Datang', 'Selamat datang di Sistem Perpustakaan Digital'),
(NULL, 'warning', 'Maintenance', 'Sistem akan maintenance pada hari Minggu jam 02:00'),
(1, 'success', 'Buku Dipinjam', 'Buku "Laskar Pelangi" berhasil dipinjam'),
(1, 'error', 'Peminjaman Gagal', 'Stok buku "Harry Potter" habis');