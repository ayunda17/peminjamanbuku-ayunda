<?php
// Setup script untuk sistem notifikasi

require_once 'security_init.php';
require_once 'notifications.php';

echo "<h1>Setup Sistem Notifikasi Modern</h1>";

try {
    // Baca file SQL
    $sql = file_get_contents('notifications_table.sql');

    if (!$sql) {
        throw new Exception("Tidak dapat membaca file notifications_table.sql");
    }

    // Split SQL commands
    $commands = array_filter(array_map('trim', explode(';', $sql)));

    $conn = getDBConnection();

    echo "<h3>Membuat Tabel Database...</h3>";

    foreach ($commands as $command) {
        if (!empty($command) && !preg_match('/^--/', $command)) {
            try {
                $conn->exec($command);
                echo "✅ Query executed: " . substr($command, 0, 50) . "...<br>";
            } catch (Exception $e) {
                echo "⚠️  Warning: " . $e->getMessage() . "<br>";
            }
        }
    }

    echo "<h3>Insert Sample Data...</h3>";

    // Insert sample notifications
    $sampleNotifications = [
        [null, 'info', 'Selamat Datang', 'Selamat datang di Sistem Perpustakaan Digital!'],
        [null, 'warning', 'Maintenance', 'Sistem akan maintenance pada hari Minggu jam 02:00'],
        [1, 'success', 'Buku Dipinjam', 'Buku "Laskar Pelangi" berhasil dipinjam'],
        [1, 'error', 'Peminjaman Gagal', 'Stok buku "Harry Potter" habis'],
        [1, 'warning', 'Stok Menipis', 'Stok buku "Dilan 1990" tersisa 1 buku'],
        [2, 'success', 'Pengembalian', 'Buku "Cantik Itu Luka" berhasil dikembalikan'],
    ];

    foreach ($sampleNotifications as $notif) {
        $result = saveNotification($notif[0], $notif[1], $notif[2], $notif[3]);
        if ($result) {
            echo "✅ Sample notification created: {$notif[2]}<br>";
        } else {
            echo "❌ Failed to create notification: {$notif[2]}<br>";
        }
    }

    echo "<h3>Test Fungsi...</h3>";

    // Test functions
    $unreadCount = getUnreadCount(1);
    echo "✅ Unread count for user 1: $unreadCount<br>";

    $notifications = getUserNotifications(1, 5, true);
    echo "✅ Retrieved " . count($notifications) . " notifications for user 1<br>";

    echo "<hr>";
    echo "<h2>✅ Setup Berhasil!</h2>";
    echo "<p>Sistem notifikasi telah berhasil di-setup.</p>";
    echo "<p><a href='notification_demo.php'>Buka Halaman Demo</a> untuk testing.</p>";
    echo "<p><strong>PENTING:</strong> Hapus file ini setelah setup selesai untuk alasan keamanan.</p>";

} catch (Exception $e) {
    echo "<h2>❌ Setup Gagal</h2>";
    echo "<p>Error: " . escapeOutput($e->getMessage()) . "</p>";
    echo "<p>Pastikan:</p>";
    echo "<ul>";
    echo "<li>File notifications_table.sql ada di direktori yang sama</li>";
    echo "<li>Koneksi database sudah benar</li>";
    echo "<li>User memiliki permission untuk membuat tabel</li>";
    echo "</ul>";
}
?>