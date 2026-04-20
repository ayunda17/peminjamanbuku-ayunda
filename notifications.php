<?php
// Sistem Notifikasi Modern untuk Perpustakaan Digital

// Sertakan file keamanan
require_once 'security_init.php';

/**
 * Simpan notifikasi ke database
 */
function saveNotification($userId, $type, $title, $message) {
    try {
        $conn = getDBConnection();

        $stmt = $conn->prepare("
            INSERT INTO notifications (user_id, type, title, message, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");

        $stmt->execute([$userId, $type, $title, $message]);

        // Log aktivitas
        logActivity('notification_created', "Type: $type, Title: $title", $userId);

        return $conn->lastInsertId();

    } catch (Exception $e) {
        logError("Failed to save notification: " . $e->getMessage(), 'database');
        return false;
    }
}

/**
 * Ambil notifikasi untuk user tertentu
 */
function getUserNotifications($userId, $limit = 10, $includeRead = false) {
    try {
        $conn = getDBConnection();

        $readCondition = $includeRead ? "" : "AND is_read = FALSE";

        $stmt = $conn->prepare("
            SELECT id, type, title, message, is_read, created_at
            FROM notifications
            WHERE (user_id = ? OR user_id IS NULL) $readCondition
            ORDER BY created_at DESC
            LIMIT ?
        ");

        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (Exception $e) {
        logError("Failed to get notifications: " . $e->getMessage(), 'database');
        return [];
    }
}

/**
 * Hitung notifikasi yang belum dibaca
 */
function getUnreadCount($userId) {
    try {
        $conn = getDBConnection();

        $stmt = $conn->prepare("
            SELECT COUNT(*) as count
            FROM notifications
            WHERE (user_id = ? OR user_id IS NULL) AND is_read = FALSE
        ");

        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int) $result['count'];

    } catch (Exception $e) {
        logError("Failed to count unread notifications: " . $e->getMessage(), 'database');
        return 0;
    }
}

/**
 * Tandai notifikasi sebagai sudah dibaca
 */
function markAsRead($notificationId, $userId) {
    try {
        $conn = getDBConnection();

        $stmt = $conn->prepare("
            UPDATE notifications
            SET is_read = TRUE
            WHERE id = ? AND (user_id = ? OR user_id IS NULL)
        ");

        $stmt->execute([$notificationId, $userId]);

        // Log aktivitas
        logActivity('notification_read', "Notification ID: $notificationId", $userId);

        return $stmt->rowCount() > 0;

    } catch (Exception $e) {
        logError("Failed to mark notification as read: " . $e->getMessage(), 'database');
        return false;
    }
}

/**
 * Tandai semua notifikasi sebagai sudah dibaca
 */
function markAllAsRead($userId) {
    try {
        $conn = getDBConnection();

        $stmt = $conn->prepare("
            UPDATE notifications
            SET is_read = TRUE
            WHERE (user_id = ? OR user_id IS NULL) AND is_read = FALSE
        ");

        $stmt->execute([$userId]);

        $count = $stmt->rowCount();

        // Log aktivitas
        logActivity('all_notifications_read', "Marked $count notifications as read", $userId);

        return $count;

    } catch (Exception $e) {
        logError("Failed to mark all notifications as read: " . $e->getMessage(), 'database');
        return 0;
    }
}

/**
 * Hapus notifikasi lama (untuk cleanup)
 */
function cleanupOldNotifications($daysOld = 30) {
    try {
        $conn = getDBConnection();

        $stmt = $conn->prepare("
            DELETE FROM notifications
            WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY) AND is_read = TRUE
        ");

        $stmt->execute([$daysOld]);

        $count = $stmt->rowCount();

        if ($count > 0) {
            logActivity('notifications_cleanup', "Deleted $count old notifications");
        }

        return $count;

    } catch (Exception $e) {
        logError("Failed to cleanup old notifications: " . $e->getMessage(), 'database');
        return 0;
    }
}

/**
 * Format waktu relatif (contoh: "2 menit yang lalu")
 */
function formatRelativeTime($timestamp) {
    $now = time();
    $time = strtotime($timestamp);
    $diff = $now - $time;

    if ($diff < 60) {
        return $diff . ' detik yang lalu';
    } elseif ($diff < 3600) {
        return floor($diff / 60) . ' menit yang lalu';
    } elseif ($diff < 86400) {
        return floor($diff / 3600) . ' jam yang lalu';
    } elseif ($diff < 604800) {
        return floor($diff / 86400) . ' hari yang lalu';
    } else {
        return date('d/m/Y H:i', $time);
    }
}

/**
 * Helper function untuk menampilkan toast notification
 */
function showToast($type, $title, $message, $duration = 4000) {
    $icon = [
        'success' => '✅',
        'error' => '❌',
        'warning' => '⚠️',
        'info' => 'ℹ️'
    ][$type] ?? 'ℹ️';

    $bgColor = [
        'success' => '#10b981',
        'error' => '#ef4444',
        'warning' => '#f59e0b',
        'info' => '#3b82f6'
    ][$type] ?? '#6b7280';

    echo "<script>
        showToastNotification('$icon $title', '$message', '$bgColor', $duration);
    </script>";
}

/**
 * Helper function untuk menyimpan dan menampilkan notifikasi
 */
function createAndShowNotification($userId, $type, $title, $message, $showToast = true) {
    // Simpan ke database
    $notificationId = saveNotification($userId, $type, $title, $message);

    // Tampilkan toast jika diminta
    if ($showToast && $notificationId) {
        showToast($type, $title, $message);
    }

    return $notificationId;
}
?>