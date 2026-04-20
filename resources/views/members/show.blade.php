@extends('layouts.app')

@section('title', $member->name)

@section('content')
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">👥 Detail Anggota</h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">Nama</dt>
                        <dd class="col-sm-9">{{ $member->name }}</dd>

                        <dt class="col-sm-3">Alamat</dt>
                        <dd class="col-sm-9">{{ $member->address }}</dd>

                        <dt class="col-sm-3">No HP</dt>
                        <dd class="col-sm-9">{{ $member->phone }}</dd>

                        <dt class="col-sm-3">Terdaftar</dt>
                        <dd class="col-sm-9">{{ $member->created_at->format('d M Y H:i') }}</dd>
                    </dl>

                    <div class="d-grid gap-2 d-sm-flex justify-content-sm-start">
                        <a href="{{ route('members.edit', $member) }}" class="btn btn-warning">Edit</a>
                        <form action="{{ route('members.destroy', $member) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                        </form>
                        <a href="{{ route('members.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </div>
            </div>

            @if (!$loans->isEmpty())
                <div class="card mt-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">📚 Riwayat Peminjaman</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Buku</th>
                                    <th>Tanggal Pinjam</th>
                                    <th>Batas Kembali</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($loans as $loan)
                                    <tr>
                                        <td>{{ $loan->book->title }}</td>
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
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
