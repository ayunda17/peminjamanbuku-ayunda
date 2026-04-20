<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Sistem Peminjaman Buku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* ========================================
           Root Variables - Elegant Color Palette
           ======================================== */
        :root {
            --primary: #1e3a5f;
            --secondary: #0d9488;
            --accent: #d4af37;
            --light-bg: #f8f6f1;
            --white: #ffffff;
            --text-dark: #0f172a;
            --text-muted: #4b5563;
            --shadow: 0 10px 40px rgba(30, 58, 95, 0.12);
            --shadow-lg: 0 20px 60px rgba(30, 58, 95, 0.15);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--light-bg);
            color: var(--text-dark);
            overflow-x: hidden;
            line-height: 1.6;
        }

        .layout-container {
            display: flex;
            min-height: 100vh;
        }

        /* ========================================
           Sidebar Styles
           ======================================== */
        .sidebar {
            width: 260px;
            background: linear-gradient(180deg, var(--primary) 0%, #2d5a7b 100%);
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            z-index: 1000;
            overflow-y: auto;
            transition: transform 0.3s ease;
            box-shadow: var(--shadow);
        }

        .sidebar-header {
            padding: 1.75rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(0, 0, 0, 0.2);
        }

        .sidebar-header h3 {
            color: var(--accent);
            font-size: 1.3rem;
            font-weight: 900;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            letter-spacing: -0.5px;
        }

        .sidebar-header i {
            font-size: 1.5rem;
        }

        .sidebar-nav {
            padding: 1.5rem 0;
        }

        .nav-item {
            margin: 0.25rem 0;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.9rem 1.5rem;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: all 0.3s ease;
            border-radius: 0 12px 12px 0;
            margin-right: 0.75rem;
            font-weight: 500;
            letter-spacing: 0.2px;
            position: relative;
        }

        .nav-link:hover {
            color: var(--accent);
            background: rgba(212, 175, 55, 0.1);
            transform: translateX(5px);
        }

        .nav-link.active {
            color: var(--white);
            background: linear-gradient(90deg, var(--secondary) 0%, rgba(13, 148, 136, 0.7) 100%);
            box-shadow: -4px 0 12px rgba(13, 148, 136, 0.3);
            font-weight: 600;
        }

        .nav-link i {
            width: 20px;
            margin-right: 0.75rem;
            font-size: 1rem;
        }

        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--secondary);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--accent);
        }

        /* ========================================
           Main Content
           ======================================== */
        .main-content {
            flex: 1;
            margin-left: 260px;
            min-height: 100vh;
            background: var(--light-bg);
            padding: 2rem;
        }

        .content-wrapper {
            max-width: 1400px;
            margin: 0 auto;
        }

        .page-header {
            background: var(--white);
            border-radius: 14px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow);
            border: 1px solid rgba(13, 148, 136, 0.1);
        }

        .page-title {
            font-size: 2.5rem;
            font-weight: 900;
            color: var(--primary);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 1rem;
            letter-spacing: -0.5px;
        }

        .page-subtitle {
            color: var(--text-muted);
            font-size: 1rem;
            margin: 0.75rem 0 0;
            font-weight: 400;
        }

        /* ========================================
           Card Styles
           ======================================== */
        .glass-card {
            background: var(--white);
            border: 1px solid rgba(13, 148, 136, 0.1);
            border-radius: 14px;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .glass-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-lg);
            border-color: var(--secondary);
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary) 0%, #2d5a7b 100%);
            color: var(--white);
            border: none;
            border-radius: 14px 14px 0 0 !important;
            padding: 1.5rem;
            font-weight: 700;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .card-body {
            padding: 2rem;
        }

        /* ========================================
           Table Styles
           ======================================== */
        .modern-table {
            background: var(--white);
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid rgba(13, 148, 136, 0.1);
            box-shadow: var(--shadow);
        }

        .modern-table thead th {
            background: linear-gradient(135deg, var(--primary) 0%, #2d5a7b 100%);
            color: var(--white);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: none;
            padding: 1.25rem;
            font-size: 0.85rem;
        }

        .modern-table tbody tr {
            border-bottom: 1px solid rgba(13, 148, 136, 0.08);
            transition: all 0.3s ease;
        }

        .modern-table tbody tr:hover {
            background: rgba(13, 148, 136, 0.05);
        }

        .modern-table tbody td {
            padding: 1.25rem;
            color: var(--text-dark);
            border: none;
            font-weight: 400;
        }

        /* ========================================
           Button Styles
           ======================================== */
        .btn-modern {
            border: none;
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            font-weight: 700;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--secondary) 0%, #059669 100%);
            color: var(--white);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px rgba(13, 148, 136, 0.3);
            color: var(--white);
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: var(--white);
        }

        .btn-success:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px rgba(16, 185, 129, 0.3);
            color: var(--white);
        }

        .btn-warning {
            background: linear-gradient(135deg, var(--accent) 0%, #c9a72e 100%);
            color: var(--primary);
        }

        .btn-warning:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px rgba(212, 175, 55, 0.3);
            color: var(--primary);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: var(--white);
        }

        .btn-danger:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px rgba(239, 68, 68, 0.3);
            color: var(--white);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #64748b 0%, #475569 100%);
            color: var(--white);
        }

        .btn-secondary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px rgba(100, 116, 139, 0.3);
            color: var(--white);
        }

        /* ========================================
           Form Styles
           ======================================== */
        .form-control {
            background: var(--white);
            border: 2px solid rgba(13, 148, 136, 0.15);
            border-radius: 10px;
            color: var(--text-dark);
            padding: 0.9rem 1rem;
            transition: all 0.3s ease;
            font-weight: 400;
        }

        .form-control:focus {
            background: var(--white);
            border-color: var(--secondary);
            box-shadow: 0 0 0 0.25rem rgba(13, 148, 136, 0.15);
            color: var(--text-dark);
        }

        .form-control::placeholder {
            color: var(--text-muted);
        }

        .form-label {
            color: var(--text-dark);
            font-weight: 600;
            margin-bottom: 0.75rem;
            letter-spacing: 0.2px;
        }

        /* ========================================
           Badge Styles
           ======================================== */
        .badge-modern {
            border-radius: 20px;
            font-weight: 700;
            padding: 0.5rem 1rem;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: var(--white);
        }

        .badge-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: var(--white);
        }

        .badge-warning {
            background: linear-gradient(135deg, var(--accent) 0%, #c9a72e 100%);
            color: var(--primary);
        }

        .badge-info {
            background: linear-gradient(135deg, var(--secondary) 0%, #059669 100%);
            color: var(--white);
        }

        /* ========================================
           Alert Styles
           ======================================== */
        .alert-modern {
            border-radius: 12px;
            border: none;
            border-left: 4px solid;
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            color: #065f46;
            border-left-color: #10b981;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            color: #7f1d1d;
            border-left-color: #ef4444;
        }

        }

        .alert-info {
            background: rgba(13, 148, 136, 0.1);
            color: #065f48;
            border-left-color: var(--secondary);
        }

        .alert-warning {
            background: rgba(212, 175, 55, 0.1);
            color: #78350f;
            border-left-color: var(--accent);
        }

        /* ========================================
           Footer
           ======================================== */
        .footer-modern {
            background: linear-gradient(135deg, var(--primary) 0%, #2d5a7b 100%);
            color: rgba(255, 255, 255, 0.8);
            text-align: center;
            padding: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: 4rem;
            font-weight: 500;
            letter-spacing: 0.3px;
        }

        /* ========================================
           Animations
           ======================================== */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }

        /* ========================================
           Notification Widget
           ======================================== */
        .notification-widget {
            position: fixed;
            top: 20px;
            right: 20px;
            width: 350px;
            background: var(--white);
            border-radius: 12px;
            box-shadow: var(--shadow-lg);
            z-index: 1000;
            max-height: 400px;
            overflow: hidden;
            animation: slideInRight 0.5s ease-out;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .notification-header {
            background: var(--primary);
            color: var(--white);
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
        }

        .notification-header .badge {
            background: var(--accent);
            color: var(--primary);
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .notification-list {
            max-height: 320px;
            overflow-y: auto;
        }

        .notification-item {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            border-bottom: 1px solid #f0f0f0;
            transition: background-color 0.2s;
        }

        .notification-item:hover {
            background-color: #f8f9fa;
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        .notification-warning {
            border-left: 4px solid #f59e0b;
        }

        .notification-danger {
            border-left: 4px solid #ef4444;
        }

        .notification-info {
            border-left: 4px solid #3b82f6;
        }

        .notification-success {
            border-left: 4px solid #10b981;
        }

        .notification-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            font-size: 1.1rem;
        }

        .notification-warning .notification-icon {
            background-color: #fef3c7;
            color: #f59e0b;
        }

        .notification-danger .notification-icon {
            background-color: #fee2e2;
            color: #ef4444;
        }

        .notification-info .notification-icon {
            background-color: #dbeafe;
            color: #3b82f6;
        }

        .notification-success .notification-icon {
            background-color: #d1fae5;
            color: #10b981;
        }

        .notification-content {
            flex: 1;
        }

        .notification-title {
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 2px;
        }

        .notification-message {
            font-size: 0.8rem;
            color: var(--text-muted);
            line-height: 1.3;
        }

        .notification-count {
            background: var(--primary);
            color: var(--white);
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: bold;
        }

        /* ========================================
           Responsive Design
           ======================================== */
        @media (max-width: 768px) {
            .notification-widget {
                width: calc(100vw - 40px);
                right: 20px;
                left: 20px;
                top: 20px;
            }

            .sidebar {
                width: 250px;
                transform: translateX(-100%);
                z-index: 2000;
                box-shadow: 4px 0 20px rgba(0, 0, 0, 0.2);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                padding: 1.5rem;
            }

            .page-header {
                padding: 1.5rem;
            }

            .page-title {
                font-size: 2rem;
            }

            .content-wrapper {
                padding: 0;
            }
        }
    </style>
</head>

<body>
    <div class="layout-container">
        <!-- Notification Widget -->
        @if(isset($notifications) && count($notifications) > 0)
        <div class="notification-widget">
            <div class="notification-header">
                <i class="fas fa-bell"></i>
                <span>Notifikasi</span>
                <span class="badge">{{ count($notifications) }}</span>
            </div>
            <div class="notification-list">
                @foreach($notifications as $notification)
                <div class="notification-item notification-{{ $notification['type'] }}">
                    <div class="notification-icon">
                        <i class="{{ $notification['icon'] }}"></i>
                    </div>
                    <div class="notification-content">
                        <div class="notification-title">{{ $notification['title'] }}</div>
                        <div class="notification-message">{{ $notification['message'] }}</div>
                    </div>
                    @if(isset($notification['count']))
                    <div class="notification-count">{{ $notification['count'] }}</div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <h3>
                    <i class="fas fa-book-open"></i>
                    Perpustakaan
                </h3>
            </div>
            <ul class="sidebar-nav">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                @auth
                @if(Auth::user()->isAdmin())
                <li class="nav-item">
                    <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <i class="fas fa-users-cog"></i>
                        <span>Kelola User</span>
                    </a>
                </li>
                @endif
                <li class="nav-item">
                    <a href="{{ route('books.index') }}" class="nav-link {{ request()->routeIs('books.*') ? 'active' : '' }}">
                        <i class="fas fa-book"></i>
                        <span>Buku</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('members.index') }}" class="nav-link {{ request()->routeIs('members.*') ? 'active' : '' }}">
                        @if(Auth::user()->isAdmin())
                            <i class="fas fa-users-cog"></i>
                            <span>Kelola Anggota</span>
                        @else
                            <i class="fas fa-user-friends"></i>
                            <span>Anggota</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('loans.index') }}" class="nav-link {{ request()->routeIs('loans.*') ? 'active' : '' }}">
                        <i class="fas fa-exchange-alt"></i>
                        <span>Peminjaman</span>
                    </a>
                </li>
                @if(Auth::user()->isAdmin())
                <li class="nav-item">
                    <a href="{{ route('penanggung-jawab.index') }}" class="nav-link {{ request()->routeIs('penanggung-jawab.*') ? 'active' : '' }}">
                        <i class="fas fa-user-tie"></i>
                        <span>Penanggung Jawab</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.index') ? 'active' : '' }}">
                        <i class="fas fa-file-alt"></i>
                        <span>Laporan</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('reports.statistics') }}" class="nav-link {{ request()->routeIs('reports.statistics') ? 'active' : '' }}">
                        <i class="fas fa-chart-bar"></i>
                        <span>Statistik</span>
                    </a>
                </li>
                @endif
                <li class="nav-item" style="border-top: 1px solid rgba(255, 255, 255, 0.1); margin-top: 1rem; padding-top: 1rem;">
                    <a href="{{ route('profile.show') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                        <i class="fas fa-user-circle"></i>
                        <span>Profil Saya</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('logout') }}" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Keluar</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                </li>
                @endauth
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <div class="content-wrapper">
                <!-- Page Header -->
                <div class="page-header fade-in-up">
                    <h1 class="page-title">
                        <i class="fas fa-tachometer-alt"></i>
                        @yield('title', 'Dashboard')
                    </h1>
                    <p class="page-subtitle">Sistem Manajemen Peminjaman Buku</p>
                </div>

                <!-- Alert Messages -->
                @if ($errors->any())
                    <div class="alert alert-modern alert-danger fade-in-up">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div>
                            <strong>Terjadi Kesalahan!</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-modern alert-success fade-in-up">
                        <i class="fas fa-check-circle"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-modern alert-danger fade-in-up">
                        <i class="fas fa-times-circle"></i>
                        {{ session('error') }}
                    </div>
                @endif

                @if (session('warning'))
                    <div class="alert alert-modern alert-warning fade-in-up">
                        <i class="fas fa-exclamation-triangle"></i>
                        {{ session('warning') }}
                    </div>
                @endif

                <!-- Page Content -->
                @yield('content')
            </div>

            <!-- Footer -->
            <footer class="footer-modern">
                <p class="mb-0">
                    <i class="fas fa-copyright"></i>
                    2026 Perpustakaan Digital | Dibuat dengan <i class="fas fa-heart" style="color: #ff6b9d;"></i> menggunakan Laravel
                </p>
            </footer>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add fade-in animation to cards
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.glass-card');
            cards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
                card.classList.add('fade-in-up');
            });
        });
    </script>

    @yield('scripts')
</body>

</html>
