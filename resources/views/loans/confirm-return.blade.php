@extends('layouts.app')

@section('title', 'Konfirmasi Pengembalian Buku')

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header bg-warning text-white">
                <h5 class="mb-0">⚠️ Konfirmasi Pengembalian Buku</h5>
            </div>
            <div class="card-body">
                <!-- Informasi Peminjaman -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted">Buku</h6>
                        <p class="mb-0"><strong>{{ $loan->book->title }}</strong></p>
                        <small class="text-muted">{{ $loan->book->author }}</small>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Anggota</h6>
                        <p class="mb-0"><strong>{{ $loan->member->name }}</strong></p>
                        <small class="text-muted">{{ $loan->member->phone }}</small>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted">Tanggal Peminjaman</h6>
                        <p class="mb-0">{{ $loan->loan_date->format('d M Y') }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Batas Pengembalian</h6>
                        <p class="mb-0">{{ $loan->due_date->format('d M Y') }}</p>
                    </div>
                </div>

                <hr>

                <!-- Informasi Denda -->
                <div class="alert {{ $lateDays > 0 ? 'alert-danger' : 'alert-success' }}">
                    <h6 class="alert-heading">{{ $lateDays > 0 ? '⚠️ Buku Terlambat Dikembalikan' : '✅ Buku Tepat Waktu' }}</h6>
                    <p class="mb-2">Tanggal pengembalian: <strong>{{ now()->format('d M Y') }}</strong></p>
                    @if ($lateDays > 0)
                        <p class="mb-2">Jumlah hari terlambat: <strong>{{ $lateDays }} hari</strong></p>
                        <p class="mb-0">Total denda: <strong>Rp {{ number_format($fine) }}</strong> (Rp 2.000 per hari)</p>
                    @else
                        <p class="mb-0">Tidak ada denda karena dikembalikan tepat waktu.</p>
                    @endif
                </div>

                <!-- Form Konfirmasi -->
                <form action="{{ route('loans.processConfirmReturn', $loan) }}" method="POST">
                    @csrf

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-check"></i> Ya, Kembalikan Buku
                        </button>
                        <a href="{{ route('loans.index') }}" class="btn btn-secondary btn-lg">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection