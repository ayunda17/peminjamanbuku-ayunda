<?php
// API endpoint untuk menandai notifikasi sebagai sudah dibaca

require_once 'security_init.php';
require_once 'notifications.php';

// Pastikan user sudah login
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['user_id'];

header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? $_POST['action'] ?? 'mark_one';

    switch ($action) {
        case 'mark_one':
            // Tandai satu notifikasi
            $notificationId = (int) ($input['notification_id'] ?? $_POST['notification_id'] ?? 0);

            if ($notificationId <= 0) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid notification ID']);
                exit;
            }

            $success = markAsRead($notificationId, $userId);

            echo json_encode([
                'success' => $success,
                'message' => $success ? 'Notification marked as read' : 'Failed to mark notification'
            ]);
            break;

        case 'mark_all':
            // Tandai semua notifikasi
            $count = markAllAsRead($userId);

            echo json_encode([
                'success' => true,
                'message' => "Marked $count notifications as read",
                'count' => $count
            ]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
            break;
    }

} catch (Exception $e) {
    logError("API mark_as_read error: " . $e->getMessage(), 'api');
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}
?>