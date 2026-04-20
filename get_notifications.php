<?php
// API endpoint untuk mengambil notifikasi user via AJAX

require_once 'security_init.php';
require_once 'notifications.php';

// Pastikan user sudah login
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['user_id'];
$action = $_GET['action'] ?? 'get';

header('Content-Type: application/json');

try {
    switch ($action) {
        case 'get':
            // Ambil notifikasi
            $limit = (int) ($_GET['limit'] ?? 10);
            $includeRead = isset($_GET['include_read']) && $_GET['include_read'] === 'true';

            $notifications = getUserNotifications($userId, $limit, $includeRead);
            $unreadCount = getUnreadCount($userId);

            // Format data untuk response
            $formattedNotifications = array_map(function($notif) {
                return [
                    'id' => $notif['id'],
                    'type' => $notif['type'],
                    'title' => $notif['title'],
                    'message' => $notif['message'],
                    'is_read' => (bool) $notif['is_read'],
                    'time_ago' => formatRelativeTime($notif['created_at']),
                    'created_at' => $notif['created_at']
                ];
            }, $notifications);

            echo json_encode([
                'success' => true,
                'notifications' => $formattedNotifications,
                'unread_count' => $unreadCount
            ]);
            break;

        case 'count':
            // Hanya ambil jumlah unread
            $unreadCount = getUnreadCount($userId);
            echo json_encode([
                'success' => true,
                'unread_count' => $unreadCount
            ]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
            break;
    }

} catch (Exception $e) {
    logError("API get_notifications error: " . $e->getMessage(), 'api');
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}
?>