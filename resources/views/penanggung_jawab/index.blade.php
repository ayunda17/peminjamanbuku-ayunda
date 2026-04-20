@extends('layouts.app')

@section('title', 'Penanggung Jawab')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>👤 Penanggung Jawab</h2>
                <a href="{{ route('penanggung-jawab.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i> Tambah Penanggung Jawab
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="card">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>NIP</th>
                                <th>Jabatan</th>
                                <th>No HP</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($penanggungJawabs as $penanggungJawab)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $penanggungJawab->nama }}</td>
                                    <td>{{ $penanggungJawab->nip }}</td>
                                    <td>{{ $penanggungJawab->jabatan }}</td>
                                    <td>{{ $penanggungJawab->no_hp }}</td>
                                    <td>
                                        <span class="badge {{ $penanggungJawab->is_active ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $penanggungJawab->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('penanggung-jawab.edit', $penanggungJawab) }}" class="btn btn-warning btn-sm">
                                            Edit
                                        </a>
                                        <form action="{{ route('penanggung-jawab.destroy', $penanggungJawab) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus penanggung jawab ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Belum ada penanggung jawab.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
