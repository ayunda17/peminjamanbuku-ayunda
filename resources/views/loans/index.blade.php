@extends('layouts.app')

@section('title', 'Daftar Peminjaman')

@section('content')
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2>📋 Daftar Peminjaman</h2>
                <a href="{{ route('loans.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Pinjam Buku
                </a>
            </div>
        </div>
    </div>

    @if ($loans->isEmpty())
        <div class="alert alert-info">
            @if(!empty($search))
                Tidak ada hasil pencarian untuk peminjaman.
            @else
                Belum ada data peminjaman.
            @endif
            <a href="{{ route('loans.create') }}">Mulai peminjaman</a>
        </div>
    @else
        <div class="card mb-3">
            <div class="card-header">
                <form method="GET" action="{{ route('loans.index') }}" class="d-flex gap-2">
                    <input type="text" name="search" class="form-control" placeholder="Cari buku atau anggota..." value="{{ $search }}" style="width: 300px;">
                    <select name="status" class="form-select" style="width: 200px;">
                        <option value="">Semua Status</option>
                        <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Masih Dipinjam</option>
                        <option value="overdue" {{ $status === 'overdue' ? 'selected' : '' }}>Terlambat</option>
                        <option value="returned" {{ $status === 'returned' ? 'selected' : '' }}>Dikembalikan</option>
                    </select>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    @if(!empty($search) || !empty($status))
                        <a href="{{ route('loans.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Buku</th>
                            <th>Anggota</th>
                            <th>Penanggung Jawab</th>
                            <th>Tgl Pinjam</th>
                            <th>Batas Kembali</th>
                            <th>Status</th>
                            <th>Denda</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($loans as $loan)
                            <tr>
                                <td>{{ $pagination['firstItem'] + $loop->index }}</td>
                                <td>{{ $loan->book->title }}</td>
                                <td>{{ $loan->member->name }}</td>
                                <td>{{ $loan->penanggungJawab?->nama ?? '-' }}</td>
                                <td>{{ $loan->loan_date->format('d M Y') }}</td>
                                <td>{{ $loan->due_date->format('d M Y') }}</td>
                                <td>
                                    @if ($loan->isActive())
                                        <span class="badge {{ $loan->isOverdue() ? 'bg-danger' : 'bg-success' }}">
                                            {{ $loan->isOverdue() ? 'Terlambat' : 'Aktif' }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">Dikembalikan</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($loan->fine > 0)
                                        <strong class="text-danger">Rp {{ number_format($loan->fine) }}</strong>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('loans.show', $loan) }}" class="btn btn-info btn-sm" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if ($loan->isActive())
                                            <a href="{{ route('loans.confirmReturn', $loan) }}" class="btn btn-warning btn-sm" title="Kembalikan Buku">
                                                <i class="fas fa-undo"></i>
                                            </a>
                                        @endif
                                        <form action="{{ route('loans.destroy', $loan) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus Peminjaman" onclick="return confirm('Yakin ingin menghapus?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination Component -->
        <x-pagination-component 
            :pagination="$pagination" 
            routeName="loans.index"
            :queryParams="['search' => $search, 'status' => $status]"
        />
    @endif
@endsection
