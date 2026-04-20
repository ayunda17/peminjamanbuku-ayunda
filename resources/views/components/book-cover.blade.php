@props([
    'book' => null,
    'cover' => null,
    'alt' => 'Book Cover',
    'size' => 'medium', // small, medium, large
    'clickable' => false,
    'modal' => false,
    'modalId' => null,
])

@php
    $sizes = [
        'small' => 'width: 50px; height: 70px;',
        'medium' => 'width: 150px; height: 200px; max-width: 100%;',
        'large' => 'width: 300px; height: 400px; max-width: 100%;',
    ];

    // Tentukan filename
    $filename = $cover ?? $book?->cover;
    
    // Primary URL: symlink
    $coverPath = $filename ? asset('storage/covers/' . $filename) : null;
    
    // Fallback URL: direct serve via controller
    $fallbackUrl = $filename ? route('storage.cover', ['filename' => $filename]) : null;
    
    $alt = $alt ?: ($book?->title ?? 'Book Cover');
    $size = $sizes[$size] ?? $sizes['medium'];
@endphp

<div style="{{ $size }} background: #e5e7eb; border-radius: 8px; overflow: hidden; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 2px 8px rgba(0,0,0,0.1);" class="book-cover-container"
     @if($clickable && $modal && $modalId)
        data-bs-toggle="modal" data-bs-target="#{{ $modalId }}" style="cursor: pointer;"
     @endif>
    @if($coverPath)
        <img src="{{ $coverPath }}" 
             alt="{{ $alt }}"
             loading="lazy"
             decoding="async"
             data-fallback="{{ $fallbackUrl }}"
             onerror="handleCoverError(this)"
             style="width: 100%; height: 100%; object-fit: cover; object-position: center; display: block;">
    @else
        <i class="fas fa-book" style="font-size: 2rem; color: #9ca3af;"></i>
    @endif
</div>

<script>
window.handleCoverError = function(img) {
    const fallbackUrl = img.dataset.fallback;
    const tried = img.dataset.fallbackAttempted === 'true';
    
    if (fallbackUrl && !tried) {
        img.dataset.fallbackAttempted = 'true';
        img.src = fallbackUrl;
    } else {
        // Jika semua fallback gagal, tampilkan icon
        img.style.display = 'none';
        const container = img.parentElement;
        container.innerHTML = '<i class="fas fa-book" style="font-size: 2rem; color: #9ca3af;"></i>';
    }
};
</script>
