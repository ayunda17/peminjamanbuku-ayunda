<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Services\PaginationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Contracts\Filesystem\Filesystem;

class BookController extends Controller
{
    /**
     * Menampilkan daftar semua buku dengan paginasi
     */
    public function index(Request $request)
    {
        $page = $request->get('page', 1);
        $search = $request->get('search', '');
        
        // Query buku dengan filter pencarian
        $query = Book::query();
        
        if (!empty($search)) {
            $query->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('publisher', 'like', "%{$search}%");
        }
        
        // Hitung total data
        $totalBooks = $query->count();
        
        // Setup pagination
        $pagination = new PaginationService($page, 10);
        $pagination->setTotal($totalBooks);
        
        // Get data dengan limit dan offset
        $books = $query->offset($pagination->getOffset())
                       ->limit($pagination->getPerPage())
                       ->get();
        
        return view('books.index', [
            'books' => $books,
            'pagination' => $pagination->toArray(),
            'search' => $search,
        ]);
    }

    /**
     * Menampilkan form tambah buku
     */
    public function create()
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return redirect()->route('books.index')->with('error', 'Akses ditolak. Hanya admin yang dapat mengelola buku.');
        }
        return view('books.create');
    }

    /**
     * Menyimpan buku baru ke database
     */
    public function store(Request $request)
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return redirect()->route('books.index')->with('error', 'Akses ditolak. Hanya admin yang dapat menambah buku.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publisher' => 'required|string|max:255',
            'year' => 'required|integer|min:1900|max:2100',
            'stock' => 'required|integer|min:0',
            'genre' => 'required|string|in:"Fiksi / Novel",Romance,Horor,"Misteri / Thriller",Fantasi,"Sains Fiksi",Sejarah,Biografi,"Self-Improvement",Religi,Remaja,Komedi',
            'cover' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Handle cover upload
        $coverName = null;
        if ($request->hasFile('cover')) {
            $coverName = $this->storeCoverImage($request->file('cover'), $validated['title']);
        }

        $validated['cover'] = $coverName;
        Book::create($validated);

        return redirect()->route('books.index')
            ->with('success', 'Buku berhasil ditambahkan!');
    }

    /**
     * Menampilkan detail buku
     */
    public function show(Book $book)
    {
        return view('books.show', compact('book'));
    }

    /**
     * Menampilkan form edit buku
     */
    public function edit(Book $book)
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return redirect()->route('books.index')->with('error', 'Akses ditolak. Hanya admin yang dapat mengedit buku.');
        }
        return view('books.edit', compact('book'));
    }

    /**
     * Update data buku
     */
    public function update(Request $request, Book $book)
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return redirect()->route('books.index')->with('error', 'Akses ditolak. Hanya admin yang dapat mengedit buku.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publisher' => 'required|string|max:255',
            'year' => 'required|integer|min:1900|max:2100',
            'stock' => 'required|integer|min:0',
            'genre' => 'required|string|in:"Fiksi / Novel",Romance,Horor,"Misteri / Thriller",Fantasi,"Sains Fiksi",Sejarah,Biografi,"Self-Improvement",Religi,Remaja,Komedi',
            'cover' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Handle new cover upload & delete old one
        if ($request->hasFile('cover')) {
            // Delete old cover if exists
            if ($book->cover && Storage::disk('public')->exists("covers/{$book->cover}")) {
                Storage::disk('public')->delete("covers/{$book->cover}");
            }

            // Store new cover
            $coverName = $this->storeCoverImage($request->file('cover'), $validated['title']);
            $validated['cover'] = $coverName;
        }

        $book->update($validated);

        return redirect()->route('books.index')
            ->with('success', 'Buku berhasil diperbarui!');
    }

    /**
     * Hapus buku
     */
    public function destroy(Book $book)
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return redirect()->route('books.index')->with('error', 'Akses ditolak. Hanya admin yang dapat menghapus buku.');
        }

        // Cek apakah buku sedang dipinjam
        if ($book->loans()->whereNull('return_date')->exists()) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus buku yang sedang dipinjam!');
        }

        // Delete cover image if exists
        if ($book->cover && Storage::disk('public')->exists("covers/{$book->cover}")) {
            Storage::disk('public')->delete("covers/{$book->cover}");
        }

        $book->delete();

        return redirect()->route('books.index')
            ->with('success', 'Buku berhasil dihapus!');
    }

    /**
     * Simpan cover image ke storage
     * Format nama file: slug-judul-buku_timestamp.extension
     */
    private function storeCoverImage($file, $bookTitle)
    {
        // Buat nama file yang aman
        $filename = Str::slug($bookTitle) . '_' . time() . '.' . $file->getClientOriginalExtension();

        // Simpan file ke storage/app/public/covers/
        $path = $file->storeAs('covers', $filename, 'public');
        
        // Pastikan storage symlink exists
        $this->ensureStorageLink();

        return $filename;
    }
    
    /**
     * Pastikan storage symlink sudah dibuat
     */
    private function ensureStorageLink()
    {
        $link = public_path('storage');
        $target = storage_path('app/public');
        
        // Cek apakah symlink sudah ada
        if (!is_link($link)) {
            try {
                // Windows: gunakan mklink, Unix: gunakan symlink
                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                    // Windows
                    exec("mklink /D \"$link\" \"$target\"");
                } else {
                    // Unix/Linux/Mac
                    symlink($target, $link);
                }
            } catch (\Exception $e) {
                // Silent fail, storage link mungkin sudah ada atau tidak bisa dibuat
            }
        }
    }
}
