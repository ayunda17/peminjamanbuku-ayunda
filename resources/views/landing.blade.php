@extends('layouts.landing')

@section('title', 'Perpustakaan Digital - Sistem Peminjaman Buku Sekolah')

@section('content')
<style>
    /* ========================================
       Color Palette - Elegant Theme
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

    /* ========================================
       Animated Background
       ======================================== */
    .animated-bg {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, var(--primary) 0%, #2d5a7b 50%, var(--secondary) 100%);
        animation: gradientFlow 8s ease infinite;
        z-index: -1;
        pointer-events: none;
    }

    @keyframes gradientFlow {
        0%, 100% { filter: hue-rotate(0deg); }
        50% { filter: hue-rotate(3deg); }
    }

    /* ========================================
       Hero Section
       ======================================== */
    .landing-hero {
        background: transparent;
        color: white;
        padding: 120px 20px;
        text-align: center;
        position: relative;
        overflow: hidden;
        min-height: 90vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .landing-hero::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 600px;
        height: 600px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.08) 0%, transparent 70%);
        border-radius: 50%;
        transform: translate(-50%, -50%);
        animation: pulse 6s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% { transform: translate(-50%, -50%) scale(1); opacity: 0.5; }
        50% { transform: translate(-50%, -50%) scale(1.15); opacity: 0.8; }
    }

    .landing-hero-content {
        position: relative;
        z-index: 1;
        max-width: 900px;
    }

    .landing-hero h1 {
        font-size: 4rem;
        font-weight: 900;
        margin-bottom: 25px;
        line-height: 1.1;
        animation: slideDown 1s ease-out;
        letter-spacing: -1px;
    }

    .landing-hero p {
        font-size: 1.4rem;
        margin-bottom: 50px;
        opacity: 0.95;
        animation: slideUp 1s ease-out 0.2s both;
        line-height: 1.8;
        font-weight: 300;
    }

    .hero-buttons {
        display: flex;
        gap: 20px;
        justify-content: center;
        flex-wrap: wrap;
        animation: fadeIn 1s ease-out 0.4s both;
    }

    .btn-hero {
        padding: 16px 50px;
        font-size: 1rem;
        font-weight: 700;
        border-radius: 8px;
        transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        min-width: 200px;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        position: relative;
        overflow: hidden;
        box-shadow: var(--shadow-lg);
    }

    .btn-hero::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }

    .btn-hero:hover::before {
        width: 300px;
        height: 300px;
    }

    .btn-hero-primary {
        background: var(--accent);
        color: var(--primary);
        border: none;
    }

    .btn-hero-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 25px 50px rgba(212, 175, 55, 0.4);
    }

    .btn-hero-secondary {
        background: transparent;
        color: var(--accent);
        border: 2px solid var(--accent);
    }

    .btn-hero-secondary:hover {
        background: var(--accent);
        color: var(--primary);
        transform: translateY(-3px);
        box-shadow: 0 25px 50px rgba(212, 175, 55, 0.4);
    }

    /* ========================================
       Features Section
       ======================================== */
    .features-section {
        padding: 120px 20px;
        background: var(--light-bg);
        position: relative;
        z-index: 1;
    }

    .section-title {
        text-align: center;
        font-size: 3rem;
        font-weight: 900;
        margin-bottom: 70px;
        color: var(--primary);
        position: relative;
        letter-spacing: -1px;
    }

    .section-title::after {
        content: '';
        position: absolute;
        bottom: -20px;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 4px;
        background: var(--accent);
        border-radius: 2px;
    }

    .feature-card {
        background: var(--white);
        border-radius: 12px;
        padding: 45px 35px;
        text-align: center;
        box-shadow: var(--shadow);
        transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        height: 100%;
        border: 1px solid rgba(13, 148, 136, 0.08);
        position: relative;
        overflow: hidden;
    }

    .feature-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--secondary);
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .feature-card:hover::before {
        transform: scaleX(1);
    }

    .feature-card:hover {
        transform: translateY(-12px);
        box-shadow: var(--shadow-lg);
    }

    .feature-icon {
        font-size: 3.5rem;
        margin-bottom: 25px;
        color: var(--secondary);
        transition: all 0.3s ease;
        display: inline-block;
    }

    .feature-card:hover .feature-icon {
        transform: scale(1.15);
    }

    .feature-card h3 {
        font-size: 1.4rem;
        font-weight: 800;
        margin-bottom: 15px;
        color: var(--primary);
        letter-spacing: -0.5px;
    }

    .feature-card p {
        color: var(--text-muted);
        font-size: 1rem;
        line-height: 1.7;
        font-weight: 400;
    }

    /* ========================================
       Stats Section
       ======================================== */
    .stats-section {
        padding: 100px 20px;
        background: linear-gradient(135deg, var(--primary) 0%, #2d5a7b 100%);
        color: white;
        text-align: center;
        position: relative;
        z-index: 1;
        overflow: hidden;
    }

    .stat-item {
        padding: 40px 20px;
        position: relative;
        z-index: 1;
    }

    .stat-number {
        font-size: 3.5rem;
        font-weight: 900;
        margin-bottom: 10px;
        animation: countUp 2s ease-out;
        letter-spacing: -1px;
    }

    @keyframes countUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .stat-label {
        font-size: 1.1rem;
        opacity: 0.9;
        font-weight: 500;
        letter-spacing: 0.5px;
    }

    /* ========================================
       CTA Section
       ======================================== */
    .cta-section {
        padding: 120px 20px;
        text-align: center;
        background: var(--light-bg);
        position: relative;
        z-index: 1;
    }

    .cta-content {
        max-width: 700px;
        margin: 0 auto;
    }

    .cta-section h2 {
        font-size: 3rem;
        font-weight: 900;
        margin-bottom: 25px;
        color: var(--primary);
        letter-spacing: -1px;
    }

    .cta-section p {
        font-size: 1.2rem;
        color: var(--text-muted);
        margin-bottom: 40px;
        line-height: 1.8;
        font-weight: 400;
    }

    .info-banner {
        background: var(--white);
        border-left: 5px solid var(--secondary);
        padding: 35px;
        border-radius: 12px;
        margin-top: 50px;
        color: var(--primary);
        box-shadow: var(--shadow);
        animation: slideUp 1s ease-out;
        border-top: 1px solid rgba(13, 148, 136, 0.1);
    }

    .info-banner i {
        margin-right: 12px;
        color: var(--secondary);
        font-size: 1.3rem;
    }

    .info-banner strong {
        display: block;
        margin-bottom: 8px;
        font-size: 1.1rem;
        color: var(--primary);
    }

    .info-banner p {
        margin: 0;
        color: var(--text-muted);
        line-height: 1.6;
    }

    /* ========================================
       Animations
       ======================================== */
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-40px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(40px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    html { scroll-behavior: smooth; }

    /* ========================================
       Responsive Design
       ======================================== */
    @media (max-width: 768px) {
        .animated-bg { display: none; }

        .landing-hero {
            min-height: auto;
            padding: 60px 20px;
        }

        .landing-hero h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
        }

        .landing-hero p {
            font-size: 1.1rem;
            margin-bottom: 30px;
        }

        .hero-buttons {
            gap: 15px;
        }

        .btn-hero {
            min-width: 100%;
            padding: 14px 30px;
            font-size: 0.9rem;
        }

        .section-title {
            font-size: 2.2rem;
            margin-bottom: 50px;
        }

        .feature-card {
            padding: 30px 20px;
        }

        .stat-number { font-size: 2.5rem; }

        .cta-section h2 { font-size: 2.2rem; }

        .cta-section p { font-size: 1rem; }

        .info-banner {
            padding: 25px;
            font-size: 0.95rem;
        }

        .features-section { padding: 80px 20px; }
        .cta-section { padding: 80px 20px; }
        .stats-section { padding: 80px 20px; }
    }
</style>

<!-- Animated Background -->
<div class="animated-bg"></div>

<!-- Hero Section -->
<section class="landing-hero">
    <div class="landing-hero-content">
        <h1>Perpustakaan Digital</h1>
        <p>Revolusi Digital Peminjaman Buku Sekolah Modern</p>
        <div class="hero-buttons">
            <a href="{{ route('login') }}" class="btn btn-hero btn-hero-primary">
                <i class="fas fa-sign-in-alt me-2"></i>
                Masuk Sekarang
            </a>
            <a href="{{ route('register') }}" class="btn btn-hero btn-hero-secondary">
                <i class="fas fa-user-plus me-2"></i>
                Daftar Gratis
            </a>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features-section">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12">
                <h2 class="section-title">Fitur Unggulan</h2>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <h3>1000+ Koleksi</h3>
                    <p>Nikmati ribuan judul buku terbaru dari berbagai genre yang tersedia untuk Anda pinjam kapan saja.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-lightning-bolt"></i>
                    </div>
                    <h3>Instan & Cepat</h3>
                    <p>Proses peminjaman hanya 30 detik. Tidak perlu repot, cukup beberapa klik dan buku siap dinikmati.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-bell"></i>
                    </div>
                    <h3>Notifikasi Real-Time</h3>
                    <p>Dapatkan pengingat otomatis dan update status peminjaman langsung ke perangkat Anda.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3>Pencarian Canggih</h3>
                    <p>Filter pencarian lengkap berdasarkan genre, penulis, tahun terbit, atau rekomendasi personal.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Dashboard Interaktif</h3>
                    <p>Pantau riwayat peminjaman, statistik membaca, dan preferensi buku favorit Anda dalam satu dashboard.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Keamanan Terjamin</h3>
                    <p>Enkripsi end-to-end dan protokol keamanan tingkat enterprise untuk melindungi data Anda.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <div class="stat-number">1000+</div>
                    <div class="stat-label">Koleksi Buku</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <div class="stat-number">500+</div>
                    <div class="stat-label">Pembaca Aktif</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <div class="stat-number">50+</div>
                    <div class="stat-label">Genre Buku</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <div class="stat-number">24/7</div>
                    <div class="stat-label">Akses Online</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="cta-content">
        <h2>Mulai Petualangan Membaca Anda</h2>
        <p>Jadilah bagian dari komunitas pembaca kami yang terus berkembang dan rasakan pengalaman perpustakaan digital yang revolusioner.</p>
        
        <div class="info-banner">
            <i class="fas fa-info-circle"></i>
            <strong>Informasi Penting:</strong>
            Jika NIS Anda sudah terdaftar, akun akan langsung aktif. Untuk NIS baru, silakan tunggu persetujuan dari administrator dalam waktu 1-2 hari kerja.
        </div>
    </div>
</section>

@endsection
