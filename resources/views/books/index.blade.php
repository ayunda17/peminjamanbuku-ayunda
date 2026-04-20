@extends('layouts.app')

@section('title', 'Kelola Buku')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">📚 Kelola Buku</h2>
            <p class="text-muted mb-0">Kelola koleksi buku perpustakaan</p>
        </div>
        @if(auth()->user()?->role === 'admin')
            <a href="{{ route('books.create') }}" class="btn btn-primary btn-modern">
            <i class="fas fa-plus"></i>
            <span>Tambah Buku</span>
        </a>
        @endif
    </div>

    @if ($books->isEmpty())
        <div class="glass-card text-center py-5">
            <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">
                @if(!empty($search))
                    Tidak ada hasil pencarian
                @else
                    Belum ada buku
                @endif
            </h4>
            <p class="text-muted mb-4">
                @if(!empty($search))
                    Coba gunakan kata kunci lain
                @else
                    Mulai tambahkan buku pertama ke perpustakaan
                @endif
            </p>
            @if(auth()->user()?->role === 'admin')
                <a href="{{ route('books.create') }}" class="btn btn-primary btn-modern">
                <i class="fas fa-plus"></i>
                <span>Tambah Buku Baru</span>
            </a>
            @endif
        </div>
    @else
        <div class="glass-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-list"></i>
                    <span>Daftar Buku ({{ $pagination['totalItems'] }} buku)</span>
                </div>
                <div class="d-flex gap-2">
                    <form method="GET" action="{{ route('books.index') }}" class="d-flex gap-2">
                        <input type="text" name="search" class="form-control" placeholder="Cari judul, pengarang, penerbit..." value="{{ $search }}" style="width: 300px;">
                        <button type="submit" class="btn btn-primary btn-modern">
                            <i class="fas fa-search"></i> Cari
                        </button>
                        @if(!empty($search))
                            <a href="{{ route('books.index') }}" class="btn btn-secondary btn-modern">
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
                                <th><i class="fas fa-image"></i> Cover</th>
                                <th><i class="fas fa-hashtag"></i> No</th>
                                <th><i class="fas fa-book"></i> Judul</th>
                                <th><i class="fas fa-user"></i> Pengarang</th>
                                <th><i class="fas fa-building"></i> Penerbit</th>
                                <th><i class="fas fa-calendar"></i> Tahun</th>
                                <th><i class="fas fa-tags"></i> Genre</th>
                                <th><i class="fas fa-copy"></i> Stok</th>
                                <th><i class="fas fa-cogs"></i> Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($books as $book)
                                <tr data-stock="{{ $book->stock }}" data-genre="{{ $book->genre }}">
                                    <!-- Cover Thumbnail -->
                                    <td>
                                        <x-book-cover :book="$book" size="small" clickable modal modal-id="coverModal{{ $book->id }}" />
                                    </td>
                                    <td>{{ $pagination['firstItem'] + $loop->index }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <strong class="text-dark">{{ $book->title }}</strong>
                                                <br>
                                                <small class="text-muted">{{ Str::limit($book->author, 30) }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $book->author }}</td>
                                    <td>{{ $book->publisher }}</td>
                                    <td>{{ $book->year }}</td>
                                    <td>{{ $book->genre }}</td>
                                    <td>
                                        <span class="badge-modern {{ $book->stock > 0 ? 'badge-success' : 'badge-danger' }}">
                                            {{ $book->stock }}
                                        </span>
                                    </td>
                                    <td>
                                        @if(auth()->user()?->role === 'admin')
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('books.show', $book) }}" class="btn btn-info btn-sm" title="Lihat Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('books.edit', $book) }}" class="btn btn-warning btn-sm" title="Edit Buku">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('books.destroy', $book) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus Buku" onclick="return confirm('Apakah Anda yakin ingin menghapus buku ini?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
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
                routeName="books.index"
                :queryParams="['search' => $search]"
            />
        </div>
    @endif

    <!-- Cover Preview Modals -->
    @foreach($books as $book)
        <div class="modal fade" id="coverModal{{ $book->id }}" tabindex="-1" aria-labelledby="coverModalLabel{{ $book->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content" style="background-color: #f8f6f1; border: none;">
                    <div class="modal-header" style="border-bottom: 2px solid #1e3a5f; background-color: #f8f6f1;">
                        <h5 class="modal-title" id="coverModalLabel{{ $book->id }}" style="color: #1e3a5f;">
                            <i class="fas fa-image"></i> Cover Buku: {{ $book->title }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center" style="padding: 2rem;">
                        <x-book-cover :book="$book" size="large" />
                    </div>
                    <div class="modal-footer" style="border-top: 2px solid #1e3a5f; background-color: #f8f6f1;">
                        <button type="button" class="btn" style="background-color: #1e3a5f; color: white;" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <script>
        // Ensure DOM is ready before adding event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Search functionality
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.addEventListener('keyup', function() {
                    const searchTerm = this.value.toLowerCase();
                    const rows = document.querySelectorAll('tbody tr');

                    rows.forEach(row => {
                        const title = row.cells[2].textContent.toLowerCase();  // Updated: now at index 2 instead of 1
                        const author = row.cells[3].textContent.toLowerCase();  // Updated: now at index 3 instead of 2
                        const publisher = row.cells[4].textContent.toLowerCase();  // Updated: now at index 4 instead of 3

                        if (title.includes(searchTerm) || author.includes(searchTerm) || publisher.includes(searchTerm)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            }
        });

        // Filter functionality
        function filterBooks(type) {
            const rows = document.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const stock = parseInt(row.getAttribute('data-stock'));

                switch(type) {
                    case 'available':
                        row.style.display = stock > 0 ? '' : 'none';
                        break;
                    case 'out':
                        row.style.display = stock === 0 ? '' : 'none';
                        break;
                    default:
                        row.style.display = '';
                }
            });
        }

        // Genre filter functionality
        function filterGenre(genre) {
            const rows = document.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const bookGenre = row.getAttribute('data-genre');

                if (bookGenre === genre) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>
@endsection
