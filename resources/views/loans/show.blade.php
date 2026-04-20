@extends('layouts.app')

@section('title', 'Detail Peminjaman')

@section('content')
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">📋 Detail Peminjaman</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">Informasi Buku</h6>
                            <dl class="row mb-0">
                                <dt class="col-sm-6">Judul</dt>
                                <dd class="col-sm-6">{{ $loan->book->title }}</dd>

                                <dt class="col-sm-6">Pengarang</dt>
                                <dd class="col-sm-6">{{ $loan->book->author }}</dd>

                                <dt class="col-sm-6">Penerbit</dt>
                                <dd class="col-sm-6">{{ $loan->book->publisher }}</dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Informasi Anggota</h6>
                            <dl class="row mb-0">
                                <dt class="col-sm-6">Nama</dt>
                                <dd class="col-sm-6">{{ $loan->member->name }}</dd>

                                <dt class="col-sm-6">No HP</dt>
                                <dd class="col-sm-6">{{ $loan->member->phone }}</dd>

                                <dt class="col-sm-6">Alamat</dt>
                                <dd class="col-sm-6">{{ $loan->member->address }}</dd>

                        <dt class="col-sm-6">Penanggung Jawab</dt>
                        <dd class="col-sm-6">{{ $loan->penanggungJawab?->nama ?? '-' }}</dd>
                    </div>

                    <hr>

                    <h6 class="text-muted">Informasi Peminjaman</h6>
                    <dl class="row">
                        <dt class="col-sm-3">Tanggal Peminjaman</dt>
                        <dd class="col-sm-9">{{ $loan->loan_date->format('d M Y') }}</dd>

                        <dt class="col-sm-3">Batas Pengembalian</dt>
                        <dd class="col-sm-9">{{ $loan->due_date->format('d M Y') }}</dd>

                        <dt class="col-sm-3">Status</dt>
                        <dd class="col-sm-9">
                            @if ($loan->isActive())
                                <span class="badge {{ $loan->isOverdue() ? 'bg-danger' : 'bg-success' }}">
                                    {{ $loan->isOverdue() ? 'Terlambat' : 'Aktif' }}
                                </span>
                            @else
                                <span class="badge bg-secondary">Dikembalikan</span>
                            @endif
                        </dd>

                        @if ($loan->return_date)
                            <dt class="col-sm-3">Tanggal Pengembalian</dt>
                            <dd class="col-sm-9">{{ $loan->return_date->format('d M Y') }}</dd>

                            <dt class="col-sm-3">Denda</dt>
                            <dd class="col-sm-9">
                                @if ($loan->fine > 0)
                                    <strong class="text-danger">Rp {{ number_format($loan->fine) }}</strong>
                                @else
                                    <span class="text-success">Tidak ada denda</span>
                                @endif
                            </dd>
                        @endif
                    </dl>

                    <div class="d-grid gap-2 d-sm-flex justify-content-sm-start">
                        @if ($loan->isActive())
                            <a href="{{ route('loans.returnForm', $loan) }}" class="btn btn-success">Kembalikan Buku</a>
                        @endif
                        <form action="{{ route('loans.destroy', $loan) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                        </form>
                        <a href="{{ route('loans.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
