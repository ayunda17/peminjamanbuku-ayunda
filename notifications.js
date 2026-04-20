// JavaScript untuk Sistem Notifikasi Modern

class NotificationSystem {
    constructor() {
        this.toastContainer = null;
        this.bellIcon = null;
        this.dropdown = null;
        this.notifications = [];
        this.unreadCount = 0;
        this.init();
    }

    init() {
        this.createToastContainer();
        this.createBellIcon();
        this.bindEvents();
        this.loadNotifications();
        this.startPolling();
    }

    createToastContainer() {
        this.toastContainer = document.createElement('div');
        this.toastContainer.className = 'toast-container';
        document.body.appendChild(this.toastContainer);
    }

    createBellIcon() {
        // Cari navbar atau header untuk menempatkan bell icon
        const navbar = document.querySelector('.navbar, .header, nav');
        if (!navbar) return;

        const bellContainer = document.createElement('div');
        bellContainer.className = 'notification-bell';

        bellContainer.innerHTML = `
            <span class="bell-icon" id="notificationBell">
                🔔
                <span class="notification-badge hidden" id="notificationBadge">0</span>
            </span>
            <div class="notification-dropdown" id="notificationDropdown">
                <div class="notification-header">
                    <h4>Notifikasi</h4>
                    <button class="mark-all-read" id="markAllRead">Tandai Semua Dibaca</button>
                </div>
                <div class="notification-list" id="notificationList">
                    <div class="notification-empty">
                        <div class="notification-empty-icon">🔔</div>
                        <div class="notification-empty-text">Belum ada notifikasi</div>
                        <div class="notification-empty-subtext">Notifikasi akan muncul di sini</div>
                    </div>
                </div>
            </div>
        `;

        navbar.appendChild(bellContainer);

        this.bellIcon = document.getElementById('notificationBell');
        this.dropdown = document.getElementById('notificationDropdown');
    }

    bindEvents() {
        if (this.bellIcon) {
            this.bellIcon.addEventListener('click', (e) => {
                e.stopPropagation();
                this.toggleDropdown();
            });
        }

        if (this.dropdown) {
            document.getElementById('markAllRead')?.addEventListener('click', () => {
                this.markAllAsRead();
            });
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (this.dropdown && !this.dropdown.contains(e.target) && e.target !== this.bellIcon) {
                this.hideDropdown();
            }
        });

        // Close dropdown on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.dropdown?.classList.contains('show')) {
                this.hideDropdown();
            }
        });
    }

    async loadNotifications() {
        try {
            const response = await fetch('get_notifications.php?action=get&limit=20&include_read=true');
            const data = await response.json();

            if (data.success) {
                this.notifications = data.notifications;
                this.unreadCount = data.unread_count;
                this.updateUI();
            }
        } catch (error) {
            console.error('Failed to load notifications:', error);
        }
    }

    async loadUnreadCount() {
        try {
            const response = await fetch('get_notifications.php?action=count');
            const data = await response.json();

            if (data.success && data.unread_count !== this.unreadCount) {
                this.unreadCount = data.unread_count;
                this.updateBadge();
            }
        } catch (error) {
            console.error('Failed to load unread count:', error);
        }
    }

    updateUI() {
        this.updateBadge();
        this.updateDropdown();
    }

    updateBadge() {
        const badge = document.getElementById('notificationBadge');
        if (!badge) return;

        if (this.unreadCount > 0) {
            badge.textContent = this.unreadCount > 99 ? '99+' : this.unreadCount;
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    }

    updateDropdown() {
        const list = document.getElementById('notificationList');
        if (!list) return;

        if (this.notifications.length === 0) {
            list.innerHTML = `
                <div class="notification-empty">
                    <div class="notification-empty-icon">🔔</div>
                    <div class="notification-empty-text">Belum ada notifikasi</div>
                    <div class="notification-empty-subtext">Notifikasi akan muncul di sini</div>
                </div>
            `;
            return;
        }

        const html = this.notifications.map(notif => {
            const icon = {
                'success': '✅',
                'error': '❌',
                'warning': '⚠️',
                'info': 'ℹ️'
            }[notif.type] || 'ℹ️';

            return `
                <div class="notification-item ${notif.is_read ? '' : 'unread'}"
                     data-id="${notif.id}"
                     onclick="notificationSystem.markAsRead(${notif.id})">
                    <div class="notification-item-icon">${icon}</div>
                    <div class="notification-item-content">
                        <div class="notification-item-title">${this.escapeHtml(notif.title)}</div>
                        <div class="notification-item-message">${this.escapeHtml(notif.message)}</div>
                        <div class="notification-item-time">${notif.time_ago}</div>
                    </div>
                </div>
            `;
        }).join('');

        list.innerHTML = html;
    }

    async markAsRead(notificationId) {
        try {
            const response = await fetch('mark_as_read.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'mark_one',
                    notification_id: notificationId
                })
            });

            const data = await response.json();

            if (data.success) {
                // Update local data
                const notif = this.notifications.find(n => n.id == notificationId);
                if (notif) {
                    notif.is_read = true;
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                    this.updateUI();
                }
            }
        } catch (error) {
            console.error('Failed to mark notification as read:', error);
        }
    }

    async markAllAsRead() {
        try {
            const response = await fetch('mark_as_read.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'mark_all'
                })
            });

            const data = await response.json();

            if (data.success) {
                // Update local data
                this.notifications.forEach(n => n.is_read = true);
                this.unreadCount = 0;
                this.updateUI();
            }
        } catch (error) {
            console.error('Failed to mark all notifications as read:', error);
        }
    }

    toggleDropdown() {
        if (this.dropdown.classList.contains('show')) {
            this.hideDropdown();
        } else {
            this.showDropdown();
        }
    }

    showDropdown() {
        this.dropdown.classList.add('show');
        this.loadNotifications(); // Refresh notifications when opening
    }

    hideDropdown() {
        this.dropdown.classList.remove('show');
    }

    startPolling() {
        // Poll for new notifications every 30 seconds
        setInterval(() => {
            this.loadUnreadCount();
        }, 30000);
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Global function for showing toast notifications
function showToastNotification(title, message, bgColor = '#333', duration = 4000) {
    if (!window.notificationSystem) {
        window.notificationSystem = new NotificationSystem();
    }

    const container = document.querySelector('.toast-container');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = 'toast-notification';
    toast.style.backgroundColor = bgColor;

    toast.innerHTML = `
        <div class="toast-content">
            <div class="toast-title">${title}</div>
            <div class="toast-message">${message}</div>
        </div>
        <button class="toast-close" onclick="this.parentElement.remove()">×</button>
    `;

    container.appendChild(toast);

    // Trigger animation
    setTimeout(() => {
        toast.classList.add('show');
    }, 10);

    // Auto remove after duration
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => {
            if (toast.parentElement) {
                toast.remove();
            }
        }, 300);
    }, duration);
}

// Initialize notification system when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.notificationSystem = new NotificationSystem();
});

// Export for global use
window.NotificationSystem = NotificationSystem;
window.showToastNotification = showToastNotification;