@extends('layouts.app')

@section('title', 'Laporan Peminjaman')

@section('content')
<style>
    .filter-section {
        background: white;
        border: 2px solid #0d9488;
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .filter-header {
        border-bottom: 2px solid #0d9488;
        padding-bottom: 1rem;
        margin-bottom: 1rem;
    }

    .filter-header h4 {
        color: var(--primary);
        margin: 0;
        font-weight: 700;
    }

    .filter-form {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr auto;
        gap: 1rem;
        align-items: flex-end;
    }

    .filter-form .form-group {
        margin: 0;
    }

    .filter-form .form-group label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #374151;
        display: block;
        margin-bottom: 0.5rem;
    }

    .filter-form .form-group input {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid #0d9488;
        border-radius: 5px;
        font-size: 0.9rem;
    }

    .filter-form .form-group input:focus {
        outline: none;
        border-color: var(--secondary);
        box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.1);
    }

    .btn-group-filter {
        display: flex;
        gap: 0.5rem;
    }

    .btn-filter {
        padding: 0.5rem 1rem;
        border: none;
        border-radius: 5px;
        font-weight: 600;
        cursor: pointer;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .btn-search {
        background: linear-gradient(135deg, var(--secondary) 0%, #065f46 100%);
        color: white;
    }

    .btn-search:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(13, 148, 136, 0.3);
    }

    .btn-reset {
        background: #f3f4f6;
        color: #374151;
    }

    .btn-reset:hover {
        background: #e5e7eb;
    }

    .export-section {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        border-left: 4px solid #0fa9e6;
        padding: 1.5rem;
        margin-bottom: 2rem;
        border-radius: 8px;
    }

    .export-header {
        font-weight: 700;
        color: #1e40af;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .export-buttons {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
    }

    .btn-export {
        padding: 0.75rem 1.25rem;
        border: 2px solid #ddd;
        border-radius: 8px;
        background: white;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        font-size: 0.9rem;
        text-decoration: none;
        color: #333;
    }

    .btn-export:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .btn-export-pdf {
        border-color: #dc2626;
        color: #dc2626;
    }

    .btn-export-pdf:hover {
        background: #fef2f2;
    }

    .btn-export-excel {
        border-color: #059669;
        color: #059669;
    }

    .btn-export-excel:hover {
        background: #f0fdf4;
    }

    .btn-export-html {
        border-color: #2563eb;
        color: #2563eb;
    }

    .btn-export-html:hover {
        background: #eff6ff;
    }

    .summary-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        padding: 1.5rem;
        text-align: center;
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

    .table-container {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .table-modern {
        width: 100%;
        margin: 0;
        border-collapse: collapse;
    }

    .table-modern thead {
        background: var(--primary);
        color: white;
    }

    .table-modern thead th {
        padding: 1rem;
        text-align: left;
        font-weight: 700;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .table-modern tbody td {
        padding: 1rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .table-modern tbody tr:hover {
        background-color: #f8f6f1;
    }

    .table-modern tbody tr:last-child td {
        border-bottom: none;
    }

    .badge-status {
        display: inline-block;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .badge-aktif {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        color: #065f46;
    }

    .badge-terlambat {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: #7f1d1d;
    }

    .badge-dikembalikan {
        background: linear-gradient(135deg, #e5e7eb 0%, #d1d5db 100%);
        color: #374151;
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

    .empty-state-text {
        color: #9ca3af;
        font-weight: 500;
    }

    @media (max-width: 768px) {
        .filter-form {
            grid-template-columns: 1fr;
        }

        .export-buttons {
            grid-template-columns: 1fr;
        }

        .summary-stats {
            grid-template-columns: 1fr;
        }
    }
</style>

<!-- Page Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2><i class="fas fa-file-chart-line"></i> Laporan Peminjaman Buku</h2>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="filter-section">
    <div class="filter-header">
        <h4><i class="fas fa-filter"></i> Filter Laporan</h4>
    </div>

    <form class="filter-form" method="GET" action="{{ route('reports.index') }}">
        <div class="form-group">
            <label for="start_date">Tanggal Mulai</label>
            <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}">
        </div>

        <div class="form-group">
            <label for="end_date">Tanggal Akhir</label>
            <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}">
        </div>

        <div class="form-group">
            <label>&nbsp;</label>
            <div class="btn-group-filter">
                <button type="submit" class="btn-filter btn-search">
                    <i class="fas fa-search"></i> Cari
                </button>
                <a href="{{ route('reports.index') }}" class="btn-filter btn-reset">
                    <i class="fas fa-redo"></i> Reset
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Export Section -->
<div class="export-section">
    <div class="export-header">
        <i class="fas fa-download"></i>
        <strong>Export Laporan</strong>
    </div>
    <div class="export-buttons">
        <form method="GET" action="{{ route('reports.export-pdf') }}" style="display: inline;">
            <input type="hidden" name="start_date" value="{{ request('start_date') }}">
            <input type="hidden" name="end_date" value="{{ request('end_date') }}">
            <button type="submit" class="btn-export btn-export-pdf" title="Export ke PDF">
                <i class="fas fa-file-pdf"></i> Export PDF
            </button>
        </form>

        <form method="GET" action="{{ route('reports.export-excel') }}" style="display: inline;">
            <input type="hidden" name="start_date" value="{{ request('start_date') }}">
            <input type="hidden" name="end_date" value="{{ request('end_date') }}">
            <button type="submit" class="btn-export btn-export-excel" title="Export ke Excel (CSV)">
                <i class="fas fa-file-excel"></i> Export Excel
            </button>
        </form>

        <form method="GET" action="{{ route('reports.export-html') }}" style="display: inline;">
            <input type="hidden" name="start_date" value="{{ request('start_date') }}">
            <input type="hidden" name="end_date" value="{{ request('end_date') }}">
            <button type="submit" class="btn-export btn-export-html" title="Export ke HTML (Print-friendly)">
                <i class="fas fa-file-html5"></i> Print Preview
            </button>
        </form>
    </div>
</div>

<!-- Summary Stats -->
@if(!$loans->isEmpty())
<div class="summary-stats">
    <div class="stat-card">
        <i class="fas fa-book fa-2x" style="color: var(--secondary);"></i>
        <div class="stat-number">{{ $loans->count() }}</div>
        <div class="stat-label">Total Peminjaman</div>
    </div>

    <div class="stat-card">
        <i class="fas fa-book-open fa-2x" style="color: #10b981;"></i>
        <div class="stat-number">{{ $loans->where('return_date', null)->count() }}</div>
        <div class="stat-label">Masih Dipinjam</div>
    </div>

    <div class="stat-card">
        <i class="fas fa-exclamation-circle fa-2x" style="color: #ef4444;"></i>
        <div class="stat-number">{{ $loans->filter(fn($l) => $l->isOverdue())->count() }}</div>
        <div class="stat-label">Terlambat</div>
    </div>

    <div class="stat-card">
        <i class="fas fa-money-bill fa-2x" style="color: #f59e0b;"></i>
        <div class="stat-number">Rp {{ number_format($totalFine, 0, ',', '.') }}</div>
        <div class="stat-label">Total Denda</div>
    </div>
</div>
@endif

<!-- Data Table -->
<div class="table-container" style="margin-top: 2rem;">
    @if($loans->isEmpty())
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="fas fa-inbox"></i>
            </div>
            <p class="empty-state-text">Tidak ada data peminjaman untuk filter yang dipilih</p>
        </div>
    @else
        <div style="overflow-x: auto;">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 20%;">Buku</th>
                        <th style="width: 14%;">Anggota</th>
                        <th style="width: 14%;">Penanggung Jawab</th>
                        <th style="width: 12%;">Tgl Pinjam</th>
                        <th style="width: 12%;">Batas Kembali</th>
                        <th style="width: 12%;">Tgl Kembali</th>
                        <th style="width: 12%;">Status</th>
                        <th style="width: 12%;">Denda</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($loans as $loan)
                        <tr>
                            <td style="font-weight: 600;">{{ $loop->iteration }}</td>
                            <td>
                                <strong style="color: var(--primary);">{{ $loan->book->title }}</strong>
                                <br>
                                <small class="text-muted">{{ $loan->book->author ?? 'N/A' }}</small>
                            </td>
                            <td>{{ $loan->member->name }}</td>
                            <td>{{ $loan->penanggungJawab?->nama ?? '-' }}</td>
                            <td>{{ $loan->loan_date->format('d M Y') }}</td>
                            <td>{{ $loan->due_date->format('d M Y') }}</td>
                            <td>
                                @if($loan->return_date)
                                    {{ $loan->return_date->format('d M Y') }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($loan->isActive())
                                    <span class="badge-status {{ $loan->isOverdue() ? 'badge-terlambat' : 'badge-aktif' }}">
                                        {{ $loan->isOverdue() ? 'Terlambat' : 'Aktif' }}
                                    </span>
                                @else
                                    <span class="badge-status badge-dikembalikan">Dikembalikan</span>
                                @endif
                            </td>
                            <td>
                                @if($loan->fine > 0)
                                    <strong class="text-danger">Rp {{ number_format($loan->fine, 0, '.', '.') }}</strong>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<script>
    // JavaScript untuk memastikan form works properly
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            // Optional: Validasi tanggal
            const startDate = this.querySelector('input[name="start_date"]')?.value;
            const endDate = this.querySelector('input[name="end_date"]')?.value;

            if (startDate && endDate && startDate > endDate) {
                e.preventDefault();
                alert('Tanggal mulai harus lebih kecil dari tanggal akhir!');
            }
        });
    });
</script>

@endsection
