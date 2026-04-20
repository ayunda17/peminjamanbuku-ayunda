@extends('layouts.app')

@section('title', 'Detail Buku: ' . $book->title)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">📖 Detail Buku</h2>
            <p class="text-muted mb-0">Informasi lengkap buku "{{ $book->title }}"</p>
        </div>
        <div class="d-flex gap-2">
            @if(auth()->user()?->role === 'admin')
            <a href="{{ route('books.edit', $book) }}" class="btn btn-warning btn-modern">
                <i class="fas fa-edit"></i>
                <span>Edit Buku</span>
            </a>
            @endif
            <a href="{{ route('books.index') }}" class="btn btn-secondary btn-modern">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="glass-card mb-4">
                <div class="card-header">
                    <i class="fas fa-book"></i>
                    <span>Informasi Buku</span>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                    <i class="fas fa-book text-white fa-lg"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1 text-dark">{{ $book->title }}</h5>
                                    <small class="text-muted">Judul Buku</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-success rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                    <i class="fas fa-user text-white fa-lg"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1 text-dark">{{ $book->author }}</h5>
                                    <small class="text-muted">Pengarang</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-info rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                    <i class="fas fa-building text-white fa-lg"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1 text-dark">{{ $book->publisher }}</h5>
                                    <small class="text-muted">Penerbit</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                    <i class="fas fa-calendar text-white fa-lg"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1 text-light">{{ $book->year }}</h5>
                                    <small class="text-muted">Tahun Terbit</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                    <i class="fas fa-boxes text-white fa-lg"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1">
                                        <span class="badge-modern {{ $book->stock > 0 ? 'badge-success' : 'badge-danger' }}">
                                            {{ $book->stock }} {{ $book->stock > 1 ? 'eksemplar' : 'eksemplar' }}
                                        </span>
                                    </h5>
                                    <small class="text-muted">Stok Tersedia</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="glass-card">
                <div class="card-header">
                    <i class="fas fa-clock"></i>
                    <span>Informasi Sistem</span>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="p-3 rounded" style="background: rgba(157, 78, 221, 0.1); border-left: 4px solid var(--accent-purple);">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-plus-circle text-primary me-2"></i>
                                    <small class="text-muted fw-bold">DIBUAT</small>
                                </div>
                                <div class="text-light">{{ $book->created_at->format('d M Y') }}</div>
                                <small class="text-muted">{{ $book->created_at->format('H:i:s') }}</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 rounded" style="background: rgba(14, 165, 233, 0.1); border-left: 4px solid var(--accent-blue);">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-edit text-info me-2"></i>
                                    <small class="text-muted fw-bold">DIPERBARUI</small>
                                </div>
                                <div class="text-light">{{ $book->updated_at->format('d M Y') }}</div>
                                <small class="text-muted">{{ $book->updated_at->format('H:i:s') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="glass-card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-bar"></i>
                    <span>Status & Statistik</span>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="display-4 fw-bold mb-2" style="color: var(--accent-cyan);">
                            {{ $book->stock }}
                        </div>
                        <small class="text-muted">Eksemplar Tersedia</small>
                    </div>

                    <div class="progress mb-3" style="height: 8px;">
                        <div class="progress-bar {{ $book->stock > 5 ? 'bg-success' : ($book->stock > 0 ? 'bg-warning' : 'bg-danger') }}"
                             role="progressbar"
                             style="width: {{ $book->stock > 0 ? min(100, ($book->stock / 10) * 100) : 0 }}%"
                             aria-valuenow="{{ $book->stock }}"
                             aria-valuemin="0"
                             aria-valuemax="10">
                        </div>
                    </div>

                    <div class="text-center">
                        <small class="text-muted">
                            @if($book->stock > 5)
                                <i class="fas fa-check-circle text-success"></i> Stok Melimpah
                            @elseif($book->stock > 0)
                                <i class="fas fa-exclamation-triangle text-warning"></i> Stok Terbatas
                            @else
                                <i class="fas fa-times-circle text-danger"></i> Stok Habis
                            @endif
                        </small>
                    </div>
                </div>
            </div>

            <div class="glass-card">
                <div class="card-header">
                    <i class="fas fa-cogs"></i>
                    <span>Aksi Cepat</span>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if(auth()->user()?->role === 'admin')
                            <a href="{{ route('books.edit', $book) }}" class="btn btn-warning btn-modern">
                                <i class="fas fa-edit"></i>
                                <span>Edit Buku</span>
                            </a>
                        @endif

                        <button class="btn btn-info btn-modern" onclick="copyBookInfo()">
                            <i class="fas fa-copy"></i>
                            <span>Salin Info</span>
                        </button>

                        @if(auth()->user()?->role === 'admin')
                        <form action="{{ route('books.destroy', $book) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-modern w-100"
                                    onclick="return confirm('Yakin ingin menghapus buku \'{{ $book->title }}\'?')">
                                <i class="fas fa-trash"></i>
                                <span>Hapus Buku</span>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyBookInfo() {
            const bookInfo = `Judul: ${"{{ $book->title }}"}}\nPengarang: ${"{{ $book->author }}"}}\nPenerbit: ${"{{ $book->publisher }}"}}\nTahun: ${"{{ $book->year }}"}}\nStok: ${"{{ $book->stock }}"}}\n\nDari Sistem Peminjaman Buku`;

            navigator.clipboard.writeText(bookInfo).then(function() {
                // Show success message
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-modern alert-success position-fixed';
                alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
                alertDiv.innerHTML = '<i class="fas fa-check-circle"></i> <span>Informasi buku berhasil disalin!</span>';

                document.body.appendChild(alertDiv);

                setTimeout(() => {
                    alertDiv.remove();
                }, 3000);
            }).catch(function(err) {
                console.error('Failed to copy: ', err);
            });
        }
    </script>
@endsection
