@extends('layouts.app')

@section('title', 'Tambah Buku')

@section('content')
    @if(auth()->user()?->role !== 'admin')
        <div class="alert alert-danger">
            <i class="fas fa-lock me-2"></i>
            <strong>Akses Ditolak</strong><br>
            Hanya admin yang dapat menambah buku. <a href="{{ route('books.index') }}">Kembali ke daftar buku</a>
        </div>
    @else
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">📚 Tambah Buku Baru</h2>
                <p class="text-muted mb-0">Tambahkan buku baru ke koleksi perpustakaan</p>
            </div>
            <a href="{{ route('books.index') }}" class="btn btn-secondary btn-modern">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="glass-card">
                    <div class="card-header">
                        <i class="fas fa-plus-circle"></i>
                        <span>Form Tambah Buku</span>
                    </div>
    @endif
                <div class="card-body">
                    <form action="{{ route('books.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="title" class="form-label">
                                        <i class="fas fa-book"></i>
                                        <span>Judul Buku</span>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                                        name="title" value="{{ old('title') }}" required
                                        placeholder="Masukkan judul buku">
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="author" class="form-label">
                                        <i class="fas fa-user"></i>
                                        <span>Pengarang</span>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('author') is-invalid @enderror" id="author"
                                        name="author" value="{{ old('author') }}" required
                                        placeholder="Masukkan nama pengarang">
                                    @error('author')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="publisher" class="form-label">
                                        <i class="fas fa-building"></i>
                                        <span>Penerbit</span>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('publisher') is-invalid @enderror"
                                        id="publisher" name="publisher" value="{{ old('publisher') }}" required
                                        placeholder="Masukkan nama penerbit">
                                    @error('publisher')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="year" class="form-label">
                                        <i class="fas fa-calendar"></i>
                                        <span>Tahun Terbit</span>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control @error('year') is-invalid @enderror" id="year"
                                        name="year" value="{{ old('year') }}" min="1900" max="2100" required
                                        placeholder="2024">
                                    @error('year')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="stock" class="form-label">
                                        <i class="fas fa-boxes"></i>
                                        <span>Jumlah Stok</span>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control @error('stock') is-invalid @enderror" id="stock"
                                        name="stock" value="{{ old('stock') }}" min="0" required
                                        placeholder="0">
                                    @error('stock')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Masukkan jumlah eksemplar buku yang tersedia</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="genre" class="form-label">
                                        <i class="fas fa-tags"></i>
                                        <span>Genre</span>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control @error('genre') is-invalid @enderror" id="genre"
                                        name="genre" required>
                                        <option value="">Pilih Genre</option>
                                        <option value="Fiksi / Novel" {{ old('genre') == 'Fiksi / Novel' ? 'selected' : '' }}>Fiksi / Novel</option>
                                        <option value="Romance" {{ old('genre') == 'Romance' ? 'selected' : '' }}>Romance</option>
                                        <option value="Horor" {{ old('genre') == 'Horor' ? 'selected' : '' }}>Horor</option>
                                        <option value="Misteri / Thriller" {{ old('genre') == 'Misteri / Thriller' ? 'selected' : '' }}>Misteri / Thriller</option>
                                        <option value="Fantasi" {{ old('genre') == 'Fantasi' ? 'selected' : '' }}>Fantasi</option>
                                        <option value="Sains Fiksi" {{ old('genre') == 'Sains Fiksi' ? 'selected' : '' }}>Sains Fiksi</option>
                                        <option value="Sejarah" {{ old('genre') == 'Sejarah' ? 'selected' : '' }}>Sejarah</option>
                                        <option value="Biografi" {{ old('genre') == 'Biografi' ? 'selected' : '' }}>Biografi</option>
                                        <option value="Self-Improvement" {{ old('genre') == 'Self-Improvement' ? 'selected' : '' }}>Self-Improvement</option>
                                        <option value="Religi" {{ old('genre') == 'Religi' ? 'selected' : '' }}>Religi</option>
                                        <option value="Remaja" {{ old('genre') == 'Remaja' ? 'selected' : '' }}>Remaja</option>
                                        <option value="Komedi" {{ old('genre') == 'Komedi' ? 'selected' : '' }}>Komedi</option>
                                    </select>
                                    @error('genre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Cover Image Section -->
                        <div class="row mt-5 pt-4 border-top border-secondary">
                            <div class="col-12">
                                <h5 class="mb-3">
                                    <i class="fas fa-image"></i>
                                    <span>Cover Buku</span>
                                </h5>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="cover" class="form-label">
                                        <i class="fas fa-upload"></i>
                                        <span>Unggah Cover Buku</span>
                                        <span class="text-muted">(Opsional)</span>
                                    </label>
                                    <input type="file" class="form-control @error('cover') is-invalid @enderror" id="cover"
                                        name="cover" accept="image/jpeg,image/jpg,image/png"
                                        onchange="previewCover(event)">
                                    @error('cover')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="d-block mt-2 text-muted">
                                        <i class="fas fa-info-circle"></i>
                                        Format: JPG, JPEG, PNG | Ukuran maksimal: 2MB
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label>&nbsp;</label>
                                <div id="coverPreviewContainer" style="display: none;">
                                    <img id="coverPreview" src="" alt="Preview" style="max-width: 200px; max-height: 300px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                </div>
                                <div id="noPreviewMessage" class="alert alert-light border-2 border-secondary text-center" style="padding: 40px;">
                                    <i class="fas fa-image" style="font-size: 3rem; color: #ccc;"></i>
                                    <p class="text-muted mt-2 mb-0">Preview cover akan tampil di sini</p>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-3 justify-content-end pt-4 border-top border-secondary mt-5">
                            <a href="{{ route('books.index') }}" class="btn btn-secondary btn-modern">
                                <i class="fas fa-times"></i>
                                <span>Batal</span>
                            </a>
                            <button type="submit" class="btn btn-primary btn-modern">
                                <i class="fas fa-save"></i>
                                <span>Simpan Buku</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-focus on first input
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('title').focus();
        });

        // Form validation enhancement
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('input[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            if (!isValid) {
                e.preventDefault();
                // Show error message
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-modern alert-danger mt-3';
                alertDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> <span>Mohon lengkapi semua field yang wajib diisi.</span>';
                form.insertBefore(alertDiv, form.firstChild);

                setTimeout(() => {
                    alertDiv.remove();
                }, 5000);
            }
        });

        // Preview cover image
        function previewCover(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('coverPreview');
            const previewContainer = document.getElementById('coverPreviewContainer');
            const noPreviewMessage = document.getElementById('noPreviewMessage');

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    previewContainer.style.display = 'block';
                    noPreviewMessage.style.display = 'none';
                };
                reader.readAsDataURL(file);
            } else {
                previewContainer.style.display = 'none';
                noPreviewMessage.style.display = 'block';
            }
        }
    </script>
@endsection
