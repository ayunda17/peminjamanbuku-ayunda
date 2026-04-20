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

        /* ========================================
           Navigation Bar
           ======================================== */
        .navbar-landing {
            background: var(--white);
            box-shadow: 0 2px 10px rgba(30, 58, 95, 0.08);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-brand {
            font-weight: 900;
            font-size: 1.5rem;
            color: var(--primary) !important;
            display: flex;
            align-items: center;
            gap: 12px;
            letter-spacing: -0.5px;
        }

        .navbar-brand i {
            font-size: 1.8rem;
            color: var(--secondary);
        }

        .nav-link {
            font-weight: 600;
            color: var(--text-muted) !important;
            transition: all 0.3s ease;
            margin-left: 1rem;
            letter-spacing: 0.3px;
            position: relative;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--secondary);
            transition: width 0.3s ease;
        }

        .nav-link:hover {
            color: var(--secondary) !important;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .navbar-toggler {
            border: none;
        }

        .navbar-toggler:focus {
            box-shadow: none;
            outline: 2px solid var(--secondary);
        }

        /* ========================================
           Footer
           ======================================== */
        .footer-landing {
            background: linear-gradient(135deg, var(--primary) 0%, #2d5a7b 100%);
            color: var(--white);
            padding: 60px 0 20px;
            margin-top: 0;
            text-align: center;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .footer-content h5 {
            font-size: 1.5rem;
            font-weight: 900;
            margin-bottom: 25px;
            letter-spacing: -0.5px;
        }

        .footer-links {
            margin: 30px 0;
            display: flex;
            justify-content: center;
            gap: 40px;
            flex-wrap: wrap;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
            letter-spacing: 0.3px;
            position: relative;
        }

        .footer-links a::after {
            content: '';
            position: absolute;
            bottom: -3px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--accent);
            transition: width 0.3s ease;
        }

        .footer-links a:hover {
            color: var(--accent);
        }

        .footer-links a:hover::after {
            width: 100%;
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: 30px;
            padding-top: 30px;
            opacity: 0.85;
            font-size: 0.95rem;
        }

        .footer-bottom p {
            margin: 8px 0;
            line-height: 1.6;
        }

        .footer-bottom i {
            color: #ff6b9d;
        }

        /* ========================================
           Scrollbar Styling
           ======================================== */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--light-bg);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--secondary);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary);
        }

        /* ========================================
           Responsive Design
           ======================================== */
        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 1.3rem;
                gap: 8px;
            }

            .nav-link {
                margin-left: 0;
                padding: 10px 0;
                border-bottom: 1px solid rgba(30, 58, 95, 0.1);
            }

            .footer-links {
                gap: 20px;
            }

            .footer-landing {
                padding: 40px 0 15px;
            }

            .floating-actions {
                right: 16px;
                bottom: 16px;
            }

            .floating-actions a {
                width: 48px;
                height: 48px;
                font-size: 1.05rem;
            }
        }

        .floating-actions {
            position: fixed;
            right: 20px;
            bottom: 20px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            z-index: 1100;
            align-items: flex-end;
        }

        .floating-actions a {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            box-shadow: 0 18px 35px rgba(15, 23, 42, 0.18);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            text-decoration: none;
        }

        .floating-actions a:hover {
            transform: translateY(-2px);
            box-shadow: 0 24px 40px rgba(15, 23, 42, 0.24);
        }

        .floating-actions .whatsapp {
            background: #25d366;
        }

        .floating-actions .instagram {
            background: radial-gradient(circle at 30% 107%, #fdf497 0%, #fdf497 5%, #fd5949 45%, #d6249f 60%, #285aeb 90%);
        }

        .floating-actions .floating-icon {
            font-size: 1.25rem;
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-landing">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="fas fa-book-open"></i>
                Perpustakaan Digital
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="ms-auto">
                    @guest
                        <a href="{{ route('login') }}" class="nav-link">Masuk</a>
                        <a href="{{ route('register') }}" class="nav-link">Daftar</a>
                    @else
                        <a href="{{ route('dashboard') }}" class="nav-link">Dashboard</a>
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="nav-link" style="background: none; border: none; cursor: pointer;">
                                Keluar
                            </button>
                        </form>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer-landing">
        <div class="footer-content">
            <h5>Perpustakaan Digital Sekolah</h5>
            <div class="footer-links">
                <a href="{{ route('home') }}">Beranda</a>
                <a href="#features">Fitur</a>
                <a href="#">Tentang</a>
                <a href="#">Hubungi Kami</a>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 Perpustakaan Digital. Semua hak dilindungi.</p>
                <p>Dibuat dengan <i class="fas fa-heart"></i> untuk pendidikan yang lebih baik</p>
            </div>
        </div>
    </footer>

    <div class="floating-actions">
        <a href="https://wa.me/6285180820304" target="_blank" rel="noopener noreferrer"
            class="whatsapp" data-bs-toggle="tooltip" data-bs-placement="left" title="Chat WhatsApp">
            <i class="fab fa-whatsapp floating-icon"></i>
        </a>
        <a href="https://instagram.com/ayyundachikal_" target="_blank" rel="noopener noreferrer"
            class="instagram" data-bs-toggle="tooltip" data-bs-placement="left" title="Instagram">
            <i class="fab fa-instagram floating-icon"></i>
        </a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function (tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
    <script>
        function hanyaAngka(event) { 
            var charCode = (event.which) ? event.which : event.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                return false;
            }
            return true;
        }
    </script>
</body>

</html>
