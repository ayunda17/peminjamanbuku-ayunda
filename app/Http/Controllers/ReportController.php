<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Halaman laporan peminjaman
     */
    public function index()
    {
        $startDate = request('start_date');
        $endDate = request('end_date');

        $loans = Loan::query()
            ->with('book', 'member', 'penanggungJawab')
            ->when($startDate, function ($query) use ($startDate) {
                return $query->whereDate('loan_date', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                return $query->whereDate('loan_date', '<=', $endDate);
            })
            ->orderBy('loan_date', 'desc')
            ->get();

        // Hitung total denda dari loans yang difilter
        $totalFine = $loans->sum('fine');

        return view('reports.loans-index', compact('loans', 'startDate', 'endDate', 'totalFine'));
    }

    /**
     * Export laporan ke PDF
     */
    public function exportPdf()
    {
        $startDate = request('start_date');
        $endDate = request('end_date');

        $loans = Loan::query()
            ->with('book', 'member', 'penanggungJawab')
            ->when($startDate, function ($query) use ($startDate) {
                return $query->whereDate('loan_date', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                return $query->whereDate('loan_date', '<=', $endDate);
            })
            ->orderBy('loan_date', 'desc')
            ->get();

        // Hitung total denda dari loans yang difilter
        $totalFine = $loans->sum('fine');

        $data = [
            'loans' => $loans,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalFine' => $totalFine,
            'generatedAt' => Carbon::now(),
        ];

        $pdf = Pdf::loadView('reports.pdf-loans', $data);
        $pdf->setPaper('a4', 'landscape');

        $filename = 'Laporan-Peminjaman-' . Carbon::now()->format('d-m-Y-His') . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Export laporan ke Excel
     */
    public function exportExcel()
    {
        $startDate = request('start_date');
        $endDate = request('end_date');

        $loans = Loan::query()
            ->with('book', 'member', 'penanggungJawab')
            ->when($startDate, function ($query) use ($startDate) {
                return $query->whereDate('loan_date', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                return $query->whereDate('loan_date', '<=', $endDate);
            })
            ->orderBy('loan_date', 'desc')
            ->get();

        // Export ke CSV (sederhana tanpa library tambahan)
        $filename = 'Laporan-Peminjaman-' . Carbon::now()->format('d-m-Y-His') . '.csv';

        $headers = array(
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0",
        );

        $columns = ['No', 'Buku', 'Author', 'Anggota', 'Penanggung Jawab', 'Tgl Pinjam', 'Batas Kembali', 'Tgl Kembali', 'Status', 'Denda'];

        $callback = function () use ($loans, $columns) {
            $file = fopen('php://output', 'w');

            // UTF-8 BOM untuk Excel
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header
            fputcsv($file, $columns, ';');

            // Data
            $no = 1;
            foreach ($loans as $loan) {
                $row = [
                    $no++,
                    $loan->book->title,
                    $loan->book->author ?? '-',
                    $loan->member->name,
                    $loan->penanggungJawab?->nama ?? '-',
                    $loan->loan_date->format('d/m/Y'),
                    $loan->due_date->format('d/m/Y'),
                    $loan->return_date ? $loan->return_date->format('d/m/Y') : '-',
                    $loan->isActive() ? ($loan->isOverdue() ? 'Terlambat' : 'Aktif') : 'Dikembalikan',
                    $loan->fine > 0 ? 'Rp ' . number_format($loan->fine, 0, '.', '.') : '-',
                ];
                fputcsv($file, $row, ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export ke HTML (untuk print/preview)
     */
    public function exportHtml()
    {
        $startDate = request('start_date');
        $endDate = request('end_date');

        $loans = Loan::query()
            ->with('book', 'member', 'penanggungJawab')
            ->when($startDate, function ($query) use ($startDate) {
                return $query->whereDate('loan_date', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                return $query->whereDate('loan_date', '<=', $endDate);
            })
            ->orderBy('loan_date', 'desc')
            ->get();

        // Hitung total denda dari loans yang difilter
        $totalFine = $loans->sum('fine');

        return view('reports.html-loans', compact('loans', 'startDate', 'endDate', 'totalFine'));
    }

    /**
     * Halaman statistik perpustakaan
     */
    public function statistics()
    {
        $year = request('year', date('Y'));

        // Top 5 buku paling sering dipinjam
        $topBooks = DB::table('loans')
            ->join('books', 'loans.book_id', '=', 'books.id')
            ->select('books.title', DB::raw('COUNT(loans.id) as loan_count'))
            ->whereYear('loans.loan_date', $year)
            ->groupBy('books.id', 'books.title')
            ->orderBy('loan_count', 'desc')
            ->limit(5)
            ->get();

        // Top 5 anggota paling aktif
        $topMembers = DB::table('loans')
            ->join('members', 'loans.member_id', '=', 'members.id')
            ->select('members.name', DB::raw('COUNT(loans.id) as loan_count'))
            ->whereYear('loans.loan_date', $year)
            ->groupBy('members.id', 'members.name')
            ->orderBy('loan_count', 'desc')
            ->limit(5)
            ->get();

        // Peminjaman per bulan dalam 1 tahun
        $monthlyLoans = DB::table('loans')
            ->select(DB::raw("strftime('%m', loan_date) as month"), DB::raw('COUNT(*) as count'))
            ->whereYear('loan_date', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // Isi bulan yang kosong dengan 0
        $monthlyData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthStr = str_pad($i, 2, '0', STR_PAD_LEFT);
            $monthlyData[$i] = $monthlyLoans[$monthStr] ?? 0;
        }

        // Rata-rata durasi peminjaman (dalam hari)
        $avgDuration = DB::table('loans')
            ->select(DB::raw("AVG(julianday(return_date) - julianday(loan_date)) as avg_duration"))
            ->whereNotNull('return_date')
            ->whereYear('loan_date', $year)
            ->first();

        // Total denda yang terkumpul (hanya denda positif)
        $totalFine = DB::table('loans')
            ->whereYear('loan_date', $year)
            ->where('fine', '>', 0)
            ->sum('fine');

        // Daftar tahun untuk dropdown
        $years = DB::table('loans')
            ->select(DB::raw("DISTINCT strftime('%Y', loan_date) as year"))
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        return view('reports.statistics', compact(
            'topBooks',
            'topMembers',
            'monthlyData',
            'avgDuration',
            'totalFine',
            'year',
            'years'
        ));
    }
}
