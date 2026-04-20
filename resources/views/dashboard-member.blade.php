@extends('layouts.app')

@section('title', 'Dashboard Anggota')

@section('content')
<style>
    .member-card {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
        border: none;
    }

    .member-card .card-body {
        padding: 2rem;
    }

    .member-info-item {
        background: rgba(255, 255, 255, 0.1);
        padding: 1.5rem;
        border-radius: 10px;
        margin-bottom: 1rem;
        border-left: 4px solid var(--accent);
    }

    .member-info-label {
        font-size: 0.85rem;
        text-transform: uppercase;
        opacity: 0.8;
        letter-spacing: 0.5px;
    }

    .member-info-value {
        font-size: 1.3rem;
        font-weight: 700;
        margin-top: 0.5rem;
    }

    .fine-alert {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        border-left: 4px solid #dc2626;
        color: #7f1d1d;
    }

    .fine-amount {
        font-size: 1.5rem;
        font-weight: 700;
        color: #dc2626;
    }

    .section-header {
        border-bottom: 3px solid var(--secondary);
        padding-bottom: 1rem;
        margin-bottom: 2rem;
    }

    .section-header h4 {
        color: var(--primary);
        font-weight: 700;
        margin: 0;
    }

    .loan-status-active {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        color: #065f46;
    }

    .loan-status-returned {
        background: linear-gradient(135deg, #e5e7eb 0%, #d1d5db 100%);
        color: #374151;
    }

    .loan-status-overdue {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: #7f1d1d;
    }

    .loan-status-fine {
        background: linear-gradient(135deg, #fef3c7 0%, #fcd34d 100%);
        color: #92400e;
    }

    .badge-loan {
        display: inline-block;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .table-modern tbody tr {
        border-bottom: 1px solid #e5e7eb;
    }

    .table-modern tbody tr:hover {
        background-color: rgba(13, 148, 136, 0.05);
    }

    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
    }

    .empty-state-icon {
        font-size: 3rem;
        color: #d1d5db;
        margin-bottom: 1rem;
    }

    .fine-badge-warning {
        background: #fef3c7;
        color: #92400e;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .fine-badge-danger {
        background: #fee2e2;
        color: #7f1d1d;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        padding: 1.5rem;
        text-align: center;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        border-color: var(--secondary);
        box-shadow: 0 4px 12px rgba(13, 148, 136, 0.1);
    }

    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        color: var(--secondary);
        margin: 0.5rem 0;
    }

    .stat-label {
        font-size: 0.85rem;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
</style>

<!-- Welcome Banner -->
<div class="alert alert-modern fade-in-up mb-4" style="background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); color: white; border: none;">
    <div class="d-flex align-items-center">
        <i class="fas fa-user-circle fa-3x me-3"></i>
        <div>
            <h3 class="mb-0">👋 Selamat Datang, {{ Auth::user()->name }}!</h3>
            <p class="mb-1" style="font-size: 0.9rem; opacity: 0.9;">Dashboard Anggota Perpustakaan</p>
            <small style="opacity: 0.8;">NIS: <strong>{{ Auth::user()->nis }}</strong></small>
        </div>
    </div>
</div>

<!-- Member Info Card -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="glass-card member-card">
            <div class="card-body">
                <h5 class="mb-3"><i class="fas fa-user-check"></i> Data Diri</h5>
                <div class="member-info-item">
                    <div class="member-info-label">Nama Lengkap</div>
                    <div class="member-info-value">{{ Auth::user()->member->name ?? Auth::user()->name }}</div>
                </div>
                <div class="member-info-item">
                    <div class="member-info-label">NIS (Nomor Induk Siswa)</div>
                    <div class="member-info-value">{{ Auth::user()->nis }}</div>
                </div>
                <div class="member-info-item">
                    <div class="member-info-label">Email</div>
                    <div class="member-info-value" style="font-size: 1rem;">{{ Auth::user()->email }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Card -->
    <div class="col-md-6">
        <div class="stats-row">
            <div class="stat-card">
                <i class="fas fa-book-open fa-2x" style="color: var(--secondary);"></i>
                <div class="stat-number">{{ Auth::user()->getActiveLoans()->count() }}</div>
                <div class="stat-label">Buku Dipinjam</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-history fa-2x" style="color: #0fa9e6;"></i>
                <div class="stat-number">{{ Auth::user()->getAllLoans()->count() }}</div>
                <div class="stat-label">Total Peminjaman</div>
            </div>
        </div>
        @php
            $totalFines = 0; // Reset output denda ke 0
        @endphp
        @if($totalFines > 0)
        <div class="fine-alert alert">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <strong><i class="fas fa-exclamation-circle"></i> Denda Belum Dibayar</strong>
                    <p class="mb-0 small mt-1">Anda memiliki denda yang belum dibayarkan</p>
                </div>
                <div class="fine-amount">Rp {{ number_format($totalFines, 0, ',', '.') }}</div>
            </div>
        </div>
        @else
        <div class="alert alert-success mb-0">
            <i class="fas fa-check-circle"></i> Tidak ada denda yang tertunggak
        </div>
        @endif
    </div>
</div>

<!-- Buku yang Sedang Dipinjam -->
<div class="row mb-4">
    <div class="col-12">
        <div class="glass-card">
            <div class="card-header">
                <i class="fas fa-book-reader"></i> Buku yang Sedang Dipinjam
            </div>
            <div class="card-body">
                @php
                    $activeLoans = Auth::user()->getActiveLoans();
                @endphp

                @if($activeLoans->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-inbox"></i>
                        </div>
                        <p class="text-muted mb-3">Anda belum meminjam buku apapun</p>
                        <a href="{{ route('books.index') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-search"></i> Cari Buku
                        </a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table modern-table mb-0">
                            <thead>
                                <tr>
                                    <th>Buku</th>
                                    <th>Tanggal Pinjam</th>
                                    <th>Batas Kembali</th>
                                    <th>Status</th>
                                    <th>Info</th>
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
                                            <div>
                                                <strong style="color: var(--primary);">{{ $loan->book->title }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $loan->book->author ?? 'N/A' }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <small>{{ $loan->loan_date->format('d M Y') }}</small>
                                        </td>
                                        <td>
                                            <small>
                                                <strong class="@if($isOverdue) text-danger @endif">
                                                    {{ $loan->due_date->format('d M Y') }}
                                                </strong>
                                            </small>
                                        </td>
                                        <td>
                                            @if($isOverdue)
                                                <span class="badge badge-loan loan-status-overdue">Terlambat</span>
                                            @else
                                                <span class="badge badge-loan loan-status-active">Aktif</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($isOverdue)
                                                <small class="text-danger"><strong>{{ abs($daysLeft) }} hari yg lalu</strong></small>
                                            @else
                                                <small class="text-success"><strong>{{ $daysLeft }} hari lagi</strong></small>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('loans.show', $loan) }}" class="btn btn-sm btn-outline-primary" title="Lihat Detail">
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

<!-- Denda yang Tertunggak -->
@php
    $unpaidFines = Auth::user()->getUnpaidFines();
@endphp
@if($unpaidFines->isNotEmpty())
<div class="row mb-4">
    <div class="col-12">
        <div class="glass-card">
            <div class="card-header" style="background: #fee2e2; color: #7f1d1d;">
                <i class="fas fa-money-bill-wave"></i> Denda Belum Dibayar
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table modern-table mb-0">
                        <thead>
                            <tr>
                                <th>Buku</th>
                                <th>Tanggal Kembali</th>
                                <th>Denda</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($unpaidFines as $loan)
                                <tr>
                                    <td>
                                        <strong style="color: var(--primary);">{{ $loan->book->title }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $loan->book->author ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <small>{{ $loan->return_date->format('d M Y') }}</small>
                                    </td>
                                    <td>
                                        <strong class="text-danger">Rp {{ number_format($loan->fine, 0, ',', '.') }}</strong>
                                    </td>
                                    <td>
                                        <span class="fine-badge-danger">Belum Dibayar</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('loans.show', $loan) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Riwayat Peminjaman -->
<div class="row">
    <div class="col-12">
        <div class="glass-card">
            <div class="card-header">
                <i class="fas fa-history"></i> Riwayat Peminjaman
            </div>
            <div class="card-body">
                @php
                    $loanHistory = Auth::user()->getLoanHistory();
                @endphp

                @if($loanHistory->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-box-open"></i>
                        </div>
                        <p class="text-muted">Belum ada riwayat peminjaman</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table modern-table mb-0">
                            <thead>
                                <tr>
                                    <th>Buku</th>
                                    <th>Tanggal Pinjam</th>
                                    <th>Tanggal Kembali</th>
                                    <th>Durasi</th>
                                    <th>Denda (jika ada)</th>
                                    <th>Status Pembayaran</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($loanHistory as $loan)
                                    @php
                                        $loanDate = \Carbon\Carbon::parse($loan->loan_date);
                                        $returnDate = \Carbon\Carbon::parse($loan->return_date);
                                        $duration = $loanDate->diffInDays($returnDate);
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong style="color: var(--primary);">{{ $loan->book->title }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $loan->book->author ?? 'N/A' }}</small>
                                        </td>
                                        <td><small>{{ $loan->loan_date->format('d M Y') }}</small></td>
                                        <td><small>{{ $loan->return_date->format('d M Y') }}</small></td>
                                        <td><small><strong>{{ $duration }} hari</strong></small></td>
                                        <td>
                                            @if($loan->fine > 0)
                                                <strong class="text-danger">Rp {{ number_format($loan->fine, 0, ',', '.') }}</strong>
                                            @else
                                                <span class="text-success">Gratis</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($loan->fine === 0 || $loan->fine === null)
                                                <span class="badge badge-loan loan-status-returned">Lunas</span>
                                            @else
                                                <span class="badge badge-loan loan-status-fine">Belum Dibayar</span>
                                            @endif
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

@endsection
