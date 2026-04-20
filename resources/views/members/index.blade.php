@extends('layouts.app')

@section('title', 'Kelola Anggota')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">👥 Kelola Anggota</h2>
            <p class="text-muted mb-0">Kelola data anggota perpustakaan</p>
        </div>
        <a href="{{ route('members.create') }}" class="btn btn-primary btn-modern">
            <i class="fas fa-user-plus"></i>
            <span>Tambah Anggota</span>
        </a>
    </div>

    @if ($members->isEmpty())
        <div class="glass-card text-center py-5">
            <i class="fas fa-users fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">
                @if(!empty($search))
                    Tidak ada hasil pencarian
                @else
                    Belum ada anggota
                @endif
            </h4>
            <p class="text-muted mb-4">
                @if(!empty($search))
                    Coba gunakan kata kunci lain
                @else
                    Mulai tambahkan anggota pertama ke perpustakaan
                @endif
            </p>
            <a href="{{ route('members.create') }}" class="btn btn-primary btn-modern">
                <i class="fas fa-user-plus"></i>
                <span>Tambah Anggota Baru</span>
            </a>
        </div>
    @else
        <div class="glass-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-list"></i>
                    <span>Daftar Anggota ({{ $pagination['totalItems'] }} orang)</span>
                </div>
                <div class="d-flex gap-2">
                    <form method="GET" action="{{ route('members.index') }}" class="d-flex gap-2">
                        <input type="text" name="search" class="form-control" placeholder="Cari nama, NIS, email..." value="{{ $search }}" style="width: 300px;">
                        <button type="submit" class="btn btn-primary btn-modern">
                            <i class="fas fa-search"></i> Cari
                        </button>
                        @if(!empty($search))
                            <a href="{{ route('members.index') }}" class="btn btn-secondary btn-modern">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                        @endif
                    </form>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table modern-table mb-0">
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag"></i> No</th>
                                <th><i class="fas fa-user"></i> Nama Lengkap</th>
                                <th><i class="fas fa-map-marker-alt"></i> Alamat</th>
                                <th><i class="fas fa-phone"></i> No. HP</th>
                                <th><i class="fas fa-cogs"></i> Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($members as $member)
                                <tr data-name="{{ strtolower($member->name) }}" data-created="{{ $member->created_at->timestamp }}">
                                    <td>{{ $pagination['firstItem'] + $loop->index }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-success rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                            <div>
                                                <strong class="text-dark">{{ $member->name }}</strong>
                                                <br>
                                                <small class="text-muted">ID: {{ $member->id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-start">
                                            <i class="fas fa-map-marker-alt text-muted me-2 mt-1"></i>
                                            <span>{{ Str::limit($member->address, 40) }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-phone text-info me-2"></i>
                                            <span>{{ $member->phone }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('members.show', $member) }}" class="btn btn-info btn-sm" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('members.edit', $member) }}" class="btn btn-warning btn-sm" title="Edit Anggota">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('members.destroy', $member) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus Anggota"
                                                        onclick="return confirm('Yakin ingin menghapus anggota \'{{ $member->name }}\'?')">
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
                routeName="members.index"
                :queryParams="['search' => $search]"
            />
        </div>
    @endif

    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const name = row.cells[1].textContent.toLowerCase();
                const address = row.cells[2].textContent.toLowerCase();
                const phone = row.cells[3].textContent.toLowerCase();

                if (name.includes(searchTerm) || address.includes(searchTerm) || phone.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Sort functionality
        function sortMembers(type) {
            const tbody = document.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));

            rows.sort((a, b) => {
                switch(type) {
                    case 'name':
                        return a.getAttribute('data-name').localeCompare(b.getAttribute('data-name'));
                    case 'name-desc':
                        return b.getAttribute('data-name').localeCompare(a.getAttribute('data-name'));
                    case 'newest':
                        return parseInt(b.getAttribute('data-created')) - parseInt(a.getAttribute('data-created'));
                    case 'oldest':
                        return parseInt(a.getAttribute('data-created')) - parseInt(b.getAttribute('data-created'));
                    default:
                        return 0;
                }
            });

            // Re-append sorted rows
            rows.forEach(row => tbody.appendChild(row));

            // Update numbering
            rows.forEach((row, index) => {
                row.cells[0].textContent = index + 1;
            });
        }
    </script>
@endsection
