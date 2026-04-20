@extends('layouts.app')

@section('title', 'Profil Pengguna')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">👤 Profil Saya</h2>
            <p class="text-muted mb-0">Lihat dan kelola informasi profil Anda</p>
        </div>
        <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-modern">
            <i class="fas fa-edit"></i>
            <span>Edit Profil</span>
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <strong>Berhasil!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="glass-card mb-4">
                <div class="card-header">
                    <i class="fas fa-user"></i>
                    <span>Informasi Akun</span>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Nama Lengkap</label>
                            <p class="mb-0"><strong style="font-size: 1.1rem; color: #1e3a5f;">{{ $user->name }}</strong></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Email</label>
                            <p class="mb-0"><strong style="font-size: 1.1rem; color: #1e3a5f;">{{ $user->email }}</strong></p>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Status Akun</label>
                            <p class="mb-0">
                                <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-warning' }}">
                                    {{ $user->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Peran</label>
                            <p class="mb-0">
                                <span class="badge" style="background-color: {{ $user->role === 'admin' ? '#1e3a5f' : '#0d9488' }}">
                                    {{ $user->role === 'admin' ? 'Administrator' : 'Anggota' }}
                                </span>
                            </p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Bergabung Sejak</label>
                            <p class="mb-0"><strong style="color: #6b7280;">{{ $user->created_at->format('d M Y H:i') }}</strong></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Update Terakhir</label>
                            <p class="mb-0"><strong style="color: #6b7280;">{{ $user->updated_at->format('d M Y H:i') }}</strong></p>
                        </div>
                    </div>
                </div>
            </div>

            @if ($user->role === 'anggota')
                <div class="glass-card">
                    <div class="card-header">
                        <i class="fas fa-id-card"></i>
                        <span>Informasi Anggota</span>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted small">NIS</label>
                                <p class="mb-0"><strong style="font-size: 1.1rem; color: #1e3a5f;">{{ $user->nis ?? 'Belum diisi' }}</strong></p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Nomor Telepon</label>
                                <p class="mb-0"><strong style="font-size: 1.1rem; color: #1e3a5f;">{{ $user->phone ?? 'Belum diisi' }}</strong></p>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <label class="form-label text-muted small">Alamat</label>
                                <p class="mb-0"><strong style="color: #1e3a5f;">{{ $user->address ?? 'Belum diisi' }}</strong></p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="glass-card">
                <div class="card-header">
                    <i class="fas fa-lock"></i>
                    <span>Keamanan Akun</span>
                </div>
                <div class="card-body">
                    <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary w-100 mb-2">
                        <i class="fas fa-key me-2"></i>
                        Ubah Password
                    </a>
                    <button type="button" class="btn btn-outline-danger w-100" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                        <i class="fas fa-trash me-2"></i>
                        Hapus Akun
                    </button>
                </div>
            </div>

            <div class="glass-card mt-4">
                <div class="card-header">
                    <i class="fas fa-info-circle"></i>
                    <span>Informasi Bantuan</span>
                </div>
                <div class="card-body text-sm">
                    <p class="mb-2">
                        <strong>Untuk mengubah data profil:</strong><br>
                        Klik tombol "Edit Profil" di atas
                    </p>
                    <p class="mb-0">
                        <strong>Untuk mengubah password:</strong><br>
                        Gunakan fitur "Ubah Password"
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Account Modal -->
    <div class="modal fade" id="deleteAccountModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger bg-opacity-10">
                    <h5 class="modal-title">⚠️ Hapus Akun</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-danger mb-3">
                        <strong>Perhatian!</strong> Tindakan ini tidak dapat dibatalkan. Semua data akun Anda akan dihapus.
                    </p>
                    <p class="text-muted">Ketik "<strong>HAPUS AKUN</strong>" untuk mengkonfirmasi:</p>
                    <input type="text" class="form-control" id="deleteConfirm" placeholder="HAPUS AKUN">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete" disabled>Hapus Akun</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteConfirmInput = document.getElementById('deleteConfirm');
            const confirmDeleteBtn = document.getElementById('confirmDelete');

            deleteConfirmInput.addEventListener('input', function() {
                confirmDeleteBtn.disabled = this.value !== 'HAPUS AKUN';
            });

            confirmDeleteBtn.addEventListener('click', function() {
                if (confirm('Apakah Anda benar-benar ingin menghapus akun ini? Tindakan ini tidak dapat dibatalkan!')) {
                    // TODO: Implementasi delete account logic
                    alert('Fitur ini akan segera tersedia');
                }
            });
        });
    </script>
@endsection
