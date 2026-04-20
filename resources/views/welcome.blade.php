@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <!-- User sudah login: Dashboard -->
    <div class="row mb-5">
        <div class="col-md-12">
            <div class="alert alert-success border-0 shadow-sm d-flex align-items-center" role="alert">
                <i class="fas fa-user-check fa-2x me-3"></i>
                <div>
                    <h4 class="alert-heading mb-1">👋 Selamat datang kembali, {{ Auth::user()->name }}!</h4>
                    <p class="mb-0">Kelola peminjaman buku dengan mudah. Akun status: 
                        @if(Auth::user()->is_active)
                            <span class='badge bg-success'>✅ Aktif</span>
                        @else
                            <span class='badge bg-warning'>⏳ Pending</span>
                        @endif
                    </p>
                </div>
                <div class="ms-auto">
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Dashboard Cards -->
    <div class="row g-4 mb-5">
        <div class="col-xl-3 col-md-6">
            <div class="glass-card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-book fa-3x text-primary mb-3"></i>
                    <h5>Daftar Buku</h5>
                    <p class="text-muted small">Lihat & cari buku tersedia</p>
                    <a href="{{ route('books.index') }}" class="btn btn-primary btn-modern">
                        <i class="fas fa-eye"></i> Lihat Buku
                    </a>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="glass-card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-exchange-alt fa-3x text-info mb-3"></i>
                    <h5>Peminjaman Aktif</h5>
                    <p class="text-muted small">Status pinjaman Anda</p>
                    <a href="{{ route('loans.index') }}" class="btn btn-info btn-modern">
                        <i class="fas fa-list"></i> Cek Peminjaman
                    </a>
                </div>
            </div>
        </div>
        @if(Auth::user()->isAdmin())
        <div class="col-xl-3 col-md-6">
            <div class="glass-card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-users-cog fa-3x text-success mb-3"></i>
                    <h5>Kelola Anggota</h5>
                    <p class="text-muted small">Approve & manage users</p>
                    <a href="{{ route('members.index') }}" class="btn btn-success btn-modern">
                        <i class="fas fa-users"></i> Kelola
                    </a>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="glass-card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-plus-circle fa-3x text-warning mb-3"></i>
                    <h5>Peminjaman Baru</h5>
                    <p class="text-muted small">Catat pinjaman baru</p>
                    <a href="{{ route('loans.create') }}" class="btn btn-warning btn-modern">
                        <i class="fas fa-plus"></i> Tambah
                    </a>
                </div>
            </div>
        </div>
        @else
        <div class="col-xl-6 col-md-12">
            <div class="glass-card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-user fa-3x text-secondary mb-3"></i>
                    <h5>Profil Saya</h5>
                    <p class="text-muted small">Lihat informasi akun dan riwayat</p>
                    <a href="{{ route('members.show', Auth::id()) }}" class="btn btn-secondary btn-modern">
                        <i class="fas fa-user-circle"></i> Profil
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Quick Stats for Admin -->
    @if(Auth::user()->isAdmin())
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="glass-card text-center">
                <div class="card-body">
                    <h1 class="display-4 text-primary">{{ App\Models\Book::count() }}</h1>
                    <p class="text-muted">Total Buku</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card text-center">
                <div class="card-body">
                    <h1 class="display-4 text-success">{{ App\Models\Member::count() }}</h1>
                    <p class="text-muted">Total Anggota</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card text-center">
                <div class="card-body">
                    <h1 class="display-4 text-info">{{ App\Models\Loan::where('status', 'dipinjam')->count() }}</h1>
                    <p class="text-muted">Dipinjam</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card text-center">
                <div class="card-body">
                    <h1 class="display-4 text-warning">{{ App\Models\User::where('pending_status', 'pending')->count() }}</h1>
                    <p class="text-muted">Pending Approval</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Features Section -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="h3 mb-4 text-center">✨ Fitur Sistem Peminjaman Buku</h2>
        </div>
    </div>
    <div class="row g-4">
        <div class="col-lg-3 col-md-6">
            <div class="glass-card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-book fa-3x text-primary mb-3"></i>
                    <h5>Manajemen Buku</h5>
                    <p class="small">CRUD lengkap buku dengan stok real-time</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="glass-card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-3x text-success mb-3"></i>
                    <h5>Manajemen Anggota</h5>
                    <p class="small">Data anggota & approval otomatis NIS</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="glass-card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-exchange-alt fa-3x text-info mb-3"></i>
                    <h5>Peminjaman & Pengembalian</h5>
                    <p class="small">Transaksi lengkap dengan stok auto update</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="glass-card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-calculator fa-3x text-warning mb-3"></i>
                    <h5>Denda Otomatis</h5>
                    <p class="small">Rp1.000/hari keterlambatan otomatis</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

