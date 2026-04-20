<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Book;
use App\Models\Member;
use App\Models\PenanggungJawab;
use App\Services\PaginationService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LoanController extends Controller
{
    /**
     * Menampilkan daftar semua peminjaman dengan paginasi
     */
    public function index(Request $request)
    {
        $page = $request->get('page', 1);
        $search = $request->get('search', '');
        $status = $request->get('status', '');
        
        // Query peminjaman dengan relasi
        $query = Loan::with('book', 'member', 'penanggungJawab');
        
        // Filter 
        if (!empty($search)) {
            $query->whereHas('book', function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            })
            ->orWhereHas('member', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }
        
        // Filter status peminjaman
        if ($status === 'active') {
            $query->whereNull('return_date');
        } elseif ($status === 'returned') {
            $query->whereNotNull('return_date');
        } elseif ($status === 'overdue') {
            $query->whereNull('return_date')
                  ->whereDate('due_date', '<', Carbon::now());
        }
        
        // Hitung total data
        $totalLoans = $query->count();
        
        // Setup pagination
        $pagination = new PaginationService($page, 10);
        $pagination->setTotal($totalLoans);
        
        // Get data dengan limit dan offset
        $loans = $query->orderBy('loan_date', 'desc')
                       ->offset($pagination->getOffset())
                       ->limit($pagination->getPerPage())
                       ->get();
        
        return view('loans.index', [
            'loans' => $loans,
            'pagination' => $pagination->toArray(),
            'search' => $search,
            'status' => $status,
        ]);
    }

    /**
     * Menampilkan form peminjaman buku baru
     */
    public function create()
    {
        $books = Book::where('stock', '>', 0)->get();
        $members = Member::all();
        $penanggungJawabs = PenanggungJawab::where('is_active', true)->get();
        return view('loans.create', compact('books', 'members', 'penanggungJawabs'));
    }

    /**
     * Menyimpan peminjaman baru
     */
    public function store(Request $request)
    {
        $today = date('Y-m-d');
        
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'member_id' => 'required|exists:members,id',
            'penanggung_jawab_id' => 'required|exists:penanggung_jawabs,id',
            'loan_date' => [
                'required',
                'date',
                'date_format:Y-m-d',
                function ($attribute, $value, $fail) use ($today) {
                    if ($value < $today) {
                        $fail('Tanggal peminjaman tidak boleh kurang dari hari ini.');
                    }
                },
            ],
            'due_date' => 'required|date|after:loan_date',
        ]);

        // Cek stok buku
        $book = Book::find($validated['book_id']);
        if ($book->stock <= 0) {
            return redirect()->back()
                ->with('error', 'Stok buku tidak tersedia!');
        }

        // Buat record peminjaman
        Loan::create($validated);

        // Kurangi stok buku
        $book->decrement('stock');

        return redirect()->route('loans.index')
            ->with('success', 'Peminjaman berhasil dicatat!');
    }

    /**
     * Menampilkan detail peminjaman
     */
    public function show(Loan $loan)
    {
        return view('loans.show', compact('loan'));
    }

    /**
     * Form pengembalian buku
     */
    public function returnForm(Loan $loan)
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('loans.index')->with('error', 'Hanya admin yang dapat mengembalikan buku.');
        }

        if (!$loan->isActive()) {
            return redirect()->route('loans.index')
                ->with('error', 'Buku sudah dikembalikan!');
        }
        return view('loans.return', compact('loan'));
    }

    /**
     * Proses pengembalian buku
     */
    public function processReturn(Request $request, Loan $loan)
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('loans.index')->with('error', 'Hanya admin yang dapat mengembalikan buku.');
        }

        $validated = $request->validate([
            'return_date' => 'required|date',
        ]);

        // Hitung denda jika terlambat
        $returnDateStr = $validated['return_date'];
        $dueDateStr = $loan->due_date->format('Y-m-d');

        $fine = 0;

        // Bandingkan tanggal menggunakan strtotime
        $returnTimestamp = strtotime($returnDateStr);
        $dueTimestamp = strtotime($dueDateStr);

        if ($returnTimestamp > $dueTimestamp) {
            // Hitung selisih hari
            $diffSeconds = $returnTimestamp - $dueTimestamp;
            $lateDays = floor($diffSeconds / (60 * 60 * 24));
            $fine = max(0, $lateDays * 2000); // Rp 2.000 per hari, pastikan tidak negatif
        }

        // Update peminjaman
        $loan->update([
            'return_date' => $validated['return_date'],
            'fine' => $fine,
        ]);

        // Tambah stok buku kembali
        $loan->book->increment('stock');

        return redirect()->route('loans.index')
            ->with('success', 'Pengembalian buku berhasil dicatat!' . ($fine > 0 ? ' Denda: Rp ' . number_format($fine) : ''));
    }

    /**
     * Hapus record peminjaman
     */
    public function destroy(Loan $loan)
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('loans.index')->with('error', 'Hanya admin yang dapat menghapus record peminjaman.');
        }

        // Jika belum dikembalikan, kembalikan stoknya
        if ($loan->isActive()) {
            $loan->book->increment('stock');
        }

        $loan->delete();

        return redirect()->route('loans.index')
            ->with('success', 'Record peminjaman berhasil dihapus!');
    }
    public function confirmReturn(Loan $loan)
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('loans.index')->with('error', 'Hanya admin yang dapat mengembalikan buku.');
        }

        if (!$loan->isActive()) {
            return redirect()->route('loans.index')
                ->with('error', 'Buku sudah dikembalikan!');
        }

        // Hitung denda jika terlambat (dengan tanggal hari ini)
        $returnDateStr = date('Y-m-d'); // Tanggal hari ini
        $dueDateStr = $loan->due_date->format('Y-m-d'); // Format Y-m-d

        $lateDays = 0;
        $fine = 0;

        // Bandingkan tanggal menggunakan strtotime
        $returnTimestamp = strtotime($returnDateStr);
        $dueTimestamp = strtotime($dueDateStr);

        if ($returnTimestamp > $dueTimestamp) {
            // Hitung selisih hari
            $diffSeconds = $returnTimestamp - $dueTimestamp;
            $lateDays = floor($diffSeconds / (60 * 60 * 24)); // Konversi ke hari
            $fine = $lateDays * 2000; // Rp 2.000 per hari
        }

        return view('loans.confirm-return', compact('loan', 'lateDays', 'fine'));
    }

    /**
     * Proses konfirmasi pengembalian buku
     */
    public function processConfirmReturn(Request $request, Loan $loan)
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('loans.index')->with('error', 'Hanya admin yang dapat mengembalikan buku.');
        }

        if (!$loan->isActive()) {
            return redirect()->route('loans.index')
                ->with('error', 'Buku sudah dikembalikan!');
        }

        // Hitung denda dengan tanggal hari ini
        $returnDateStr = date('Y-m-d'); // Tanggal hari ini
        $dueDateStr = $loan->due_date->format('Y-m-d'); // Format Y-m-d

        $fine = 0;

        // Bandingkan tanggal menggunakan strtotime
        $returnTimestamp = strtotime($returnDateStr);
        $dueTimestamp = strtotime($dueDateStr);

        if ($returnTimestamp > $dueTimestamp) {
            // Hitung selisih hari
            $diffSeconds = $returnTimestamp - $dueTimestamp;
            $lateDays = floor($diffSeconds / (60 * 60 * 24)); // Konversi ke hari
            $fine = max(0, $lateDays * 2000); // Pastikan tidak negatif
        }

        // Update peminjaman
        $loan->update([
            'return_date' => date('Y-m-d'),
            'fine' => $fine,
        ]);

        // Tambah stok buku kembali
        $loan->book->increment('stock');

        return redirect()->route('loans.index')
            ->with('success', 'Pengembalian buku berhasil dicatat!' . ($fine > 0 ? ' Denda: Rp ' . number_format($fine) : ''));
    }
}
