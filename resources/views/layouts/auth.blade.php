<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Peminjaman Buku') - Perpustakaan</title>
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
            background: linear-gradient(135deg, var(--primary) 0%, #2d5a7b 50%, var(--secondary) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            overflow-x: hidden;
        }

        .auth-container {
            width: 100%;
            max-width: 450px;
        }

        .auth-card {
            background: var(--white);
            border-radius: 16px;
            box-shadow: var(--shadow-lg);
            border: 1px solid rgba(255, 255, 255, 0.1);
            overflow: hidden;
            animation: slideUp 0.6s ease-out;
        }

        .auth-header {
            background: linear-gradient(135deg, var(--primary) 0%, #2d5a7b 100%);
            color: white;
            padding: 3rem 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .auth-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at top right, rgba(212, 175, 55, 0.1), transparent 70%);
            pointer-events: none;
        }

        .auth-header h1 {
            font-size: 2rem;
            font-weight: 900;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            letter-spacing: -0.5px;
            position: relative;
            z-index: 1;
        }

        .auth-header p {
            margin: 0.75rem 0 0;
            opacity: 0.9;
            font-size: 1rem;
            font-weight: 400;
            letter-spacing: 0.3px;
            position: relative;
            z-index: 1;
        }

        .auth-body {
            padding: 2.5rem;
        }

        .form-label {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 0.75rem;
            letter-spacing: 0.2px;
        }

        .form-control {
            border-radius: 10px;
            border: 2px solid rgba(13, 148, 136, 0.15);
            padding: 0.9rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: var(--light-bg);
            color: var(--text-dark);
            font-weight: 400;
        }

        .form-control:focus {
            border-color: var(--secondary);
            background: var(--white);
            box-shadow: 0 0 0 0.25rem rgba(13, 148, 136, 0.15);
            color: var(--text-dark);
        }

        .form-control::placeholder {
            color: var(--text-muted);
        }

        .input-group .btn-outline-secondary {
            border-color: rgba(13, 148, 136, 0.15);
            color: var(--secondary);
            background: var(--light-bg);
            font-weight: 600;
        }

        .input-group .btn-outline-secondary:hover {
            background: var(--secondary);
            border-color: var(--secondary);
            color: var(--white);
        }

        .form-check-input {
            border: 2px solid rgba(13, 148, 136, 0.2);
            border-radius: 6px;
            transition: all 0.3s ease;
            width: 1.2rem;
            height: 1.2rem;
        }

        .form-check-input:checked {
            background-color: var(--secondary);
            border-color: var(--secondary);
        }

        .form-check-input:focus {
            border-color: var(--secondary);
            box-shadow: 0 0 0 0.25rem rgba(13, 148, 136, 0.15);
        }

        .form-check-label {
            color: var(--text-muted);
            font-weight: 500;
            margin-left: 0.5rem;
        }

        .btn-login {
            background: linear-gradient(135deg, var(--accent) 0%, #c9a72e 100%);
            border: none;
            border-radius: 10px;
            padding: 1rem 2rem;
            font-weight: 700;
            font-size: 1rem;
            width: 100%;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 10px 30px rgba(212, 175, 55, 0.2);
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 50px rgba(212, 175, 55, 0.4);
            color: var(--primary);
        }

        .btn-login:active {
            transform: translateY(-1px);
        }

        .divider {
            position: relative;
            text-align: center;
            margin: 2rem 0;
            color: var(--text-muted);
            font-weight: 500;
            font-size: 0.9rem;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: rgba(13, 148, 136, 0.15);
        }

        .divider span {
            background: var(--white);
            padding: 0 1rem;
            position: relative;
            z-index: 1;
        }

        .btn-register {
            border: 2px solid var(--secondary);
            color: var(--secondary);
            border-radius: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn-register:hover {
            background: var(--secondary);
            color: var(--white);
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(13, 148, 136, 0.3);
        }

        .alert-modern {
            border: none;
            border-radius: 12px;
            border-left: 4px solid;
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }

        .alert-success {
            background: rgba(13, 148, 136, 0.1);
            border-left-color: var(--secondary);
            color: #065f46;
        }

        .alert-info {
            background: rgba(30, 58, 95, 0.1);
            border-left-color: var(--primary);
            color: #1e3a5f;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            border-left-color: #ef4444;
            color: #7f1d1d;
        }

        .invalid-feedback {
            color: #ef4444;
            font-weight: 500;
            font-size: 0.9rem;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 576px) {
            .auth-body {
                padding: 1.75rem;
            }

            .auth-header {
                padding: 2rem 1.5rem;
            }

            .auth-header h1 {
                font-size: 1.5rem;
            }

            .btn-login,
            .btn-register {
                padding: 0.9rem 1.5rem;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>
                    @yield('icon', '')
                    @yield('title-section', 'Selamat Datang')
                </h1>
                <p>@yield('subtitle', 'Sistem Peminjaman Buku Perpustakaan')</p>
            </div>
            <div class="auth-body">
                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

