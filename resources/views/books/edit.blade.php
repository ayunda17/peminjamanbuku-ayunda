@extends('layouts.app')

@section('title', 'Edit Buku')

@section('content')
    @if(auth()->user()?->role !== 'admin')
        <div class="alert alert-danger">
            <i class="fas fa-lock me-2"></i>
            <strong>Akses Ditolak</strong><br>
            Hanya admin yang dapat mengedit buku. <a href="{{ route('books.index') }}">Kembali ke daftar buku</a>
        </div>
    @else
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mb-0">✏️ Edit Buku</h5>
                    </div>
    @endif
                <div class="card-body">
                    <form action="{{ route('books.update', $book) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="title" class="form-label">Judul Buku <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                                name="title" value="{{ old('title', $book->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="author" class="form-label">Pengarang <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('author') is-invalid @enderror"
                                id="author" name="author" value="{{ old('author', $book->author) }}" required>
                            @error('author')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="publisher" class="form-label">Penerbit <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('publisher') is-invalid @enderror"
                                id="publisher" name="publisher" value="{{ old('publisher', $book->publisher) }}" required>
                            @error('publisher')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="year" class="form-label">Tahun Terbit <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('year') is-invalid @enderror" id="year"
                                name="year" value="{{ old('year', $book->year) }}" min="1900" max="2100" required>
                            @error('year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="stock" class="form-label">Stok <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('stock') is-invalid @enderror" id="stock"
                                name="stock" value="{{ old('stock', $book->stock) }}" min="0" required>
                            @error('stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="genre" class="form-label">Genre <span class="text-danger">*</span></label>
                            <select class="form-control @error('genre') is-invalid @enderror" id="genre"
                                name="genre" required>
                                <option value="">Pilih Genre</option>
                                <option value="Fiksi / Novel" {{ old('genre', $book->genre) == 'Fiksi / Novel' ? 'selected' : '' }}>Fiksi / Novel</option>
                                <option value="Romance" {{ old('genre', $book->genre) == 'Romance' ? 'selected' : '' }}>Romance</option>
                                <option value="Horor" {{ old('genre', $book->genre) == 'Horor' ? 'selected' : '' }}>Horor</option>
                                <option value="Misteri / Thriller" {{ old('genre', $book->genre) == 'Misteri / Thriller' ? 'selected' : '' }}>Misteri / Thriller</option>
                                <option value="Fantasi" {{ old('genre', $book->genre) == 'Fantasi' ? 'selected' : '' }}>Fantasi</option>
                                <option value="Sains Fiksi" {{ old('genre', $book->genre) == 'Sains Fiksi' ? 'selected' : '' }}>Sains Fiksi</option>
                                <option value="Sejarah" {{ old('genre', $book->genre) == 'Sejarah' ? 'selected' : '' }}>Sejarah</option>
                                <option value="Biografi" {{ old('genre', $book->genre) == 'Biografi' ? 'selected' : '' }}>Biografi</option>
                                <option value="Self-Improvement" {{ old('genre', $book->genre) == 'Self-Improvement' ? 'selected' : '' }}>Self-Improvement</option>
                                <option value="Religi" {{ old('genre', $book->genre) == 'Religi' ? 'selected' : '' }}>Religi</option>
                                <option value="Remaja" {{ old('genre', $book->genre) == 'Remaja' ? 'selected' : '' }}>Remaja</option>
                                <option value="Komedi" {{ old('genre', $book->genre) == 'Komedi' ? 'selected' : '' }}>Komedi</option>
                            </select>
                            @error('genre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Cover Image Section -->
                        <div class="mb-3 mt-4 pt-3 border-top">
                            <label for="cover" class="form-label">
                                <i class="fas fa-image"></i>
                                <span>Cover Buku</span>
                                <span class="text-muted">(Opsional)</span>
                            </label>

                            <!-- Current Cover Preview -->
                            @if($book->cover)
                                <div class="mb-3">
                                    <p class="text-muted small"><i class="fas fa-check-circle text-success"></i> Cover saat ini:</p>
                                    <x-book-cover :book="$book" size="medium" />
                                </div>
                            @endif

                            <input type="file" class="form-control @error('cover') is-invalid @enderror" id="cover"
                                name="cover" accept="image/jpeg,image/jpg,image/png"
                                onchange="previewCover(event)">
                            @error('cover')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="d-block mt-2 text-muted">
                                <i class="fas fa-info-circle"></i>
                                Format: JPG, JPEG, PNG | Ukuran maksimal: 2MB
                            </small>

                            <!-- New Cover Preview -->
                            <div id="newCoverPreview" style="margin-top: 15px; display: none;">
                                <p class="text-muted small"><i class="fas fa-upload text-info"></i> Preview baru:</p>
                                <img id="newCoverImage" src="" alt="Preview" 
                                     style="max-width: 150px; max-height: 200px; border-radius: 5px; box-shadow: 0 2px 8px rgba(0,0,0,0.15);">
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-warning">Perbarui</button>
                            <a href="{{ route('books.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

<script>
    // Preview new cover image
    function previewCover(event) {
        const file = event.target.files[0];
        const newCoverPreview = document.getElementById('newCoverPreview');
        const newCoverImage = document.getElementById('newCoverImage');

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                newCoverImage.src = e.target.result;
                newCoverPreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            newCoverPreview.style.display = 'none';
        }
    }
</script>
