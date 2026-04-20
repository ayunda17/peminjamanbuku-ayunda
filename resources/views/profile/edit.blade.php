@extends('layouts.app')

@section('title', 'Edit Profil')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">✏️ Edit Profil</h2>
            <p class="text-muted mb-0">Perbarui informasi profil Anda</p>
        </div>
        <a href="{{ route('profile.show') }}" class="btn btn-secondary btn-modern">
            <i class="fas fa-arrow-left"></i>
            <span>Kembali</span>
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong>Terjadi Kesalahan!</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Form Edit Data Dasar -->
            <div class="glass-card mb-4">
                <div class="card-header">
                    <i class="fas fa-user-circle"></i>
                    <span>Data Akun</span>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Nama Lengkap -->
                        <div class="mb-4">
                            <label for="name" class="form-label">
                                <i class="fas fa-user"></i>
                                <span>Nama Lengkap</span>
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name', $user->name) }}" required
                                placeholder="Masukkan nama lengkap">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-4">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope"></i>
                                <span>Email</span>
                                <span class="text-danger">*</span>
                            </label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" value="{{ old('email', $user->email) }}" required
                                placeholder="Masukkan email">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted d-block mt-2">
                                🔒 Email akan divalidasi untuk memastikan tidak ada duplikasi
                            </small>
                        </div>

                        <!-- Data Anggota (hanya muncul untuk role anggota) -->
                        @if ($user->role === 'anggota')
                            <hr class="border-secondary my-4">
                            <h5 class="mb-3">
                                <i class="fas fa-id-card"></i>
                                <span>Informasi Anggota</span>
                            </h5>

                            <!-- NIS -->
                            <div class="mb-4">
                                <label for="nis" class="form-label">
                                    <i class="fas fa-hashtag"></i>
                                    <span>NIS</span>
                                </label>
                                <input type="text" class="form-control @error('nis') is-invalid @enderror" id="nis"
                                    name="nis" value="{{ old('nis', $user->nis) }}"
                                    placeholder="Nomor Induk Siswa">
                                @error('nis')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Nomor Telepon -->
                            <div class="mb-4">
                                <label for="phone" class="form-label">
                                    <i class="fas fa-phone"></i>
                                    <span>Nomor Telepon</span>
                                </label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone"
                                    name="phone" value="{{ old('phone', $user->phone) }}"
                                    placeholder="Contoh: 08123456789">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Alamat -->
                            <div class="mb-4">
                                <label for="address" class="form-label">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>Alamat</span>
                                </label>
                                <textarea class="form-control @error('address') is-invalid @enderror" id="address"
                                    name="address" rows="3" placeholder="Masukkan alamat lengkap">{{ old('address', $user->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        <hr class="border-secondary my-4">
                        <h5 class="mb-3">
                            <i class="fas fa-key"></i>
                            <span>Ubah Password (Opsional)</span>
                        </h5>

                        <!-- Current Password -->
                        <div class="mb-4">
                            <label for="current_password" class="form-label">
                                <i class="fas fa-lock"></i>
                                <span>Password Saat Ini</span>
                            </label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                id="current_password" name="current_password"
                                placeholder="Masukkan password saat ini (jika ingin ubah password)">
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted d-block mt-1">
                                ℹ️ Wajib diisi jika Anda ingin mengubah password
                            </small>
                        </div>

                        <!-- New Password -->
                        <div class="mb-4">
                            <label for="new_password" class="form-label">
                                <i class="fas fa-lock"></i>
                                <span>Password Baru</span>
                            </label>
                            <input type="password" class="form-control @error('new_password') is-invalid @enderror"
                                id="new_password" name="new_password"
                                placeholder="Masukkan password baru">
                            @error('new_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted d-block mt-1">
                                🔐 Minimal 8 karakter
                            </small>
                        </div>

                        <!-- Confirm New Password -->
                        <div class="mb-4">
                            <label for="new_password_confirmation" class="form-label">
                                <i class="fas fa-lock"></i>
                                <span>Konfirmasi Password Baru</span>
                            </label>
                            <input type="password" class="form-control @error('new_password_confirmation') is-invalid @enderror"
                                id="new_password_confirmation" name="new_password_confirmation"
                                placeholder="Ketik ulang password baru">
                            @error('new_password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Alert Info -->
                        <div class="alert alert-info alert-sm border-0" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Tips Keamanan:</strong>
                            <ul class="mb-0 mt-2 small">
                                <li>Gunakan password yang kuat (kombinasi huruf besar, kecil, angka, simbol)</li>
                                <li>Jangan bagikan password Anda kepada siapapun</li>
                                <li>Ubah password secara berkala untuk keamanan lebih baik</li>
                            </ul>
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="d-flex gap-3 justify-content-end pt-4 border-top border-secondary mt-4">
                            <a href="{{ route('profile.show') }}" class="btn btn-secondary btn-modern">
                                <i class="fas fa-times"></i>
                                <span>Batal</span>
                            </a>
                            <button type="submit" class="btn btn-primary btn-modern">
                                <i class="fas fa-save"></i>
                                <span>Simpan Perubahan</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        .alert-sm {
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
        }
    </style>
@endsection
