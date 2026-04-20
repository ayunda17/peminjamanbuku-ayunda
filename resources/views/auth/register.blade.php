@extends('layouts.auth')

@section('title', 'Registrasi')
@section('title-section', 'Daftar Akun Baru')
@section('icon', '')
@section('subtitle', 'Buat akun peminjaman buku perpustakaan')

@section('content')
    <!-- Session Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show alert-modern" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('info'))
        <div class="alert alert-info alert-dismissible fade show alert-modern" role="alert">
            <i class="fas fa-info-circle me-2"></i>
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger alert-modern">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                       id="name" name="name" value="{{ old('name') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label for="nis" class="form-label">NIS <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                    <input type="text" class="form-control @error('nis') is-invalid @enderror" 
                           id="nis" name="nis" value="{{ old('nis') }}" required maxlength="20">
                </div>
                <div class="form-text small">NIS akan divalidasi otomatis dengan database sekolah</div>
                @error('nis')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-4">
            <label for="email" class="form-label">Email Sekolah <span class="text-danger">*</span></label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                   id="email" name="email" value="{{ old('email') }}" required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label for="phone" class="form-label">No. Telepon <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                           id="phone" name="phone" value="{{ old('phone') }}" required maxlength="20">
                </div>
                @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label for="address" class="form-label">Alamat <span class="text-danger">*</span></label>
                <textarea class="form-control @error('address') is-invalid @enderror" 
                          id="address" name="address" rows="3" required maxlength="1000">{{ old('address') }}</textarea>
                @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-4">
            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                   id="password" name="password" required minlength="8">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
            <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" 
                   id="password_confirmation" name="password_confirmation" required>
            @error('password_confirmation')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-login mb-4 w-100">
            <i class="fas fa-user-check me-2"></i>Buat Akun Saya
        </button>
    </form>

    <div class="divider">
        <span>atau</span>
    </div>

    <div class="text-center">
        <p class="text-muted mb-3">Sudah punya akun?</p>
        <a href="{{ route('login') }}" class="btn btn-register">
            <i class="fas fa-sign-in-alt me-2"></i>Masuk ke Sistem
        </a>
    </div>

    <div class="text-center mt-4 p-3" style="background: rgba(13, 148, 136, 0.08); border-radius: 10px; border-left: 3px solid #0d9488; font-size: 0.875rem;">
        <small style="color: #0f172a;">
            <i class="fas fa-info-circle" style="color: #0d9488; margin-right: 8px;"></i>
            <strong>Catatan:</strong> NIS terdaftar akan langsung aktif. Untuk NIS baru, silakan tunggu persetujuan admin dalam 1-2 hari kerja.
        </small>
    </div>
@endsection

