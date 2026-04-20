@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<style>
    .badge-status {
        display: inline-block;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .badge-active {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }
    
    .badge-overdue {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
    }
    
    .badge-returned {
        background: linear-gradient(135deg, #64748b 0%, #475569 100%);
        color: white;
    }
</style>

<!-- Welcome Alert -->
<div class="alert alert-modern alert-success fade-in-up mb-4" role="alert">
    <i class="fas fa-user-check"></i>
    <div>
        <strong>👋 Selamat datang kembali, {{ Auth::user()->name }}!</strong>
        <p class="mb-0 mt-1">Status akun: 
            @if(Auth::user()->is_active)
                <span class="badge-status badge-active">✅ Aktif</span>
            @else
                <span class="badge-status badge-overdue">⏳ Pending</span>
            @endif
        </p>
    </div>
</div>

<!-- Dashboard Cards -->
<div class="row g-4 mb-5">
    <div class="col-xl-3 col-md-6">
        <div class="glass-card">
            <div class="card-body text-center">
                <i class="fas fa-book fa-3x mb-3" style="color: var(--secondary);"></i>
                <h5>Daftar Buku</h5>
                <p class="text-muted small">Lihat & cari buku tersedia</p>
                <a href="{{ route('books.index') }}" class="btn btn-primary btn-modern btn-sm">
                    <i class="fas fa-eye"></i> Lihat
                </a>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="glass-card">
            <div class="card-body text-center">
                <i class="fas fa-exchange-alt fa-3x mb-3" style="color: var(--secondary);"></i>
                <h5>Peminjaman</h5>
                <p class="text-muted small">Status pinjaman Anda</p>
                <a href="{{ route('loans.index') }}" class="btn btn-primary btn-modern btn-sm">
                    <i class="fas fa-list"></i> Cek
                </a>
            </div>
        </div>
    </div>
    @if(Auth::user()->isAdmin())
    <div class="col-xl-3 col-md-6">
        <div class="glass-card">
            <div class="card-body text-center">
                <i class="fas fa-users-cog fa-3x mb-3" style="color: var(--secondary);"></i>
                <h5>Kelola Anggota</h5>
                <p class="text-muted small">Manage users</p>
                <a href="{{ route('members.index') }}" class="btn btn-primary btn-modern btn-sm">
                    <i class="fas fa-users"></i> Kelola
                </a>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="glass-card">
            <div class="card-body text-center">
                <i class="fas fa-plus-circle fa-3x mb-3" style="color: var(--secondary);"></i>
                <h5>Tambah Buku</h5>
                <p class="text-muted small">Buku baru</p>
                <a href="{{ route('books.create') }}" class="btn btn-primary btn-modern btn-sm">
                    <i class="fas fa-plus"></i> Tambah
                </a>
            </div>
        </div>
    </div>
    @else
    <div class="col-xl-6 col-md-12">
        <div class="glass-card">
            <div class="card-body text-center">
                <i class="fas fa-user fa-3x mb-3" style="color: var(--secondary);"></i>
                <h5>Profil Saya</h5>
                <p class="text-muted small">Lihat info & riwayat</p>
                <a href="{{ route('members.show', Auth::id()) }}" class="btn btn-primary btn-modern btn-sm">
                    <i class="fas fa-user-circle"></i> Profil
                </a>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Buku Dipinjam Section -->
<div class="row mb-5">
    <div class="col-12">
        <div class="glass-card">
            <div class="card-header">
                <i class="fas fa-book-reader"></i> Buku Dipinjam Saat Ini
            </div>
            <div class="card-body">
                @php
                    $activeLoans = Auth::user()->getActiveLoans();
                @endphp
                
                @if($activeLoans->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3" style="opacity: 0.3;"></i>
                        <p class="text-muted">Anda belum meminjam buku apapun.</p>
                        <a href="{{ route('books.index') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-search"></i> Cari Buku
                        </a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="modern-table">
                            <thead>
                                <tr>
                                    <th>Buku</th>
                                    <th>Tanggal Pinjam</th>
                                    <th>Batas Kembali</th>
                                    <th>Status</th>
                                    <th>Sisa Hari</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activeLoans as $loan)
                                    @php
                                        $today = \Carbon\Carbon::today();
                                        $due = \Carbon\Carbon::parse($loan->due_date);
                                        $daysLeft = $today->diffInDays($due, false);
                                        $isOverdue = $daysLeft < 0;
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ $loan->book->title }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $loan->book->author ?? 'N/A' }}</small>
                                        </td>
                                        <td>{{ $loan->loan_date->format('d M Y') }}</td>
                                        <td>{{ $loan->due_date->format('d M Y') }}</td>
                                        <td>
                                            @if($isOverdue)
                                                <span class="badge-status badge-overdue">Terlambat</span>
                                            @else
                                                <span class="badge-status badge-active">Aktif</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($isOverdue)
                                                <strong class="text-danger">{{ abs($daysLeft) }} hari yg lalu</strong>
                                            @else
                                                <span class="text-success"><strong>{{ $daysLeft }} hari</strong></span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('loans.show', $loan) }}" class="btn btn-sm btn-primary" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats for Admin -->
@if(Auth::user()->isAdmin())
<div class="row g-4 mb-5">
    <div class="col-md-3">
        <div class="glass-card text-center">
            <div class="card-body">
                <h1 class="display-4" style="color: var(--secondary);">{{ App\Models\Book::count() }}</h1>
                <p class="text-muted">Total Buku</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="glass-card text-center">
            <div class="card-body">
                <h1 class="display-4" style="color: var(--secondary);">{{ App\Models\Member::count() }}</h1>
                <p class="text-muted">Total Anggota</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="glass-card text-center">
            <div class="card-body">
                <h1 class="display-4" style="color: var(--secondary);">{{ App\Models\Loan::whereNull('return_date')->count() }}</h1>
                <p class="text-muted">Sedang Dipinjam</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="glass-card text-center">
            <div class="card-body">
                <h1 class="display-4" style="color: var(--secondary);">{{ App\Models\User::where('is_active', false)->count() }}</h1>
                <p class="text-muted">Pending Approval</p>
            </div>
        </div>
    </div>
</div>
@endif

@endsection
